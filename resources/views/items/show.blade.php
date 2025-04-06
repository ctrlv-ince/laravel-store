@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <a href="{{ route('items.index') }}" class="mr-4 text-gray-400 hover:text-blue-500 transition-colors duration-150">
        <i class="fas fa-arrow-left"></i>
    </a>
    <i class="fas fa-box text-blue-500 mr-2"></i>
    <span class="mr-2">Products</span>
    <i class="fas fa-chevron-right text-xs text-gray-600 mx-2"></i>
    <span class="text-gray-400 truncate">{{ $item->item_name }}</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Product Images -->
                <div class="p-6">
                    @if($item->images && $item->images->isNotEmpty())
                    <div class="mb-4">
                        <div class="main-image-container h-96 bg-gray-700 rounded-lg overflow-hidden flex items-center justify-center">
                            <img id="main-image" src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                                alt="{{ $item->item_name }}" 
                                class="w-full h-full object-contain">
                        </div>
                    </div>
                    
                    <!-- Thumbnail images gallery -->
                    @if($item->images->count() > 1)
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($item->images as $image)
                        <div class="h-20 bg-gray-700 rounded cursor-pointer hover:opacity-75 transition-opacity duration-150 {{ $loop->first ? 'ring-2 ring-blue-500' : '' }}"
                             onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', this)">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $item->item_name }}" 
                                 class="w-full h-full object-cover rounded">
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @else
                    <div class="h-96 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-500 text-6xl"></i>
                    </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <h1 class="text-3xl font-semibold text-white mb-2">{{ $item->item_name }}</h1>
                        @if($item->inventory && $item->inventory->quantity > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-300">
                            <span class="dot bg-green-400 mr-1"></span> In Stock
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-900 text-red-300">
                            <span class="dot bg-red-400 mr-1"></span> Out of Stock
                        </span>
                        @endif
                    </div>
                    
                    <!-- Dynamic average rating -->
                    <div class="flex items-center mb-6">
                        <div class="flex items-center text-yellow-400">
                            @php
                                $avgRating = $item->reviews()->avg('rating') ?: 0;
                                $reviewCount = $item->reviews()->count();
                                $fullStars = floor($avgRating);
                                $halfStar = $avgRating - $fullStars > 0.3 && $avgRating - $fullStars < 0.8;
                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            @endphp
                            
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            
                            @if ($halfStar)
                                <i class="fas fa-star-half-alt"></i>
                            @endif
                            
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="far fa-star"></i>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-400 ml-2">{{ number_format($avgRating, 1) }} ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </div>
                    
                    <p class="text-gray-300 mb-6">{{ $item->item_description }}</p>
                    
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-white mb-4">Product Details</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($item->groups && $item->groups->isNotEmpty())
                            <div>
                                <span class="text-gray-400">Category:</span>
                                <span class="text-white">{{ $item->groups->pluck('group_name')->join(', ') }}</span>
                            </div>
                            @endif
                            @if($item->inventory)
                            <div>
                                <span class="text-gray-400">Stock:</span>
                                <span class="text-white">{{ $item->inventory->quantity }} available</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-400">SKU:</span>
                                <span class="text-white">{{ $item->item_id }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <div class="flex items-end justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-white">₱{{ number_format($item->price, 2) }}</h3>
                                @if(isset($item->original_price) && $item->original_price > $item->price)
                                <p class="text-gray-400 line-through">₱{{ number_format($item->original_price, 2) }}</p>
                                @endif
                            </div>
                            
                            @if($item->inventory && $item->inventory->quantity > 0)
                            <div class="flex items-center">
                                <button class="quantity-btn minus bg-gray-700 hover:bg-gray-600 text-white w-10 h-10 rounded-l flex items-center justify-center">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="product-quantity" min="1" value="1" max="{{ $item->inventory->quantity }}" 
                                       class="quantity-input w-16 h-10 bg-gray-700 text-white text-center focus:outline-none">
                                <button class="quantity-btn plus bg-gray-700 hover:bg-gray-600 text-white w-10 h-10 rounded-r flex items-center justify-center">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button id="add-to-cart-btn" class="flex-grow bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-semibold flex items-center justify-center transition-colors duration-150" 
                                    {{ $item->inventory && $item->inventory->quantity > 0 ? '' : 'disabled' }}
                                    data-item-id="{{ $item->item_id }}">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Add to Cart
                            </button>
                            <button id="buy-now-btn" class="flex-grow bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-md font-semibold flex items-center justify-center transition-colors duration-150" 
                                    {{ $item->inventory && $item->inventory->quantity > 0 ? '' : 'disabled' }}
                                    data-item-id="{{ $item->item_id }}">
                                <i class="fas fa-bolt mr-2"></i>
                                Buy Now
                            </button>
                            <button class="add-to-wishlist bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-md flex items-center justify-center transition-colors duration-150">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs for additional information -->
            <div class="border-t border-gray-700">
                <div class="flex border-b border-gray-700">
                    <button class="tab-btn active px-6 py-4 text-sm font-medium text-white focus:outline-none" data-tab="description">
                        Description
                    </button>
                    <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-400 hover:text-white focus:outline-none" data-tab="reviews">
                        Reviews
                    </button>
                    <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-400 hover:text-white focus:outline-none" data-tab="shipping">
                        Shipping & Returns
                    </button>
                </div>
                
                <div id="description" class="tab-content p-6 text-gray-300">
                    <div class="prose prose-invert max-w-none">
                        <p>{{ $item->item_description }}</p>
                        <p class="mt-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, velit vel bibendum bibendum, 
                        velit nisl bibendum nisl, vel bibendum nisl velit nisl bibendum nisl.</p>
                        
                        <ul class="list-disc pl-5 mt-4">
                            <li>Feature 1: Lorem ipsum dolor sit amet</li>
                            <li>Feature 2: Consectetur adipiscing elit</li>
                            <li>Feature 3: Sed euismod, velit vel bibendum</li>
                        </ul>
                    </div>
                </div>
                
                <div id="reviews" class="tab-content hidden p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-white mb-4">Customer Reviews</h3>
                        <div id="reviews-container" class="flex flex-col space-y-4">
                            @if($item->reviews->isEmpty())
                                <div class="bg-gray-700 p-4 rounded">
                                    <p class="text-gray-300">No reviews yet. Be the first to review this product!</p>
                                </div>
                            @else
                                @foreach($item->reviews as $review)
                                <div class="bg-gray-700 p-4 rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-white">{{ $review->account->user->first_name }} {{ $review->account->user->last_name }}</h4>
                                            <div class="flex text-yellow-400 text-sm mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-2 text-gray-300">{{ $review->comment }}</p>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-4">Write a Review</h3>
                        <form id="review-form" data-item-id="{{ $item->item_id }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-400 mb-2">Rating</label>
                                <div class="flex text-gray-500 text-2xl">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="far fa-star cursor-pointer review-star hover:text-yellow-400" data-rating="{{ $i }}"></i>
                                    @endfor
                                    <input type="hidden" name="rating" id="rating-input" value="0">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="review-text" class="block text-gray-400 mb-2">Your Review</label>
                                <textarea id="review-text" name="comment" rows="4" class="w-full px-4 py-2 rounded bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded font-medium">
                                Submit Review
                            </button>
                        </form>
                    </div>
                </div>
                
                <div id="shipping" class="tab-content hidden p-6 text-gray-300">
                    <h3 class="text-xl font-semibold text-white mb-4">Shipping Information</h3>
                    <p class="mb-4">We ship to all major locations within the Philippines.</p>
                    <ul class="list-disc pl-5 mb-6">
                        <li>Metro Manila: 1-2 business days</li>
                        <li>Luzon: 2-3 business days</li>
                        <li>Visayas: 3-5 business days</li>
                        <li>Mindanao: 4-7 business days</li>
                    </ul>
                    
                    <h3 class="text-xl font-semibold text-white mb-4">Returns & Refunds</h3>
                    <p class="mb-4">We accept returns within 7 days of delivery for most items. Please ensure items are returned in original packaging and condition.</p>
                    <p>Contact our customer service team for detailed return instructions.</p>
                </div>
            </div>
        </div>
        
        <!-- Related Products (Dynamic from DB) -->
        <div class="mt-12">
            <h2 class="text-2xl font-semibold text-white mb-6">You Might Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @if($relatedItems->isEmpty())
                    <p class="text-gray-400 col-span-4 text-center">No related products found.</p>
                @else
                    @foreach($relatedItems as $relatedItem)
                    <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-lg transition-shadow duration-300">
                        <a href="{{ route('items.show', $relatedItem) }}">
                            <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                                @if($relatedItem->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $relatedItem->images->first()->image_path) }}" 
                                        alt="{{ $relatedItem->item_name }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-image text-gray-500 text-4xl"></i>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-lg mb-2">{{ $relatedItem->item_name }}</h3>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ Str::limit($relatedItem->item_description, 100) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-white font-bold">₱{{ number_format($relatedItem->price, 2) }}</span>
                                    <button class="related-add-to-cart bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm"
                                            data-item-id="{{ $relatedItem->item_id }}">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cart confirmation modal -->
<div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full">
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-bold text-white" id="cart-modal-message">Item added to cart!</h3>
        </div>
        <div class="flex justify-between space-x-4">
            <button id="continue-shopping" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded">
                Continue Shopping
            </button>
            <a href="/cart" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-center">
                Go to Cart
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .tab-btn.active {
        color: white;
        border-bottom: 2px solid #3B82F6;
    }
</style>
@endpush

@push('scripts')
<script>
    // Image gallery
    function changeMainImage(src, element) {
        document.getElementById('main-image').src = src;
        
        // Update active thumbnail
        const thumbnails = document.querySelectorAll('.grid-cols-5 > div');
        thumbnails.forEach(thumb => {
            thumb.classList.remove('ring-2', 'ring-blue-500');
        });
        element.classList.add('ring-2', 'ring-blue-500');
    }
    
    // Quantity buttons
    const quantityInput = document.querySelector('.quantity-input');
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    
    if (minusBtn && plusBtn && quantityInput) {
        minusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        plusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.getAttribute('max'));
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
    }
    
    // Tabs
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-tab');
            
            // Update active tab button
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('text-gray-400');
                btn.classList.remove('text-white');
            });
            button.classList.add('active');
            button.classList.add('text-white');
            button.classList.remove('text-gray-400');
            
            // Show active tab content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName).classList.remove('hidden');
        });
    });
    
    // Review stars
    const reviewStars = document.querySelectorAll('.review-star');
    reviewStars.forEach((star, index) => {
        star.addEventListener('click', () => {
            const rating = parseInt(star.getAttribute('data-rating'));
            document.getElementById('rating-input').value = rating;
            
            reviewStars.forEach((s, i) => {
                if (i < rating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.add('far');
                    s.classList.remove('fas');
                    s.classList.remove('text-yellow-400');
                }
            });
        });
    });
    
    // Add to cart functionality
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const cartModal = document.getElementById('cart-modal');
    const cartModalMessage = document.getElementById('cart-modal-message');
    const continueShoppingBtn = document.getElementById('continue-shopping');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            const itemId = addToCartBtn.getAttribute('data-item-id');
            const quantity = document.getElementById('product-quantity').value;
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartModalMessage.textContent = 'Item added to cart!';
                    cartModal.classList.remove('hidden');
                } else {
                    cartModalMessage.textContent = data.message;
                    cartModal.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                cartModalMessage.textContent = 'Error adding item to cart. Please try again.';
                cartModal.classList.remove('hidden');
            });
        });
    }
    
    // Buy now functionality
    const buyNowBtn = document.getElementById('buy-now-btn');
    
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', () => {
            const itemId = buyNowBtn.getAttribute('data-item-id');
            const quantity = document.getElementById('product-quantity').value;
            
            fetch('{{ route("order.buyNow") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("user.orders") }}';
                } else {
                    cartModalMessage.textContent = data.message;
                    cartModal.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                cartModalMessage.textContent = 'Error processing order. Please try again.';
                cartModal.classList.remove('hidden');
            });
        });
    }
    
    // Close modal
    if (continueShoppingBtn) {
        continueShoppingBtn.addEventListener('click', () => {
            cartModal.classList.add('hidden');
        });
    }
    
    // Add review functionality
    const reviewForm = document.getElementById('review-form');
    
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const itemId = this.getAttribute('data-item-id');
            const rating = document.getElementById('rating-input').value;
            const comment = document.getElementById('review-text').value;
            
            if (rating === '0') {
                alert('Please select a rating');
                return;
            }
            
            if (!comment.trim()) {
                alert('Please enter a review comment');
                return;
            }
            
            fetch(`/items/${itemId}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    rating: rating,
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Clear form
                    document.getElementById('rating-input').value = '0';
                    document.getElementById('review-text').value = '';
                    
                    // Reset stars
                    reviewStars.forEach(s => {
                        s.classList.add('far');
                        s.classList.remove('fas');
                        s.classList.remove('text-yellow-400');
                    });
                    
                    // Reload page to show the new review
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting review. Please try again.');
            });
        });
    }
    
    // Related products add to cart
    document.querySelectorAll('.related-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const itemId = this.getAttribute('data-item-id');
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartModalMessage.textContent = 'Item added to cart!';
                    cartModal.classList.remove('hidden');
                } else {
                    cartModalMessage.textContent = data.message;
                    cartModal.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                cartModalMessage.textContent = 'Error adding item to cart. Please try again.';
                cartModal.classList.remove('hidden');
            });
        });
    });
</script>
@endpush 