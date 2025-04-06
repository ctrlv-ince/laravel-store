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
                        @if($item->images->isNotEmpty())
                        <div class="mb-6">
                            <div id="product-gallery" class="mb-2">
                                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                                    alt="{{ $item->item_name }}" 
                                    class="w-full h-64 object-contain rounded-lg bg-gray-900">
                            </div>
                            
                            @if($item->images->count() > 1)
                            <div class="grid grid-cols-5 gap-2">
                                @foreach($item->images as $image)
                                <div class="cursor-pointer">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                        alt="{{ $item->item_name }}" 
                                        class="w-full h-16 object-cover rounded-lg hover:opacity-80 transition-opacity gallery-thumb"
                                        data-img="{{ asset('storage/' . $image->image_path) }}">
                                </div>
                                @endforeach
                            </div>
                            @endif
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
    // Image gallery
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');
    const mainImage = document.querySelector('#product-gallery img');
    
    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            mainImage.src = this.getAttribute('data-img');
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