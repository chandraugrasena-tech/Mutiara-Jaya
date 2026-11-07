<x-home-layout :title="$title">
    <div class="max-w-3xl mx-auto space-y-4">
        <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Pesanan saya</h2>

        @forelse ($orders as $o)
            <a href="{{ route('orders.show', $o) }}" class="block bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <p class="font-mono font-bold text-sm">#{{ $o->order_code }}</p>
                        <p class="text-[11px] text-gray-500">{{ $o->created_at->translatedFormat('d M Y, H:i') }} | {{ $o->items->sum('quantity') }} porsi | {{ $o->payment_method }}</p>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $o->items->pluck('product_name')->join(', ') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-extrabold text-sm">{{ rp($o->total) }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                            {{ $o->status === 'selesai' ? 'bg-green-100 text-green-700' : ($o->status === 'dibatalkan' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $o->status_label }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-12 space-y-3">
                <p class="text-sm text-gray-600">Belum ada pesanan.</p>
                <a href="{{ route('home') }}" class="inline-block bg-[#165823] text-white text-xs font-bold px-6 py-2.5 rounded-xl">Pilih menu</a>
            </div>
        @endforelse

        <div>{{ $orders->links() }}</div>
    </div>
</x-home-layout>
