@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-user text-blue-500 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Customer Profile</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-between">
            <a href="{{ url()->previous() }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">
                    {{ $user->first_name }} {{ $user->last_name }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Information -->
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-white mb-4 border-b border-gray-600 pb-2">
                            <i class="fas fa-address-card text-blue-400 mr-2"></i>User Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">ID:</span>
                                <span class="text-white">#{{ $user->user_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">First Name:</span>
                                <span class="text-white">{{ $user->first_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Last Name:</span>
                                <span class="text-white">{{ $user->last_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Email:</span>
                                <span class="text-white">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Registered:</span>
                                <span class="text-white">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Last Updated:</span>
                                <span class="text-white">{{ $user->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Information -->
                    @if($account)
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-white mb-4 border-b border-gray-600 pb-2">
                            <i class="fas fa-user-shield text-blue-400 mr-2"></i>Account Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Account ID:</span>
                                <span class="text-white">#{{ $account->account_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Username:</span>
                                <span class="text-white">{{ $account->username }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Role:</span>
                                <span class="text-white uppercase">{{ $account->role }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $account->account_status == 'active' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ ucfirst($account->account_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center text-yellow-500">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>No account details available</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Order History -->
                <div class="mt-6 bg-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-white mb-4 border-b border-gray-600 pb-2">
                        <i class="fas fa-shopping-cart text-blue-400 mr-2"></i>Order History
                    </h3>
                    
                    @if($account && $account->orders && $account->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-600">
                                    @foreach($account->orders as $order)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                            #{{ $order->order_id }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                            {{ \Carbon\Carbon::parse($order->date_ordered)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-white">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($order->status == 'pending') bg-yellow-500 text-white
                                                @elseif($order->status == 'processing') bg-blue-500 text-white
                                                @elseif($order->status == 'completed') bg-green-500 text-white
                                                @elseif($order->status == 'cancelled') bg-red-500 text-white
                                                @elseif($order->status == 'refunded') bg-purple-500 text-white
                                                @else bg-gray-500 text-white
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="text-blue-400 hover:text-blue-300">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-gray-400">
                            No orders found for this customer.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 