@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Form -->
        <div class="mb-8">
            <form action="{{ route('search') }}" method="GET" class="flex gap-4">
                <input type="text" 
                       name="query" 
                       value="{{ $query }}" 
                       placeholder="Search products..." 
                       class="flex-1 rounded-md border-gray-700 bg-gray-800 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
            </form>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-white">
                {{ $items->total() }} results found for "{{ $query }}"
            </h2>
        </div>

        @if($items->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-600 text-5xl mb-4"></i>
                <p class="text-gray-400 text-lg">No products found matching your search.</p>
            </div>
        @else
            <!-- Results Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                    <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <a href="{{ route('items.show', $item) }}">
                            <div class="aspect-w-4 aspect-h-3 bg-gray-900">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                                         alt="{{ $item->item_name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center">
                                        <i class="fas fa-box text-gray-600 text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white mb-2">{{ $item->item_name }}</h3>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $item->item_description }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-white font-bold">${{ number_format($item->price, 2) }}</span>
                                    @if($item->inventory && $item->inventory->stock > 0)
                                        <span class="text-green-500 text-sm">In Stock ({{ $item->inventory->stock }})</span>
                                    @else
                                        <span class="text-red-500 text-sm">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 