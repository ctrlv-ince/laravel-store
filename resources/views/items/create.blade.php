@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <a href="{{ route('items.index') }}" class="mr-4 text-gray-400 hover:text-blue-500 transition-colors duration-150">
        <i class="fas fa-arrow-left"></i>
    </a>
    <i class="fas fa-box text-blue-500 mr-2"></i>
    <span class="mr-2">Products</span>
    <i class="fas fa-chevron-right text-xs text-gray-600 mx-2"></i>
    <span class="text-gray-400">Add New Product</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-semibold text-white mb-6">Add New Product</h1>
                
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Name -->
                        <div>
                            <label for="item_name" class="block text-sm font-medium text-gray-400 mb-2">Product Name <span class="text-red-500">*</span></label>
                            <input id="item_name" type="text" name="item_name" value="{{ old('item_name') }}" 
                                   class="bg-gray-700 border border-gray-600 text-white rounded-md w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
                            @error('item_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-400 mb-2">Price <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">₱</span>
                                <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" 
                                       class="bg-gray-700 border border-gray-600 text-white rounded-md w-full p-2.5 pl-7 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Stock Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-400 mb-2">Stock Quantity <span class="text-red-500">*</span></label>
                            <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity', 0) }}" 
                                   class="bg-gray-700 border border-gray-600 text-white rounded-md w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Categories <span class="text-red-500">*</span></label>
                            <div class="bg-gray-700 border border-gray-600 rounded-md px-3 py-2 h-[42px] flex items-center">
                                <button type="button" id="categories-button" class="text-white hover:text-blue-500 focus:outline-none flex items-center justify-between w-full">
                                    <span id="categories-selected">Select categories</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>
                            <div id="categories-dropdown" class="hidden mt-1 bg-gray-700 border border-gray-600 rounded-md shadow-lg overflow-hidden z-10 max-h-60 overflow-y-auto">
                                <div class="p-2 space-y-1">
                                    @foreach($groups as $group)
                                    <div class="flex items-center p-2 hover:bg-gray-600 rounded">
                                        <input type="checkbox" name="groups[]" id="group-{{ $group->group_id }}" value="{{ $group->group_id }}"
                                               class="h-4 w-4 text-blue-500 border-gray-500 rounded focus:ring-blue-500 focus:ring-opacity-50"
                                               {{ in_array($group->group_id, old('groups', [])) ? 'checked' : '' }}>
                                        <label for="group-{{ $group->group_id }}" class="ml-2 text-sm text-white">{{ $group->group_name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="bg-gray-800 p-2 border-t border-gray-600">
                                    <button type="button" id="categories-done" class="w-full py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded">
                                        Done
                                    </button>
                                </div>
                            </div>
                            @error('groups')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="item_description" class="block text-sm font-medium text-gray-400 mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea id="item_description" name="item_description" rows="5" 
                                  class="bg-gray-700 border border-gray-600 text-white rounded-md w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">{{ old('item_description') }}</textarea>
                        @error('item_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Product Images -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Product Images</label>
                        <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center" id="image-drop-area">
                            <input type="file" name="images[]" id="image-upload" class="hidden" accept="image/*" multiple>
                            <label for="image-upload" class="flex flex-col items-center justify-center cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-400 mb-1">Drag and drop images here or click to upload</p>
                                <p class="text-gray-500 text-sm">PNG, JPG, GIF up to 2MB</p>
                            </label>
                        </div>
                        <div id="image-preview-container" class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                        @error('images.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Categories dropdown
    const categoriesButton = document.getElementById('categories-button');
    const categoriesDropdown = document.getElementById('categories-dropdown');
    const categoriesDone = document.getElementById('categories-done');
    const categoriesSelected = document.getElementById('categories-selected');
    const checkboxes = document.querySelectorAll('input[name="groups[]"]');
    
    categoriesButton.addEventListener('click', () => {
        categoriesDropdown.classList.toggle('hidden');
    });
    
    categoriesDone.addEventListener('click', () => {
        const selected = Array.from(checkboxes).filter(c => c.checked);
        if (selected.length > 0) {
            categoriesSelected.textContent = `${selected.length} categories selected`;
        } else {
            categoriesSelected.textContent = 'Select categories';
        }
        categoriesDropdown.classList.add('hidden');
    });
    
    // Initially update the categories count
    const initialSelected = Array.from(checkboxes).filter(c => c.checked);
    if (initialSelected.length > 0) {
        categoriesSelected.textContent = `${initialSelected.length} categories selected`;
    }
    
    // Image upload preview
    const imageUpload = document.getElementById('image-upload');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const dropArea = document.getElementById('image-drop-area');
    
    // Handle drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropArea.classList.add('border-blue-500');
    }
    
    function unhighlight() {
        dropArea.classList.remove('border-blue-500');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    imageUpload.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        imagePreviewContainer.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative bg-gray-700 rounded overflow-hidden h-32';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-full object-contain';
                
                div.appendChild(img);
                imagePreviewContainer.appendChild(div);
            }
            
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush 