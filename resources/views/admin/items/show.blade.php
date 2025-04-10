@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-box-open text-blue-500 mr-2"></i>
    Product Details
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.items.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
            <a href="{{ route('admin.items.edit', $item->item_id) }}" class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Product
            </a>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-6">{{ $item->item_name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        @if($item->images && $item->images->count() > 0)
                        <div class="mb-6">
                            <div id="product-gallery" class="mb-2">
                                @php
                                    $firstImage = $item->images->first();
                                    $imageExists = $firstImage && Storage::disk('public')->exists($firstImage->image_path);
                                @endphp
                                
                                @if($imageExists)
                                    <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                        alt="{{ $item->item_name }}" 
                                        class="w-full h-64 object-contain rounded-lg bg-gray-900">
                                @else
                                    <div class="w-full h-64 bg-gray-900 rounded-lg shadow-md flex flex-col items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                                        <span class="text-gray-300">Image file not found</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
                                @foreach($item->images as $image)
                                    <div class="relative group">
                                        @if(Storage::disk('public')->exists($image->image_path))
                                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                 alt="{{ $item->item_name }}" 
                                                 class="w-full h-32 object-cover rounded-lg shadow-md">
                                        @else
                                            <div class="w-full h-32 bg-gray-700 rounded-lg shadow-md flex flex-col items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-2"></i>
                                                <span class="text-xs text-gray-300">Missing Image</span>
                                            </div>
                                        @endif
                                        
                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                            <form action="{{ url('/admin/item-images/' . $image->image_id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs py-1 px-2 rounded-lg transition-colors duration-150">
                                                    <i class="fas fa-trash mr-1"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                        <div class="mb-6 flex items-center justify-center h-64 bg-gray-900 rounded-lg">
                            <span class="text-gray-500">
                                <i class="fas fa-image text-3xl"></i>
                                <p class="mt-2">No image available</p>
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div>
                        <div class="bg-gray-700 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-white mb-4">Product Information</h3>
                            
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <span class="text-gray-400 text-sm">Category:</span>
                                    <span class="text-white font-medium ml-2">{{ $item->groups->isNotEmpty() ? $item->groups->first()->group_name : 'Uncategorized' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-sm">Price:</span>
                                    <span class="text-white font-medium ml-2">₱{{ number_format($item->price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-sm">Stock:</span>
                                    <span class="text-white font-medium ml-2">
                                        {{ $item->inventory ? $item->inventory->quantity : 0 }} units
                                        @if($item->inventory && $item->inventory->quantity < 5)
                                        <span class="text-red-500 ml-2">(Low stock)</span>
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-sm">Created:</span>
                                    <span class="text-white font-medium ml-2">{{ $item->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-sm">Last Updated:</span>
                                    <span class="text-white font-medium ml-2">{{ $item->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-white mb-4">Description</h3>
                            <div class="text-gray-300">
                                {!! nl2br(e($item->item_description)) !!}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($item->reviews->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-white mb-4">
                        <i class="fas fa-star text-yellow-500 mr-1"></i> 
                        Product Reviews ({{ $item->reviews->count() }})
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($item->reviews as $review)
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-500">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < $review->rating)
                                            <i class="fas fa-star"></i>
                                            @else
                                            <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-white ml-2">{{ $review->account->user->name ?? 'Anonymous' }}</span>
                                </div>
                                <span class="text-gray-400 text-sm">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-gray-300">{{ $review->comment }}</p>
                            <div class="mt-2 flex justify-end">
                                <form action="{{ route('admin.reviews.destroy', $review->review_id) }}" method="POST" class="delete-review-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 text-sm">
                                        <i class="fas fa-trash mr-1"></i> Remove Review
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image gallery - update main image when clicking thumbnails
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnails = document.querySelectorAll('.gallery-thumb');
        const mainImageContainer = document.querySelector('#product-gallery');
        
        // Set click handlers for valid image thumbnails
        document.querySelectorAll('.w-full.h-32.object-cover').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const imageUrl = this.getAttribute('src');
                if (imageUrl) {
                    // Replace main image container content
                    mainImageContainer.innerHTML = `
                        <img src="${imageUrl}" 
                             alt="{{ $item->item_name }}" 
                             class="w-full h-64 object-contain rounded-lg bg-gray-900">
                    `;
                }
            });
        });
    });
    
    // Delete review confirmation
    document.querySelectorAll('.delete-review-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this review?')) {
                this.submit();
            }
        });
    });
</script>
@endpush
@endsection 