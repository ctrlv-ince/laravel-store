@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
    {{ $isAdmin ? 'Admin Dashboard' : 'Dashboard' }}
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-500 text-white p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-500 text-white p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
        @endif
        
        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @if($isAdmin)
                <!-- Admin Stats -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Revenue</p>
                            <p class="text-white font-bold text-2xl">₱{{ number_format($totalRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Orders</p>
                            <p class="text-white font-bold text-2xl">{{ $totalOrders }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Products</p>
                            <p class="text-white font-bold text-2xl">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Users</p>
                            <p class="text-white font-bold text-2xl">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Regular User Stats -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Spent</p>
                            <p class="text-white font-bold text-2xl">₱{{ number_format($totalRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Your Orders</p>
                            <p class="text-white font-bold text-2xl">{{ $totalOrders }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg p-5 col-span-2">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 mr-4">
                            <svg class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Your Account</p>
                            <p class="text-white font-bold text-2xl">{{ $user->first_name }} {{ $user->last_name }}</p>
                            <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @if($isAdmin)
                <!-- Admin Content -->
                <!-- Recent Orders Section -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg col-span-2">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                    </div>
                    <div class="p-4">
                        @if($recentOrders->isEmpty())
                            <p class="text-gray-400 text-center py-4">No recent orders found.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700">
                                        @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                #{{ $order->order_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $order->account && $order->account->user ? $order->account->user->first_name . ' ' . $order->account->user->last_name : 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                @if(isset($order->date_ordered))
                                                    @if($order->date_ordered instanceof \DateTime || $order->date_ordered instanceof \Carbon\Carbon)
                                                        {{ $order->date_ordered->format('M d, Y') }}
                                                    @else
                                                        {{ $order->date_ordered }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($order->status == 'pending') bg-yellow-500 text-white
                                                    @elseif($order->status == 'processing') bg-blue-500 text-white
                                                    @elseif($order->status == 'completed') bg-green-500 text-white
                                                    @elseif($order->status == 'cancelled') bg-red-500 text-white
                                                    @elseif($order->status == 'refunded') bg-purple-500 text-white
                                                    @else bg-gray-500 text-white
                                                    @endif">
                                                    {{ ucfirst($order->status ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('admin.orders.show', $order->order_id) }}" class="text-blue-500 hover:text-blue-400">View</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium">View All Orders <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <a href="{{ route('admin.items.create') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-blue-500 p-2 rounded-full">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span>Add New Product</span>
                            </a>
                            <a href="{{ route('admin.groups.create') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-purple-500 p-2 rounded-full">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <span>Add New Category</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-yellow-500 p-2 rounded-full">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span>Manage Users</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-green-500 p-2 rounded-full">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                                <span>Manage Orders</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Popular Products -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg col-span-3 mt-6">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Popular Products</h3>
                    </div>
                    <div class="p-4">
                        @if($popularProducts->isEmpty())
                            <p class="text-gray-400 text-center py-4">No products found.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($popularProducts as $product)
                                <div class="bg-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                    <div class="h-40 bg-gray-600 overflow-hidden">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->item_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h4 class="text-white font-medium truncate">{{ $product->item_name }}</h4>
                                        <p class="text-gray-400 text-sm mb-2">₱{{ number_format($product->price, 2) }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs {{ $product->inventory && $product->inventory->quantity > 0 ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $product->inventory && $product->inventory->quantity > 0 ? 'In Stock: ' . $product->inventory->quantity : 'Out of Stock' }}
                                            </span>
                                            <a href="{{ route('admin.items.edit', $product->item_id) }}" class="text-blue-500 hover:text-blue-400 text-sm">Edit</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('admin.items.index') }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium">View All Products <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Regular User Content -->
                <!-- Recent Orders Section -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg col-span-3">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Your Recent Orders</h3>
                    </div>
                    <div class="p-4">
                        @if($recentOrders->isEmpty())
                            <p class="text-gray-400 text-center py-4">You haven't placed any orders yet.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700">
                                        @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                #{{ $order->order_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                @if(isset($order->date_ordered))
                                                    @if($order->date_ordered instanceof \DateTime || $order->date_ordered instanceof \Carbon\Carbon)
                                                        {{ $order->date_ordered->format('M d, Y') }}
                                                    @else
                                                        {{ $order->date_ordered }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($order->status == 'pending') bg-yellow-500 text-white
                                                    @elseif($order->status == 'processing') bg-blue-500 text-white
                                                    @elseif($order->status == 'completed') bg-green-500 text-white
                                                    @elseif($order->status == 'cancelled') bg-red-500 text-white
                                                    @elseif($order->status == 'refunded') bg-purple-500 text-white
                                                    @else bg-gray-500 text-white
                                                    @endif">
                                                    {{ ucfirst($order->status ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('orders.show', $order->order_id) }}" class="text-blue-500 hover:text-blue-400">View</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('user.orders') }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium">View All Orders <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg col-span-1 mt-6">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <a href="{{ route('items.index') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-blue-500 p-2 rounded-full">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <span>Shop Products</span>
                            </a>
                            <a href="{{ route('cart.index') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-green-500 p-2 rounded-full">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <span>View Cart</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center p-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white">
                                <div class="mr-3 bg-purple-500 p-2 rounded-full">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Recommended Products -->
                <div class="bg-gray-800 overflow-hidden shadow-lg rounded-lg col-span-2 mt-6">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Recommended Products</h3>
                    </div>
                    <div class="p-4">
                        @if($popularProducts->isEmpty())
                            <p class="text-gray-400 text-center py-4">No products to recommend at this time.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($popularProducts as $product)
                                <div class="bg-gray-700 rounded-lg overflow-hidden flex hover:shadow-lg transition-shadow">
                                    <div class="w-1/3 bg-gray-600 overflow-hidden">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->item_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 w-2/3">
                                        <h4 class="text-white font-medium truncate">{{ $product->item_name }}</h4>
                                        <p class="text-gray-400 text-sm mb-2">₱{{ number_format($product->price, 2) }}</p>
                                        <p class="text-gray-400 text-xs mb-2 line-clamp-2">{{ Str::limit($product->item_description, 80) }}</p>
                                        <div class="mt-2">
                                            <a href="{{ route('items.show', $product->item_id) }}" class="text-blue-500 hover:text-blue-400 text-sm">View Product</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('items.index') }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium">View All Products <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 