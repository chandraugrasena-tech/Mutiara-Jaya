# Mutiara Jaya (Laravel 13 + MySQL + REST API)

Toko makanan dan minuman dengan pesan antar. Pengunjung bisa melihat menu. Untuk memakai promo, memasukkan menu ke
keranjang, mengisi lokasi, dan memesan, pengguna wajib login. Admin punya dashboard, CRUD, pesanan, pemasukan,
stok, dan keterangan toko.

## Cara menjalankan (Terminal Laragon)

1. Klik **Start All** di Laragon (Apache dan MySQL menyala).
2. Ekstrak zip ke folder proyekmu (timpa file yang ada). Folder `vendor` dan `node_modules` yang sudah ada tidak perlu diganti.
3. Buka Terminal Laragon, masuk ke folder proyek:
   ```
   cd C:\laragon\www\mutiara-jaya
   ```
4. Pastikan database `mutiara_jaya` ada (kalau belum):
   ```
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS mutiara_jaya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
5. Buat ulang tabel dan isi data contoh (perintah ini menghapus semua tabel lama di database `mutiara_jaya`):
   ```
   php artisan migrate:fresh --seed
   ```
6. Jalankan server:
   ```
   php artisan serve
   ```
7. Buka http://localhost:8000

`npm run dev` tidak diperlukan lagi karena tampilan memakai Tailwind lewat CDN (butuh internet).
Kalau ada foto menu yang tidak muncul, simpan semuanya ke folder proyek dengan: `php artisan menu:download-images`

## Akun contoh

| Peran | Email | Password |
|---|---|---|
| Admin | admin@mutiarajaya.id | admin123 |
| Pengguna | pelanggan@mutiarajaya.id | password |

## Aturan login

- Tanpa login: boleh melihat menu, detail menu, dan halaman Tentang.
- Wajib login: Promo, Keranjang, Lokasi (alamat), checkout, dan riwayat pesanan.
- Menekan tombol Tambah tanpa login mengarahkan ke halaman masuk.

## Fitur pengguna

Masukkan menu ke keranjang, atur jumlah, pakai kode promo, simpan lokasi sendiri (alamat tertulis, boleh ditambah titik
koordinat lewat tombol "Gunakan lokasi saya"), pilih CASH atau QRIS, dan pantau status pesanan.

## Fitur admin (/admin)

| Menu | Isi |
|---|---|
| Dashboard | Statistik, pemasukan 7 hari, status pesanan, menu terlaris, stok menipis |
| Pesanan | Daftar, filter, detail (termasuk titik lokasi), ubah status. Pembatalan mengembalikan stok |
| Pemasukan | Rekap per periode dan per hari, CASH dan QRIS, menu terlaris. Hanya pesanan lunas yang dihitung |
| Stok Barang | Lihat stok, tambah, kurangi, catatan, dan riwayat perubahan |
| Menu, Kategori, Promo | CRUD lengkap (menu dengan upload foto, cari, filter) |
| Keterangan Toko | Nama, slogan, deskripsi, alamat, jam buka, kontak, area antar, ongkir, batas stok menipis |

## Pengujian otomatis

```
php artisan test
```
Memeriksa login wajib, keranjang, promo, lokasi, checkout, stok, pemasukan, admin, dan REST API.

## REST API

Import `postman/MutiaraJaya.postman_collection.json` ke Postman. Daftar endpoint: `php artisan route:list --path=api`.
Route API nilai mahasiswa yang sudah ada di proyek tetap dipertahankan.
