@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-400">
                <i class="fas fa-microchip mr-2"></i>Order Details #{{ $order->order_id }}
            </h1>
            <div class="flex space-x-4">
                <a href="{{ route('orders.generateReceipt', $order) }}" 
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
                        <span class="text-white font-medium">
                            @if(is_string($order->date_ordered))
                                {{ \Carbon\Carbon::parse($order->date_ordered)->format('M d, Y H:i') }}
                            @else
                                {{ $order->date_ordered->format('M d, Y H:i') }}
                            @endif
                        </span>
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

        @if(Auth::check() && Auth::user()->account && Auth::user()->account->isAdmin())
        <div class="mt-6 flex justify-end">
            <button type="button" 
                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                    onclick="updateStatus('{{ $order->order_id }}')">
                <i class="fas fa-cog mr-2"></i>Update Status
            </button>
        </div>
        @endif

        <!-- Review Section for Completed Orders -->
        @if($order->status === 'completed')
        <div id="review-products" class="mt-6 bg-gray-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-blue-400 mb-4">
                <i class="fas fa-star mr-2"></i>Review Products
            </h2>
            
            <div class="space-y-6">
                @foreach($order->orderInfos as $orderInfo)
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center mb-4">
                        <div class="flex items-center mb-4 md:mb-0 md:flex-1">
                            @if($orderInfo->item->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $orderInfo->item->images->first()->image_path) }}" 
                                 alt="{{ $orderInfo->item->item_name }}"
                                 class="w-16 h-16 object-cover rounded-lg mr-3">
                            @endif
                            <div>
                                <div class="text-white font-medium">{{ $orderInfo->item->item_name }}</div>
                                <div class="text-gray-400 text-sm">Quantity: {{ $orderInfo->quantity }}</div>
                            </div>
                        </div>

                        @php
                            $existingReview = null;
                            if (Auth::check() && Auth::user()->account) {
                                $existingReview = $orderInfo->item->reviews()
                                    ->where('account_id', Auth::user()->account->account_id)
                                    ->first();
                            }
                        @endphp

                        <button 
                            type="button" 
                            class="review-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg"
                            data-item-id="{{ $orderInfo->item->item_id }}"
                            data-item-name="{{ $orderInfo->item->item_name }}"
                            data-rating="{{ $existingReview ? $existingReview->rating : 0 }}"
                            data-comment="{{ $existingReview ? $existingReview->comment : '' }}">
                            {{ $existingReview ? 'Update Review' : 'Write a Review' }}
                        </button>
                    </div>

                    @if($existingReview)
                    <div class="bg-gray-900 p-3 rounded mt-2">
                        <div class="flex items-center mb-2">
                            <div class="mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $existingReview->rating)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-500"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-gray-400 text-sm">
                                Reviewed on {{ \Carbon\Carbon::parse($existingReview->create_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <p class="text-white">{{ $existingReview->comment }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@if(Auth::check() && Auth::user()->account && Auth::user()->account->isAdmin())
@include('orders.actions')
@endif

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full">
        <h3 class="text-xl font-semibold text-blue-400 mb-4">Review Product</h3>
        <p id="review-item-name" class="text-white text-lg mb-4"></p>
        
        <form id="review-form">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Rating</label>
                <div class="flex space-x-2" id="star-rating">
                    <button type="button" class="star text-3xl text-gray-500 focus:outline-none" data-rating="1">★</button>
                    <button type="button" class="star text-3xl text-gray-500 focus:outline-none" data-rating="2">★</button>
                    <button type="button" class="star text-3xl text-gray-500 focus:outline-none" data-rating="3">★</button>
                    <button type="button" class="star text-3xl text-gray-500 focus:outline-none" data-rating="4">★</button>
                    <button type="button" class="star text-3xl text-gray-500 focus:outline-none" data-rating="5">★</button>
                </div>
                <input type="hidden" name="rating" id="rating-input" value="0">
            </div>
            
            <div class="mb-4">
                <label for="comment" class="block text-gray-300 mb-2">Comment</label>
                <textarea 
                    name="comment" 
                    id="comment" 
                    rows="4" 
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Share your thoughts about this product"></textarea>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button 
                    type="button" 
                    id="close-review-modal" 
                    class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors duration-200">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let itemId = 0;
    
    $(document).ready(function() {
        // Review modal functionality
        $('.review-btn').on('click', function() {
            const button = $(this);
            itemId = button.data('item-id');
            const itemName = button.data('item-name');
            const rating = button.data('rating');
            const comment = button.data('comment');
            
            // Set values in the modal
            $('#review-item-name').text(itemName);
            $('#comment').val(comment);
            $('#rating-input').val(rating);
            
            // Update the stars
            updateStars(rating);
            
            // Show the modal
            $('#review-modal').removeClass('hidden');
        });
        
        // Close modal when clicking cancel
        $('#close-review-modal').on('click', function() {
            $('#review-modal').addClass('hidden');
        });
        
        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#review-modal')) {
                $('#review-modal').addClass('hidden');
            }
        });
        
        // Star rating functionality
        $('.star').on('click', function() {
            const rating = $(this).data('rating');
            $('#rating-input').val(rating);
            updateStars(rating);
        });
        
        // Submit review form
        $('#review-form').on('submit', function(e) {
            e.preventDefault();
            
            const rating = $('#rating-input').val();
            const comment = $('#comment').val();
            
            if (rating < 1) {
                alert('Please select a rating');
                return;
            }
            
            if (comment.trim() === '') {
                alert('Please write a comment');
                return;
            }
            
            $.ajax({
                url: `/items/${itemId}/reviews`,
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    rating: rating,
                    comment: comment
                },
                success: function(response) {
                    if (response.success) {
                        $('#review-modal').addClass('hidden');
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred while submitting your review.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMsg);
                }
            });
        });
    });
    
    function updateStars(rating) {
        $('.star').each(function(index) {
            if (index < rating) {
                $(this).removeClass('text-gray-500').addClass('text-yellow-400');
            } else {
                $(this).removeClass('text-yellow-400').addClass('text-gray-500');
            }
        });
    }
</script>
@endpush
@endsection 