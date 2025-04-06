@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-shopping-cart text-blue-500 mr-2"></i>
    <span>Shopping Cart</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-white mb-6">Your Cart</h2>
                
                <div id="cart-items-container">
                    @if(isset($cartItems) && count($cartItems) > 0)
                        @foreach($cartItems as $item)
                        <div class="cart-item border-b border-gray-700 py-6 flex flex-col md:flex-row items-center">
                            <div class="flex-shrink-0 w-24 h-24 bg-gray-700 rounded overflow-hidden mb-4 md:mb-0">
                                @if($item->item->images && count($item->item->images) > 0)
                                    <img class="w-full h-full object-cover" src="/storage/{{ $item->item->images[0]->image_path }}" alt="{{ $item->item->item_name }}">
                                @else
                                    <img class="w-full h-full object-cover" src="/images/placeholder.png" alt="{{ $item->item->item_name }}">
                                @endif
                            </div>
                            <div class="md:ml-6 flex-grow">
                                <h3 class="text-white font-medium text-lg mb-1">{{ $item->item->item_name }}</h3>
                                <p class="text-gray-400 text-sm mb-4">₱{{ number_format($item->item->price, 2) }}</p>
                                <div class="flex items-center">
                                    <button class="quantity-btn minus bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded-l flex items-center justify-center" data-item-id="{{ $item->item_id }}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input w-14 h-8 bg-gray-700 text-white text-center focus:outline-none" min="1" value="{{ $item->quantity }}" data-item-id="{{ $item->item_id }}" @if($item->item->inventory) max="{{ $item->item->inventory->quantity }}" @endif>
                                    <button class="quantity-btn plus bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded-r flex items-center justify-center" data-item-id="{{ $item->item_id }}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    @if($item->item->inventory)
                                        <span class="text-gray-400 text-sm ml-4">{{ $item->item->inventory->quantity }} available</span>
                                    @endif
                                </div>
                            </div>
                            <div class="md:ml-6 text-right mt-4 md:mt-0">
                                <p class="text-white font-bold text-lg mb-2">₱{{ number_format($item->item->price * $item->quantity, 2) }}</p>
                                <button class="remove-item text-red-400 hover:text-red-300 flex items-center justify-center" data-item-id="{{ $item->item_id }}">
                                    <i class="fas fa-trash-alt mr-2"></i> Remove
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <i class="fas fa-shopping-cart text-gray-600 text-6xl mb-4"></i>
                            <p class="text-gray-400 text-xl mb-6">Your cart is empty</p>
                            <a href="{{ route('items.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md font-medium">
                                <i class="fas fa-box mr-2"></i> Browse Products
                            </a>
                        </div>
                    @endif
                </div>
                
                @if(isset($cartItems) && count($cartItems) > 0)
                <div id="cart-summary" class="mt-8 border-t border-gray-700 pt-6">
                @else
                <div id="cart-summary" class="hidden mt-8 border-t border-gray-700 pt-6">
                @endif
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-300 font-medium">Subtotal</span>
                        <span class="text-white font-bold text-xl">₱{{ isset($subtotal) ? number_format($subtotal, 2) : '0.00' }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-gray-300 font-medium">Shipping</span>
                        <span class="text-white">Free</span>
                    </div>
                    <div class="flex justify-between items-center mb-6 border-t border-gray-700 pt-4">
                        <span class="text-gray-300 font-medium text-lg">Total</span>
                        <span class="text-white font-bold text-2xl">₱{{ isset($subtotal) ? number_format($subtotal, 2) : '0.00' }}</span>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('items.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white py-3 px-6 rounded-md font-medium">
                            Continue Shopping
                        </a>
                        <button id="checkout-btn" class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-md font-medium">
                            <i class="fas fa-lock mr-2"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove item confirmation modal -->
<div id="remove-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full">
        <h3 class="text-xl font-bold text-white mb-4">Remove Item</h3>
        <p class="text-gray-300 mb-6">Are you sure you want to remove this item from your cart?</p>
        <div class="flex justify-end space-x-4">
            <button id="cancel-remove" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded">
                Cancel
            </button>
            <button id="confirm-remove" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded" data-item-id="">
                Remove
            </button>
        </div>
    </div>
</div>

<!-- Cart items are now rendered directly in the blade template -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup modal controls
        const removeModal = document.getElementById('remove-modal');
        const cancelRemoveBtn = document.getElementById('cancel-remove');
        const confirmRemoveBtn = document.getElementById('confirm-remove');
        
        cancelRemoveBtn.addEventListener('click', function() {
            removeModal.classList.add('hidden');
        });
        
        confirmRemoveBtn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            removeCartItem(itemId);
        });
        
        // Checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                window.location.href = '{{ route("checkout.index") }}';
            });
        }
        
        // Setup quantity controls for all items
        setupQuantityControls();
    });
    
    function setupQuantityControls() {
        // Get all quantity inputs
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        quantityInputs.forEach(input => {
            const itemId = input.getAttribute('data-item-id');
            const minusBtn = input.previousElementSibling;
            const plusBtn = input.nextElementSibling;
            
            // Setup minus button
            minusBtn.addEventListener('click', function() {
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateCartItemQuantity(itemId, input.value);
                }
            });
            
            // Setup plus button
            plusBtn.addEventListener('click', function() {
                const max = input.getAttribute('max');
                if (!max || parseInt(input.value) < parseInt(max)) {
                    input.value = parseInt(input.value) + 1;
                    updateCartItemQuantity(itemId, input.value);
                }
            });
            
            // Setup input change event
            input.addEventListener('change', function() {
                const value = parseInt(input.value);
                const max = parseInt(input.getAttribute('max'));
                
                if (value < 1) {
                    input.value = 1;
                } else if (max && value > max) {
                    input.value = max;
                }
                
                updateCartItemQuantity(itemId, input.value);
            });
        });
        
        // Setup remove buttons
        const removeButtons = document.querySelectorAll('.remove-item');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                showRemoveModal(itemId);
            });
        });
    }
    
    function updateCartItemQuantity(itemId, quantity) {
        fetch(`/cart/item/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to reflect changes
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error updating cart item:', error);
            alert('Failed to update cart item. Please try again.');
        });
    }
    
    function showRemoveModal(itemId) {
        const modal = document.getElementById('remove-modal');
        const confirmBtn = document.getElementById('confirm-remove');
        
        modal.classList.remove('hidden');
        confirmBtn.setAttribute('data-item-id', itemId);
    }
    
    function removeCartItem(itemId) {
        fetch(`/cart/item/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('remove-modal').classList.add('hidden');
                // Reload the page to reflect changes
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error removing cart item:', error);
            alert('Failed to remove cart item. Please try again.');
        });
    }
</script>
@endpush