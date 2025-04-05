@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-400">
                <i class="fas fa-microchip mr-2"></i>Order Details #{{ $order->order_id }}
            </h1>
            <div class="flex space-x-4">
                <a href="{{ route('orders.generate-receipt', $order) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>Download Receipt
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Order Information -->
            <div class="bg-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-blue-400 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>Order Information
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Order ID:</span>
                        <span class="text-white font-medium">#{{ $order->order_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Date Ordered:</span>
                        <span class="text-white font-medium">{{ $order->date_ordered->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Status:</span>
                        @include('orders.status')
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Total Amount:</span>
                        <span class="text-white font-medium">₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-blue-400 mb-4">
                    <i class="fas fa-user mr-2"></i>Customer Information
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Name:</span>
                        <span class="text-white font-medium">{{ $order->account->user->first_name }} {{ $order->account->user->last_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Email:</span>
                        <span class="text-white font-medium">{{ $order->account->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Phone:</span>
                        <span class="text-white font-medium">{{ $order->account->user->phone_number }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mt-6 bg-gray-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-blue-400 mb-4">
                <i class="fas fa-shopping-cart mr-2"></i>Order Items
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-300">
                            <th class="p-3">Item</th>
                            <th class="p-3">Price</th>
                            <th class="p-3">Quantity</th>
                            <th class="p-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderInfos as $item)
                        <tr class="border-t border-gray-600">
                            <td class="p-3">
                                <div class="flex items-center">
                                    @if($item->item->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->item->images->first()->image_path) }}" 
                                         alt="{{ $item->item->item_name }}"
                                         class="w-12 h-12 object-cover rounded-lg mr-3">
                                    @endif
                                    <div>
                                        <div class="text-white font-medium">{{ $item->item->item_name }}</div>
                                        <div class="text-gray-400 text-sm">{{ $item->item->item_description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 text-white">₱{{ number_format($item->item->price, 2) }}</td>
                            <td class="p-3 text-white">{{ $item->quantity }}</td>
                            <td class="p-3 text-white">₱{{ number_format($item->item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(Auth::user()->account->isAdmin())
        <div class="mt-6 flex justify-end">
            <button type="button" 
                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                    onclick="updateStatus({{ $order->order_id }})">
                <i class="fas fa-cog mr-2"></i>Update Status
            </button>
        </div>
        @endif
    </div>
</div>

@if(Auth::user()->account->isAdmin())
@include('orders.actions')
@endif
@endsection 