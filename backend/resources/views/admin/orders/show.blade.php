<x-admin-layout :title="$title">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded shadow p-5 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-mono font-bold text-lg">#{{ $order->order_code }}</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @include('admin.orders._badge', ['status' => $order->status])
            </div>

            <div class="text-sm space-y-1">
                <p><strong>Pelanggan:</strong> {{ $order->user?->name }} ({{ $order->user?->email }}) {{ $order->user?->phone }}</p>
                <p><strong>Alamat:</strong> {{ $order->address_label }} | {{ $order->address_detail }}</p>
                @if ($order->address_lat && $order->address_lng)
                    <p><strong>Titik lokasi:</strong>
                        <a href="https://www.google.com/maps?q={{ $order->address_lat }},{{ $order->address_lng }}" target="_blank" rel="noreferrer" class="text-indigo-600 hover:underline">Lihat di peta</a>
                        <span class="text-xs text-gray-500">({{ $order->address_lat }}, {{ $order->address_lng }})</span>
                    </p>
                @endif
                @if ($order->notes)<p><strong>Catatan:</strong> {{ $order->notes }}</p>@endif
            </div>

            <table class="w-full text-sm">
                <thead><tr class="border-b text-left text-gray-600"><th class="py-2">Menu</th><th class="py-2 text-center">Qty</th><th class="py-2 text-right">Harga</th><th class="py-2 text-right">Subtotal</th></tr></thead>
                <tbody>
                    @foreach ($order->items as $i)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $i->product_name }}</td>
                            <td class="py-2 text-center">{{ $i->quantity }}</td>
                            <td class="py-2 text-right">{{ rp($i->price) }}</td>
                            <td class="py-2 text-right">{{ rp($i->price * $i->quantity) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-sm space-y-1 border-t pt-3 ml-auto max-w-xs">
                <div class="flex justify-between"><span>Subtotal</span><span>{{ rp($order->subtotal) }}</span></div>
                <div class="flex justify-between"><span>Ongkir</span><span>{{ rp($order->ongkir) }}</span></div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-green-700"><span>Promo {{ $order->promo_title }}</span><span>- {{ rp($order->discount) }}</span></div>
                @endif
                <div class="flex justify-between font-extrabold text-base"><span>Total</span><span>{{ rp($order->total) }}</span></div>
            </div>
        </div>

        {{-- Ubah status --}}
        <div class="bg-white rounded shadow p-5 h-fit space-y-4">
            <h3 class="font-bold">Perbarui pesanan</h3>

            @if ($order->status === 'dibatalkan')
                <p class="text-sm text-red-600">Pesanan dibatalkan, tidak dapat diubah lagi.</p>
            @else
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status pesanan</label>
                        <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status pembayaran ({{ $order->payment_method }})</label>
                        <select name="payment_status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="belum_bayar" @selected($order->payment_status === 'belum_bayar')>Belum bayar</option>
                            <option value="lunas" @selected($order->payment_status === 'lunas')>Lunas</option>
                        </select>
                    </div>
                    <button class="w-full py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer"
                            onclick="return this.form.status.value !== 'dibatalkan' || confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                        Simpan
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.orders.index') }}" class="block text-center text-sm text-gray-500 hover:underline">Kembali ke daftar</a>
        </div>
    </div>
</x-admin-layout>
