<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MutiaraJayaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@mutiarajaya.id')->first();
    }

    private function customer(): User
    {
        return User::where('email', 'pelanggan@mutiarajaya.id')->first();
    }

    public function test_pengunjung_boleh_lihat_menu_tapi_promo_dan_keranjang_wajib_login(): void
    {
        $this->get('/')->assertOk()->assertSee('Daftar menu');
        $this->get('/tentang')->assertOk();
        $this->get('/product/1')->assertOk();

        $this->get('/promo')->assertRedirect('/login');
        $this->get('/cart')->assertRedirect('/login');
        $this->get('/addresses')->assertRedirect('/login');
        $this->post('/cart/1')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');

        // halaman login menjelaskan kenapa harus masuk
        $this->get('/login?need=1')->assertOk()->assertSee('Silakan masuk dulu');
    }

    public function test_login_register_dan_logout(): void
    {
        $this->post('/register', [
            'name' => 'Budi', 'email' => 'budi@example.com', 'password' => 'rahasia1', 'password_confirmation' => 'rahasia1',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', ['email' => 'budi@example.com', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'admin@mutiarajaya.id', 'password' => 'admin123'])->assertRedirect(route('admin.dashboard'));
    }

    public function test_pelanggan_keranjang_promo_lokasi_dan_checkout(): void
    {
        $user = $this->customer();
        $this->actingAs($user);

        $this->get('/')->assertOk();
        $this->get('/promo')->assertOk()->assertSee('ONGKIR10');

        $product = Product::first();
        $stockBefore = $product->stock;

        $this->post("/cart/{$product->product_id}", ['qty' => 3])->assertSessionHas('success');
        $this->get('/cart')->assertOk()->assertSee($product->name);
        $this->patch("/cart/{$product->product_id}", ['delta' => -1]);

        $this->post('/cart/promo', ['code' => 'ONGKIR10'])->assertRedirect();
        $this->post('/cart/promo', ['code' => 'NGAWUR'])->assertSessionHas('error');

        // lokasi sendiri, lengkap dengan titik koordinat
        $this->post('/addresses', ['label' => 'Rumah', 'detail' => 'Jl. Baru No. 9, Singaraja', 'latitude' => '-8.1120000', 'longitude' => '115.0880000'])
            ->assertRedirect();
        $address = $user->addresses()->where('detail', 'Jl. Baru No. 9, Singaraja')->first();
        $this->assertNotNull($address);
        $this->assertTrue($address->is_default);
        $this->put("/addresses/{$address->address_id}", ['label' => 'Kantor', 'detail' => 'Jl. Baru No. 10']);
        $this->post('/addresses', ['label' => 'Rumah', 'detail' => ''])->assertSessionHasErrors('detail');

        $this->post('/orders', ['payment_method' => 'QRIS', 'notes' => 'tanpa sambal'])->assertRedirect();
        $order = Order::latest('order_id')->first();
        $this->assertSame(2 * $product->price, $order->total);   // ongkir 10.000 dibebaskan promo ONGKIR10
        $this->assertSame('Jl. Baru No. 10', $order->address_detail);
        $this->assertEquals(-8.112, (float) $order->address_lat);

        $this->assertSame($stockBefore - 2, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_logs', ['product_id' => $product->product_id, 'change' => -2]);

        $this->get("/orders/{$order->order_code}")->assertOk()->assertSee('QRIS');
        $this->post("/orders/{$order->order_code}/pay");
        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->get('/orders')->assertOk();

        // pelanggan lain tidak boleh membuka pesanan ini
        $this->actingAs(User::factory()->create(['role' => 'customer']))->get("/orders/{$order->order_code}")->assertForbidden();
    }

    public function test_checkout_ditolak_jika_stok_kurang_atau_keranjang_kosong(): void
    {
        $this->actingAs($this->customer());
        $product = Product::first();
        $product->update(['stock' => 1]);

        $this->post("/cart/{$product->product_id}", ['qty' => 2]);
        $this->post('/orders', ['payment_method' => 'CASH'])->assertSessionHasErrors('cart');
        $this->assertSame(1, $product->fresh()->stock);

        $this->post('/orders', ['payment_method' => 'CASH'])->assertSessionHasErrors('cart');
    }

    public function test_pelanggan_tidak_bisa_masuk_halaman_admin(): void
    {
        $this->actingAs($this->customer())->get('/admin/dashboard')->assertForbidden();
        $this->get('/admin/stok')->assertForbidden();
    }

    public function test_semua_halaman_admin_terbuka(): void
    {
        $this->actingAs($this->admin());
        $order = Order::first();

        foreach (['/admin/dashboard', '/admin/orders', "/admin/orders/{$order->order_code}", '/admin/pemasukan', '/admin/stok',
            '/admin/products', '/admin/products/create', '/admin/products/1/edit', '/admin/categories', '/admin/categories/create',
            '/admin/promos', '/admin/promos/create', '/admin/keterangan-toko'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/admin/pemasukan?from=2026-01-01&to=2030-12-31')->assertOk();
        $this->get('/admin/stok?level=menipis')->assertOk();
        $this->get('/admin/orders?status=selesai&s=MJ')->assertOk();
    }

    public function test_admin_mengurangi_dan_menambah_stok(): void
    {
        $this->actingAs($this->admin());
        $product = Product::first();
        $product->update(['stock' => 10]);

        $this->patch("/admin/stok/{$product->product_id}", ['amount' => 5, 'action' => 'tambah', 'note' => 'belanja pasar'])->assertSessionHas('success');
        $this->assertSame(15, $product->fresh()->stock);

        $this->patch("/admin/stok/{$product->product_id}", ['amount' => 4, 'action' => 'kurangi']);
        $this->assertSame(11, $product->fresh()->stock);

        // tidak boleh minus
        $this->patch("/admin/stok/{$product->product_id}", ['amount' => 99, 'action' => 'kurangi'])->assertSessionHasErrors('stock');
        $this->assertSame(11, $product->fresh()->stock);
        $this->patch("/admin/stok/{$product->product_id}", ['amount' => 0, 'action' => 'tambah'])->assertSessionHasErrors('amount');

        $this->assertSame(2, StockLog::where('product_id', $product->product_id)->where('user_id', $this->admin()->id)->count());
        $this->get('/admin/stok')->assertSee('belanja pasar');
    }

    public function test_admin_crud_menu_dengan_upload_foto(): void
    {
        Storage::fake('assets');
        $this->actingAs($this->admin());

        $this->post('/admin/products', [
            'name' => 'Mie Goreng Spesial', 'category_id' => 1, 'price' => 20000, 'stock' => 30, 'is_active' => 1,
            'img' => UploadedFile::fake()->image('mie.jpg'),
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Mie Goreng Spesial')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('assets/menu/', $product->img);
        Storage::disk('assets')->assertExists($product->img);

        $this->put("/admin/products/{$product->product_id}", ['name' => 'Mie Goreng Super', 'category_id' => 1, 'price' => 22000, 'stock' => 30])->assertRedirect();
        $this->assertSame('Mie Goreng Super', $product->fresh()->name);

        $old = $product->img;
        $this->delete("/admin/products/{$product->product_id}")->assertRedirect();
        Storage::disk('assets')->assertMissing($old);
        $this->assertNull(Product::find($product->product_id));
    }

    public function test_admin_kelola_kategori_dan_promo(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/categories', ['category' => 'Dessert'])->assertRedirect();
        $this->assertDatabaseHas('categories', ['category' => 'Dessert']);

        $this->post('/admin/promos', ['code' => 'baru20', 'title' => 'Diskon 20 ribu', 'type' => 'diskon', 'value' => 20000, 'min_order' => 50000, 'is_active' => 1])->assertRedirect();
        $this->assertDatabaseHas('promos', ['code' => 'BARU20']);
        $this->post('/admin/promos', ['code' => 'BARU20', 'title' => 'dobel', 'type' => 'diskon', 'value' => 1, 'min_order' => 0])->assertSessionHasErrors('code');
    }

    public function test_admin_ubah_status_pesanan_pembatalan_mengembalikan_stok_dan_pemasukan(): void
    {
        $user = $this->customer();
        $this->actingAs($user);
        $product = Product::first();
        $product->update(['stock' => 20]);
        $this->post("/cart/{$product->product_id}", ['qty' => 2]);
        $this->post('/orders', ['payment_method' => 'CASH']);
        $order = Order::latest('order_id')->first();
        $this->assertSame(18, $product->fresh()->stock);

        $this->actingAs($this->admin());
        $this->patch("/admin/orders/{$order->order_code}", ['status' => 'selesai'])->assertSessionHas('success');
        $this->assertSame('lunas', $order->fresh()->payment_status);   // CASH lunas saat selesai
        $income = (int) Order::where('status', '!=', 'dibatalkan')->where('payment_status', 'lunas')->sum('total');
        $this->get('/admin/pemasukan?from=2000-01-01&to=2100-01-01')->assertOk()->assertSee(rp($income));

        $second = $this->makeOrder($user, $product, 'QRIS');
        $this->actingAs($this->admin());
        $this->patch("/admin/orders/{$second->order_code}", ['status' => 'dibatalkan']);
        $this->assertSame(18, $product->fresh()->stock);   // stok kembali
        $this->patch("/admin/orders/{$second->order_code}", ['status' => 'selesai'])->assertSessionHas('error');
    }

    private function makeOrder(User $user, Product $product, string $payment): Order
    {
        $this->actingAs($user);
        $this->post("/cart/{$product->product_id}", ['qty' => 2]);
        $this->post('/orders', ['payment_method' => $payment]);

        return Order::latest('order_id')->first();
    }

    public function test_keterangan_toko_dan_ongkir_bisa_diubah_admin(): void
    {
        $this->actingAs($this->admin());
        $this->put('/admin/keterangan-toko', [
            'store_name' => 'Mutiara Jaya Singaraja', 'store_address' => 'Jl. Pramuka 1', 'store_hours' => '08.00 - 21.00',
            'delivery_area' => 'Singaraja', 'delivery_fee' => 7000, 'low_stock_limit' => 5,
        ])->assertRedirect();

        $this->assertSame('Jl. Pramuka 1', Setting::val('store_address'));
        $this->get('/tentang')->assertSee('Mutiara Jaya Singaraja')->assertSee('08.00 - 21.00');
        $this->put('/admin/keterangan-toko', ['store_name' => '', 'delivery_fee' => 'abc', 'low_stock_limit' => 5])->assertSessionHasErrors(['store_name', 'delivery_fee']);

        // ongkir baru dipakai pada pesanan berikutnya
        $user = $this->customer();
        $this->actingAs($user);
        $product = Product::first();
        $this->post("/cart/{$product->product_id}", ['qty' => 1]);
        $this->get('/cart')->assertSee('7.000');
    }

    public function test_rest_api(): void
    {
        $this->getJson('/api/products')->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'price', 'img_url']], 'links', 'meta']);
        $this->getJson('/api/profile')->assertUnauthorized();

        $token = $this->postJson('/api/login', ['email' => 'pelanggan@mutiarajaya.id', 'password' => 'password'])->assertOk()->json('token');
        $this->withToken($token)->getJson('/api/profile')->assertOk();
        $this->withToken($token)->postJson('/api/products', ['name' => 'x'])->assertForbidden();

        $order = $this->withToken($token)->postJson('/api/orders', [
            'items' => [['product_id' => 1, 'quantity' => 2]], 'address' => 'Jl. API 1', 'payment_method' => 'CASH',
        ])->assertCreated()->json('data.id');
        $this->withToken($token)->getJson("/api/orders/{$order}")->assertOk();

        $this->app['auth']->forgetGuards();   // token berganti di dalam satu pengujian
        $adminToken = $this->postJson('/api/login', ['email' => 'admin@mutiarajaya.id', 'password' => 'admin123'])->json('token');
        $this->withToken($adminToken)->postJson('/api/categories', ['category' => 'Dessert'])->assertCreated();
        $this->withToken($adminToken)->postJson('/api/logout')->assertOk();

        // route API lama milik proyek (nilai) tetap ada
        $this->getJson('/api/nilai')->assertOk();
    }
}
