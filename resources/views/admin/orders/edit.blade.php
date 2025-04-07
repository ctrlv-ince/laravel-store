@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-edit text-blue-500 mr-2"></i>
    Edit Order #{{ $order->order_id }}
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-between">
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-eye mr-2"></i> View Order Details
            </a>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Update Order Status</h2>
                
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-white mb-3">Order Details</h3>
                            <div class="bg-gray-700 rounded-lg p-4">
                                <div class="mb-2">
                                    <span class="text-gray-400">Order ID:</span>
                                    <span class="text-white ml-2">#{{ $order->order_id }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-400">Date:</span>
                                    <span class="text-white ml-2">
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
                                <div class="mb-2">
                                    <span class="text-gray-400">Total Amount:</span>
                                    <span class="text-white ml-2">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Current Status:</span>
                                    <span class="ml-2 text-xs px-2 py-1 rounded-full
                                        @if($order->status == 'pending') bg-yellow-500 text-white
                                        @elseif($order->status == 'shipped') bg-blue-500 text-white
                                        @elseif($order->status == 'for_confirm') bg-purple-500 text-white
                                        @elseif($order->status == 'completed') bg-green-500 text-white
                                        @elseif($order->status == 'cancelled') bg-red-500 text-white
                                        @else bg-gray-500 text-white
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $order->status ?? 'Unknown')) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-white mb-3">Customer Information</h3>
                            <div class="bg-gray-700 rounded-lg p-4">
                                @if($order->account && $order->account->user)
                                <div class="mb-2">
                                    <span class="text-gray-400">Name:</span>
                                    <span class="text-white ml-2">{{ $order->account->user->first_name }} {{ $order->account->user->last_name }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-400">Email:</span>
                                    <span class="text-white ml-2">{{ $order->account->user->email }}</span>
                                </div>
                                @else
                                <div class="text-yellow-500">Customer information not available</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($errors->any())
                <div class="bg-red-500 text-white p-4 mb-6 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST" id="updateOrderForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Update Status</label>
                        <select name="status" id="status" class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Changing the status will send an email notification to the customer.</p>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            Update Order Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-white mb-4">Order Items</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($order->orderInfos as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if(isset($item->item->images) && $item->item->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->item->images->first()->image_path) }}" 
                                             alt="{{ $item->item->item_name }}" 
                                             class="w-10 h-10 rounded-full mr-3 object-cover">
                                        @else
                                        <div class="w-10 h-10 rounded-full bg-gray-600 mr-3 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-white">{{ $item->item->item_name }}</div>
                                            <div class="text-sm text-gray-400">{{ $item->item->item_description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    ₱{{ number_format($item->item->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    ₱{{ number_format($item->item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-400">Total:</td>
                                <td class="px-6 py-4 text-sm font-bold text-white">₱{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateOrderForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitButton.disabled = true;
        
        // Submit the form
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            if (data.success) {
                // Trigger refresh of orders table
                window.dispatchEvent(new CustomEvent('orderStatusUpdated'));
                
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'bg-green-600 text-white p-4 mb-6 rounded-lg shadow-md';
                successMessage.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>${data.message}</span>
                    </div>
                `;
                form.parentElement.insertBefore(successMessage, form);
                
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = "{{ route('admin.orders.index') }}";
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to update order status');
            }
        })
        .catch(error => {
            // Reset button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            // Show error message
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-600 text-white p-4 mb-6 rounded-lg shadow-md';
            errorMessage.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>${error.message}</span>
                </div>
            `;
            form.parentElement.insertBefore(errorMessage, form);
        });
    });
});
</script>
@endpush
@endsection 