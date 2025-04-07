@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

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
                
                <form id="editProductForm" action="{{ route('admin.items.update', $item->item_id) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="item_name" class="block text-sm font-medium text-gray-400 mb-1">Product Name</label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $item->item_name) }}" required
                               class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="error-message text-red-500 text-xs hidden" id="item_name_error"></span>
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
                        <span class="error-message text-red-500 text-xs hidden" id="group_id_error"></span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-400 mb-1">Price (₱)</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $item->price) }}" min="0" step="0.01" required
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="error-message text-red-500 text-xs hidden" id="price_error"></span>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-400 mb-1">Quantity in Stock</label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $item->inventory ? $item->inventory->quantity : 0) }}" min="0" required
                                   class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="error-message text-red-500 text-xs hidden" id="quantity_error"></span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="item_description" class="block text-sm font-medium text-gray-400 mb-1">Description</label>
                        <textarea name="item_description" id="item_description" rows="5" required
                                  class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('item_description', $item->item_description) }}</textarea>
                        <span class="error-message text-red-500 text-xs hidden" id="item_description_error"></span>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Current Images</label>
                        
                        @if($item->images->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($item->images as $image)
                            <div class="relative group">
                                @if(Storage::disk('public')->exists($image->image_path))
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->item_name }}" class="w-full h-24 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-24 bg-gray-700 rounded-lg flex flex-col items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-1"></i>
                                        <span class="text-xs text-gray-300">Missing Image</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <form action="{{ url('/admin/edit-item-images/' . $image->image_id) }}" method="POST" class="delete-image-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs p-1 rounded">
                                            <i class="fas fa-trash mr-1"></i> Remove
                                        </button>
                                    </form>
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
                            <span class="error-message text-red-500 text-xs hidden" id="images_error"></span>
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editProductForm');
        
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
                        // New images are optional on edit
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
    });

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
    
    // Image deletion using Fetch API instead of form submission
    document.querySelectorAll('.delete-image-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formAction = this.getAttribute('action');
            console.log('Attempting to delete image with action URL:', formAction);
            
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                // Get the CSRF token
                const csrfToken = document.querySelector('input[name="_token"]').value;
                
                // Create and use a fetch request instead of form.submit()
                fetch(formAction, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        // Success - reload the page
                        window.location.reload();
                    } else {
                        // Error - show alert
                        alert('Failed to delete image');
                        console.error('Server returned error status:', response.status);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting image: ' + error.message);
                });
            }
        });
    });
</script>
@endpush
@endsection 