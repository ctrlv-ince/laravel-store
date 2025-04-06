@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <a href="{{ route('dashboard') }}" class="mr-4 text-gray-400 hover:text-blue-500 transition-colors duration-150">
        <i class="fas fa-arrow-left"></i>
    </a>
    <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
    My Orders
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-white mb-6">Your Order History</h2>
                
                @if($orders->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-shopping-cart text-gray-500 text-5xl mb-4"></i>
                    <p class="text-gray-400 text-lg">You haven't placed any orders yet.</p>
                    <a href="{{ route('items.index') }}" class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors duration-150">
                        Browse Products
                    </a>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    #{{ $order->order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    @if(isset($order->date_ordered))
                                        @if($order->date_ordered instanceof \DateTime || $order->date_ordered instanceof \Carbon\Carbon)
                                            {{ $order->date_ordered->format('M d, Y H:i') }}
                                        @else
                                            {{ $order->date_ordered }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                            @break
                                        @case('shipped')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Shipped
                                            </span>
                                            @break
                                        @case('for_confirm')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                For Confirmation
                                            </span>
                                            @break
                                        @case('completed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Cancelled
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $order->status }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-500 hover:text-blue-600">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status !== 'cancelled')
                                        <a href="{{ route('orders.generateReceipt', $order) }}" target="_blank" class="text-green-500 hover:text-green-600">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        @endif
                                        @if($order->status === 'completed')
                                        <a href="{{ route('orders.show', $order) }}#review-products" class="text-yellow-500 hover:text-yellow-600">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 