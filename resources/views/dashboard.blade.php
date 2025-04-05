@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-microchip text-blue-500 mr-2"></i>
    Dashboard
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">
                                    Total Orders
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-white">
                                        {{ $totalOrders }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">
                                    Total Products
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-white">
                                        {{ $totalProducts }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">
                                    Total Revenue
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-white">
                                        ₱{{ number_format($totalRevenue, 2) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">
                                    Total Users
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-white">
                                        {{ $totalUsers }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-medium text-white mb-4">
                    <i class="fas fa-clock mr-2"></i>
                    Recent Orders
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Customer
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($recentOrders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    #{{ $order->order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $order->account->user->first_name }} {{ $order->account->user->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $order->date_ordered->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @include('orders.status')
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Popular Products -->
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-white mb-4">
                    <i class="fas fa-star mr-2"></i>
                    Popular Products
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($popularProducts as $product)
                    <div class="bg-gray-700 rounded-lg overflow-hidden">
                        @if($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                             alt="{{ $product->item_name }}" 
                             class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                        @endif
                        <div class="p-4">
                            <h4 class="text-lg font-medium text-white mb-2">{{ $product->item_name }}</h4>
                            <p class="text-sm text-gray-400 mb-4">{{ $product->item_description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-white">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-400">{{ $product->orderInfos->sum('quantity') }} sold</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 