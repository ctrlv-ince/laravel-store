@php
    $statusIcons = [
        'pending' => 'fa-clock',
        'shipped' => 'fa-truck',
        'for_confirm' => 'fa-check-circle',
        'completed' => 'fa-check-double',
        'cancelled' => 'fa-times-circle'
    ];
@endphp

<span class="status-badge status-{{ $order->status }}">
    <i class="fas {{ $statusIcons[$order->status] }} mr-1"></i>
    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
</span> 