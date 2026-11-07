<x-admin-layout :title="$title">
    <div class="shadow bg-white rounded">

        {{-- PENCARIAN & FILTER (GET, agar tercatat di url) --}}
        <form action="{{ route('admin.orders.index') }}" method="GET" class="p-4 grid grid-cols-1 md:grid-cols-3 gap-2">
            <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">Semua status</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="s" value="{{ request('s') }}" placeholder="Cari kode pesanan / nama pelanggan"
                   class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <div class="flex gap-2">
                <button class="px-4 py-2 text-sm rounded-md border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white cursor-pointer">Cari</button>
                @if (request()->hasAny(['s', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100">Reset</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Kode</th><th class="px-6 py-3">Pelanggan</th><th class="px-6 py-3">Item</th>
                        <th class="px-6 py-3">Pembayaran</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $o)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-mono font-bold">{{ $o->order_code }}</div>
                                <div class="text-xs text-gray-500">{{ $o->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $o->user?->name }}</td>
                            <td class="px-6 py-4">{{ $o->items->sum('quantity') }} porsi</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $o->payment_method }}
                                <div class="text-xs {{ $o->payment_status === 'lunas' ? 'text-green-600' : 'text-orange-500' }}">{{ $o->payment_status === 'lunas' ? 'Lunas' : 'Belum bayar' }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold whitespace-nowrap">{{ rp($o->total) }}</td>
                            <td class="px-6 py-4">@include('admin.orders._badge', ['status' => $o->status])</td>
                            <td class="px-6 py-4 text-right"><a href="{{ route('admin.orders.show', $o) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-4">{{ $orders->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
