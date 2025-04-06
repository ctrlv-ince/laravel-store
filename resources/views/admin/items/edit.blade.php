@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-edit text-blue-500 mr-2"></i>
    Edit Product
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.items.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
            <a href="{{ route('admin.items.show', $item->item_id) }}" class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-eye mr-2"></i> View Product
            </a>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Edit Product: {{ $item->item_name }}</h2>
                
                @if($errors->any())
                <div class="bg-red-500 text-white p-4 mb-6 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form action="{{ route('admin.items.update', $item->item_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="item_name" class="block text-sm font-medium text-gray-400 mb-1">Product Name</label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $item->item_name) }}" 
                               class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-6">
                        <label for="group_id" class="block text-sm font-medium text-gray-400 mb-1">Category</label>
                        <select name="group_id" id="group_id" 
                                class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->group_id }}" {{ old('group_id', $item->groups->contains($group->group_id) ? $group->group_id : '') == $group->group_id ? 'selected' : '' }}>
                                {{ $group->group_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-400 mb-1">Price (₱)</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $item->price) }}" min="0" step="0.01" 
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-400 mb-1">Quantity in Stock</label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $item->inventory ? $item->inventory->quantity : 0) }}" min="0" 
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="item_description" class="block text-sm font-medium text-gray-400 mb-1">Description</label>
                        <textarea name="item_description" id="item_description" rows="5" 
                                  class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('item_description', $item->item_description) }}</textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Current Images</label>
                        
                        @if($item->images->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($item->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->item_name }}" class="w-full h-24 object-cover rounded-lg">
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <div class="flex space-x-2">
                                        <label class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white text-xs p-1 rounded">
                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="hidden">
                                            <i class="fas fa-trash"></i> Remove
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 italic">No images available</p>
                        @endif
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Add New Images</label>
                        <div class="border-2 border-dashed border-gray-600 rounded-lg p-6">
                            <div class="flex items-center justify-center">
                                <label for="images" class="cursor-pointer">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-sm text-gray-400">Drag and drop images here or click to browse</p>
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF up to 2MB</p>
                                    </div>
                                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                                </label>
                            </div>
                            
                            <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // For new image preview
    document.getElementById('images').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        
        const files = e.target.files;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if (!file.type.match('image.*')) {
                continue;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'relative';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-24 object-cover rounded-lg';
                
                previewItem.appendChild(img);
                previewContainer.appendChild(previewItem);
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // For delete image checkboxes
    const deleteCheckboxes = document.querySelectorAll('input[name="delete_images[]"]');
    
    deleteCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            
            if (this.checked) {
                label.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                label.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                label.classList.remove('bg-red-500', 'hover:bg-red-600');
                label.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }
        });
    });
</script>
@endpush
@endsection 