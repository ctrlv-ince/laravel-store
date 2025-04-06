@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-box text-blue-500 mr-2"></i>
    Products
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-white">All Products</h1>
            <div class="flex space-x-4">
                <!-- Filter button -->
                <button type="button" id="filter-button" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                
                <!-- Search form -->
                <form class="flex items-center">
                    <input type="text" name="search" placeholder="Search products..." class="rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-700 px-4 py-2 bg-gray-800 text-white">
                    <button type="submit" class="px-4 py-2 rounded-r-md bg-blue-600 text-white hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Filter panel (hidden by default) -->
        <div id="filter-panel" class="mb-8 bg-gray-800 p-6 rounded-lg shadow-lg {{ request()->has('min_price') || request()->has('max_price') || request()->has('groups') ? '' : 'hidden' }}">
            <form action="{{ route('items.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Price range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Price Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="min_price" placeholder="Min" min="0" step="any" 
                                value="{{ request('min_price') }}" 
                                class="rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-700 px-4 py-2 bg-gray-700 text-white">
                            <span class="text-white">-</span>
                            <input type="number" name="max_price" placeholder="Max" min="0" step="any" 
                                value="{{ request('max_price') }}" 
                                class="rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-700 px-4 py-2 bg-gray-700 text-white">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no price limit</p>
                    </div>
                    
                    <!-- Categories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Categories</label>
                        <div class="grid grid-cols-2 gap-2 overflow-y-auto max-h-32">
                            @foreach($groups as $group)
                            <div class="flex items-center">
                                <input type="checkbox" name="groups[]" value="{{ $group->group_id }}" 
                                    id="group-{{ $group->group_id }}" 
                                    {{ is_array(request('groups')) && in_array($group->group_id, request('groups')) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-700">
                                <label for="group-{{ $group->group_id }}" class="ml-2 text-sm text-gray-300">
                                    {{ $group->group_name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mr-2">
                            Apply Filters
                        </button>
                        <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($items as $item)
            <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-lg transition-shadow duration-300">
                <a href="{{ route('items.show', $item) }}">
                    @if($item->images && $item->images->isNotEmpty())
                    <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                         alt="{{ $item->item_name }}" 
                         class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-image text-gray-500 text-4xl"></i>
                    </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-white font-semibold text-lg mb-2">{{ $item->item_name }}</h3>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $item->item_description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-white font-bold">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    </div>
                </a>
                <div class="px-4 pb-4 -mt-2">
                    <button class="add-to-cart-btn w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm"
                            data-item-id="{{ $item->item_id }}">
                        <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-box-open text-gray-600 text-5xl mb-4"></i>
                <p class="text-gray-400 text-lg">No products found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('filter-button').addEventListener('click', function() {
        const filterPanel = document.getElementById('filter-panel');
        filterPanel.classList.toggle('hidden');
    });
    
    // Add to cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
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
                        alert('Item added to cart!');
                    } else {
                        alert(data.message || 'Error adding item to cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding item to cart. Please try again.');
                });
            });
        });
    });
</script>
@endpush