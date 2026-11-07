<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

// Jalankan sekali: php artisan menu:download-images
class DownloadMenuImages extends Command
{
    protected $signature = 'menu:download-images';

    protected $description = 'Unduh gambar menu dari URL eksternal ke folder public/assets/menu dan perbarui database';

    public function handle(): int
    {
        $dir = public_path('assets/menu');
        File::ensureDirectoryExists($dir);

        $products = Product::where('img', 'like', 'http://%')
            ->orWhere('img', 'like', 'https://%')
            ->get();

        if ($products->isEmpty()) {
            $this->info('Semua gambar menu sudah berupa file lokal.');
            return self::SUCCESS;
        }

        $ok = 0;
        $failed = [];

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            try {
                $res = Http::timeout(30)
                    ->retry(2, 500)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($product->img);

                $type = (string) $res->header('Content-Type');

                if (! $res->successful() || ! str_starts_with($type, 'image/')) {
                    throw new \RuntimeException('respon bukan gambar (HTTP '.$res->status().')');
                }

                $ext = str_contains($type, 'png') ? 'png' : (str_contains($type, 'webp') ? 'webp' : 'jpg');
                $file = $product->slug.'.'.$ext;

                File::put($dir.DIRECTORY_SEPARATOR.$file, $res->body());
                $product->update(['img' => 'assets/menu/'.$file]);
                $ok++;
            } catch (\Throwable $e) {
                $failed[] = $product->name.': '.$e->getMessage();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Berhasil: {$ok} gambar tersimpan di public/assets/menu");

        if ($failed) {
            $this->warn('Gagal: '.count($failed).' gambar (tetap memakai URL lama)');
            foreach (array_slice($failed, 0, 3) as $f) {
                $this->line(' - '.$f);
            }
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
