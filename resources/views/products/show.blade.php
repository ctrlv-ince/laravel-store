<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}
            </h2>
            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Products
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Image -->
                        <div class="flex justify-center items-start">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->item_name }}" 
                                    class="max-w-full h-auto rounded-lg shadow-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500">No image available</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $product->item_name }}</h3>
                                <p class="mt-2 text-sm text-gray-500">Category: {{ $product->group_name }}</p>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-lg font-semibold text-gray-900">Description</h4>
                                <p class="mt-2 text-gray-600">{{ $product->item_description }}</p>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Price</h4>
                                        <p class="mt-1 text-2xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Stock</h4>
                                        <p class="mt-1 text-2xl font-bold {{ $product->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $product->quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 flex justify-end space-x-3">
                                <a href="{{ route('products.edit', $product->item_id) }}" 
                                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Product
                                </a>
                                <form action="{{ route('products.destroy', $product->item_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                        Delete Product
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 