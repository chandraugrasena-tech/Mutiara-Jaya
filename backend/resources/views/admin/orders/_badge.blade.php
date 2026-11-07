@php
    $colors = [
        'diterima' => 'bg-blue-100 text-blue-700',
        'dimasak' => 'bg-yellow-100 text-yellow-700',
        'diantar' => 'bg-purple-100 text-purple-700',
        'selesai' => 'bg-green-100 text-green-700',
        'dibatalkan' => 'bg-red-100 text-red-700',
    ];
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $colors[$status] ?? 'bg-gray-100 text-gray-700' }}">{{ \App\Models\Order::STATUS_LABELS[$status] ?? $status }}</span>
