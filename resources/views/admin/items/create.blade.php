@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
    Add New Product
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('admin.items.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Create New Product</h2>
                
                @if(session('error'))
                <div class="bg-red-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                
                @if($errors->any())
                <div class="bg-red-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                        <div>
                            <p class="font-bold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <form id="createProductForm" action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="item_name" class="block text-sm font-medium text-gray-400 mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" required
                               class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('item_name') border-red-500 @enderror">
                        @error('item_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <span class="error-message text-red-500 text-xs hidden" id="item_name_error"></span>
                    </div>
                    
                    <div class="mb-6">
                        <label for="group_id" class="block text-sm font-medium text-gray-400 mb-1">Category</label>
                        <select name="group_id" id="group_id" 
                                class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('group_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->group_id }}" {{ old('group_id') == $group->group_id ? 'selected' : '' }}>
                                {{ $group->group_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('group_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <span class="error-message text-red-500 text-xs hidden" id="group_id_error"></span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-400 mb-1">Price (₱) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <span class="error-message text-red-500 text-xs hidden" id="price_error"></span>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-400 mb-1">Quantity in Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="0" required
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">
                            @error('quantity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <span class="error-message text-red-500 text-xs hidden" id="quantity_error"></span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="item_description" class="block text-sm font-medium text-gray-400 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="item_description" id="item_description" rows="5" required
                                  class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('item_description') border-red-500 @enderror">{{ old('item_description') }}</textarea>
                        @error('item_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <span class="error-message text-red-500 text-xs hidden" id="item_description_error"></span>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Product Images</label>
                        <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 @error('images.*') border-red-500 @enderror">
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
                            @error('images.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <span class="error-message text-red-500 text-xs hidden" id="images_error"></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createProductForm');
        const fileInput = document.getElementById('images');
        const previewContainer = document.getElementById('preview-container');
        
        // Validation rules
        const validationRules = {
            item_name: {
                validate: (value) => {
                    if (!value || value.trim() === '') return 'Product name is required';
                    if (value.length > 255) return 'Product name cannot exceed 255 characters';
                    return '';
                }
            },
            group_id: {
                validate: (value) => {
                    // Category is optional
                    return '';
                }
            },
            price: {
                validate: (value) => {
                    if (!value) return 'Price is required';
                    const price = parseFloat(value);
                    if (isNaN(price) || price < 0) return 'Please enter a valid price (must be 0 or higher)';
                    return '';
                }
            },
            quantity: {
                validate: (value) => {
                    if (!value && value !== '0') return 'Quantity is required';
                    const quantity = parseInt(value);
                    if (isNaN(quantity) || quantity < 0) return 'Please enter a valid quantity (must be 0 or higher)';
                    return '';
                }
            },
            item_description: {
                validate: (value) => {
                    if (!value || value.trim() === '') return 'Description is required';
                    return '';
                }
            },
            images: {
                validate: (fileInput) => {
                    if (!fileInput.files || fileInput.files.length === 0) {
                        // Images are optional
                        return '';
                    }
                    
                    const files = fileInput.files;
                    const validFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    const maxFileSize = 2 * 1024 * 1024; // 2MB
                    
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        
                        if (!validFileTypes.includes(file.type)) {
                            return `File "${file.name}" is not a valid image type. Please upload JPG, PNG, or GIF files only.`;
                        }
                        
                        if (file.size > maxFileSize) {
                            return `File "${file.name}" exceeds the maximum file size of 2MB.`;
                        }
                    }
                    
                    return '';
                }
            }
        };
        
        // Add event listeners for real-time validation
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field) return;
            
            // For file inputs, only validate on change
            if (fieldName === 'images') {
                field.addEventListener('change', () => {
                    validateField(field);
                    updateImagePreviews(field);
                });
                return;
            }
            
            field.addEventListener('input', () => {
                validateField(field);
            });
            
            field.addEventListener('blur', () => {
                validateField(field);
            });
        });
        
        // Field validation function
        function validateField(field) {
            const fieldName = field.id;
            const errorElement = document.getElementById(`${fieldName}_error`);
            
            if (!validationRules[fieldName] || !errorElement) return true;
            
            let errorMsg;
            if (fieldName === 'images') {
                errorMsg = validationRules[fieldName].validate(field);
            } else {
                errorMsg = validationRules[fieldName].validate(field.value);
            }
            
            if (errorMsg) {
                errorElement.textContent = errorMsg;
                errorElement.classList.remove('hidden');
                field.classList.add('border-red-500');
                return false;
            } else {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
                field.classList.remove('border-red-500');
                return true;
            }
        }
        
        // Image preview function
        function updateImagePreviews(fileInput) {
            previewContainer.innerHTML = '';
            
            if (!fileInput.files || fileInput.files.length === 0) {
                return;
            }
            
            const files = fileInput.files;
            
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
        }
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all fields
            Object.keys(validationRules).forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field) return;
                
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to the first error
                const firstError = document.querySelector('.error-message:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        // Initialize image previews if there are files selected (e.g. after form validation failure)
        if (fileInput.files && fileInput.files.length > 0) {
            updateImagePreviews(fileInput);
        }
    });
</script>
@endpush
@endsection 