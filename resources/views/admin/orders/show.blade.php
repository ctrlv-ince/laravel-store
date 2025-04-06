@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-shopping-basket text-green-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Order #{{ $order->order_id }} Details</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-between">
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
            <div>
                <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i> Update Status
                </a>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6 border-l-4 
            @if($order->status == 'pending') border-yellow-500
            @elseif($order->status == 'processing') border-blue-500
            @elseif($order->status == 'completed') border-green-500
            @elseif($order->status == 'cancelled') border-red-500
            @elseif($order->status == 'refunded') border-purple-500
            @else border-gray-500
            @endif">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-info-circle 
                        @if($order->status == 'pending') text-yellow-500
                        @elseif($order->status == 'processing') text-blue-500
                        @elseif($order->status == 'completed') text-green-500
                        @elseif($order->status == 'cancelled') text-red-500
                        @elseif($order->status == 'refunded') text-purple-500
                        @else text-gray-500
                        @endif mr-2 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-white">Order Status: <span class="uppercase">{{ $order->status ?? 'Unknown' }}</span></h3>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center justify-center px-4 py-2 rounded-full 
                        @if($order->status == 'pending') bg-yellow-500 text-white
                        @elseif($order->status == 'processing') bg-blue-500 text-white
                        @elseif($order->status == 'completed') bg-green-500 text-white
                        @elseif($order->status == 'cancelled') bg-red-500 text-white
                        @elseif($order->status == 'refunded') bg-purple-500 text-white
                        @else bg-gray-500 text-white
                        @endif font-bold">
                        {{ ucfirst($order->status ?? 'Unknown') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Order Information -->
            <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                <div class="border-b border-gray-700 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Order Information</h3>
                </div>
                <div class="p-6">
                    <div class="mb-3 flex justify-between">
                        <span class="text-gray-400">Order ID:</span>
                        <span class="text-white font-medium">#{{ $order->order_id }}</span>
                    </div>
                    <div class="mb-3 flex justify-between">
                        <span class="text-gray-400">Date:</span>
                        <span class="text-white">
                            @if(isset($order->date_ordered))
                                @if($order->date_ordered instanceof \DateTime || $order->date_ordered instanceof \Carbon\Carbon)
                                    {{ $order->date_ordered->format('M d, Y H:i') }}
                                @else
                                    {{ $order->date_ordered }}
                                @endif
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="mb-3 flex justify-between">
                        <span class="text-gray-400">Items:</span>
                        <span class="text-white">{{ $order->orderInfos->count() }} item(s)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total:</span>
                        <span class="text-white font-bold">₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                <div class="border-b border-gray-700 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Customer Information</h3>
                </div>
                <div class="p-6">
                    @if($order->account && $order->account->user)
                        <div class="mb-2 flex items-center">
                            <i class="fas fa-user text-blue-500 mr-2"></i>
                            <span class="text-white">{{ $order->account->user->first_name }} {{ $order->account->user->last_name }}</span>
                        </div>
                        <div class="mb-2 flex items-center">
                            <i class="fas fa-envelope text-blue-500 mr-2"></i>
                            <span class="text-white">{{ $order->account->user->email }}</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-700">
                            <a href="{{ route('admin.users.show', $order->account->user->user_id) }}" class="text-blue-400 hover:text-blue-500 flex items-center">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                View customer profile
                            </a>
                        </div>
                    @else
                        <div class="flex items-center text-yellow-500">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>Customer information not available</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg mb-6">
            <div class="border-b border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Order Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($order->orderInfos as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if(isset($item->item->images) && $item->item->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->item->images->first()->image_path) }}" 
                                         alt="{{ $item->item->item_name }}" 
                                         class="w-14 h-14 rounded-md mr-3 object-cover">
                                    @else
                                    <div class="w-14 h-14 rounded-md bg-gray-600 mr-3 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $item->item->item_name }}</div>
                                        <a href="{{ route('admin.items.edit', $item->item_id) }}" class="text-xs text-blue-400 hover:text-blue-300 inline-flex items-center mt-1">
                                            <i class="fas fa-edit mr-1"></i> Edit product
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                ₱{{ number_format($item->unit_price ?? $item->item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                ₱{{ number_format(($item->unit_price ?? $item->item->price) * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-700">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-white">Total:</td>
                            <td class="px-6 py-4 text-sm font-bold text-white">₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 