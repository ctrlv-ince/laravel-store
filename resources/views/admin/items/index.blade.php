@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-box text-blue-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Product Management</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">Products</h2>
                
                @if(session('success'))
                <div class="bg-green-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                
                <div class="bg-gray-900 p-4 mb-6 rounded-lg text-gray-300 shadow-inner">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        <p>Manage your product inventory, prices, and categories. Add new products or import them from Excel.</p>
                    </div>
                </div>
                
                <div class="flex items-center mb-6 space-x-3">
                    <a href="{{ route('admin.items.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Add New Product
                    </a>
                    <button id="import-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-file-import mr-2"></i> Import Products
                    </button>
                    <a href="{{ route('admin.items.trash') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i> View Deleted ({{ \App\Models\Item::onlyTrashed()->count() }})
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="items-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <!-- Table content will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Confirm Delete</h3>
        <p class="text-gray-300 mb-6">Are you sure you want to delete this product? This action can be reversed by visiting the trash.</p>
        <div class="flex justify-end">
            <button id="cancel-delete" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">
                Cancel
            </button>
            <button id="confirm-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </div>
    </div>
</div>

<!-- Import Products Modal -->
<div id="import-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Import Products</h3>
        <form action="{{ route('admin.items.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="import_file" class="block text-sm font-medium text-gray-400 mb-2">
                    Excel File (XLSX, XLS, CSV)
                </label>
                <input type="file" name="import_file" id="import_file" accept=".xlsx,.xls,.csv" required
                       class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2">
                <p class="mt-2 text-sm text-gray-400 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i> 
                    File should have columns: product_name, description, price, quantity, category
                </p>
            </div>
            <div class="flex justify-end">
                <button type="button" id="cancel-import" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-file-import mr-2"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<style>
    /* Custom DataTable styling for better readability */
    #items-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #items-table thead th {
        background-color: #1e293b; /* darker blue-gray */
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #items-table tbody tr {
        background-color: #1f2937; /* dark blue-gray */
    }
    
    #items-table tbody tr:nth-child(even) {
        background-color: #111827; /* darker for alternating rows */
    }
    
    #items-table tbody tr:hover {
        background-color: #374151; /* highlight on hover */
    }
    
    #items-table td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #4b5563;
    }
    
    /* Pagination styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: white !important;
        background-color: #374151;
        border-radius: 4px;
        margin: 2px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #4b5563 !important;
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    
    /* Search box styling */
    .dataTables_wrapper .dataTables_filter input {
        background-color: #1f2937;
        color: white;
        border: 1px solid #4b5563;
        border-radius: 4px;
        padding: 5px 10px;
        margin-left: 5px;
    }
    
    /* Action buttons styling */
    .btn-view, .btn-edit, .btn-delete {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .btn-view {
        background-color: #3b82f6;
        color: white;
    }
    
    .btn-view:hover {
        background-color: #2563eb;
    }
    
    .btn-edit {
        background-color: #f59e0b;
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #d97706;
    }
    
    .btn-delete {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #dc2626;
    }
    
    /* DataTable info and length styling */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter, 
    .dataTables_wrapper .dataTables_info {
        color: #d1d5db !important;
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select {
        background-color: #1f2937;
        color: white;
        border: 1px solid #4b5563;
        border-radius: 4px;
        padding: 5px;
    }
</style>

<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize DataTable
    let table = $('#items-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.items.index') }}",
            error: function(xhr, error, thrown) {
                console.log('DataTables error:', error, thrown);
                console.log('XHR:', xhr.responseText);
            }
        },
        columns: [
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'item_name', name: 'item_name' },
            { data: 'category', name: 'category' },
            { 
                data: 'price', 
                name: 'price',
                render: function(data) {
                    return '₱' + parseFloat(data).toFixed(2);
                }
            },
            { data: 'stock', name: 'stock', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
    
    // Delete product functionality
    let deleteItemId = null;
    
    // Show delete modal
    $(document).on('click', '.delete-btn', function() {
        deleteItemId = $(this).data('id');
        $('#delete-modal').removeClass('hidden');
    });
    
    // Hide delete modal
    $('#cancel-delete').on('click', function() {
        $('#delete-modal').addClass('hidden');
        deleteItemId = null;
    });
    
    // Confirm delete
    $('#confirm-delete').on('click', function() {
        if (deleteItemId) {
            $.ajax({
                url: "{{ url('admin/items') }}/" + deleteItemId,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        table.ajax.reload();
                    } else {
                        showNotification('Error: ' + response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred while deleting the product.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showNotification('Error: ' + errorMsg, 'error');
                },
                complete: function() {
                    $('#delete-modal').addClass('hidden');
                    deleteItemId = null;
                }
            });
        }
    });
    
    // Notification function
    function showNotification(message, type) {
        const notification = $('#notification');
        const icon = $('#notification-icon');
        const messageSpan = $('#notification-message');
        
        notification.removeClass('bg-green-500 bg-red-500');
        icon.removeClass('fa-check-circle fa-times-circle');
        
        if (type === 'success') {
            notification.addClass('bg-green-500 text-white');
            icon.addClass('fa-check-circle');
        } else {
            notification.addClass('bg-red-500 text-white');
            icon.addClass('fa-times-circle');
        }
        
        messageSpan.text(message);
        const notificationArea = $('#notification-area');
        notificationArea.removeClass('hidden').addClass('visible');
        
        // Hide after 3 seconds
        setTimeout(function() {
            notificationArea.removeClass('visible');
            setTimeout(function() {
                notificationArea.addClass('hidden');
            }, 300);
        }, 3000);
    }
    
    // Import Products Modal
    $('#import-btn').on('click', function() {
        $('#import-modal').removeClass('hidden');
    });
    
    $('#cancel-import').on('click', function() {
        $('#import-modal').addClass('hidden');
    });
    
    // Close Import Modal when clicking outside
    $(window).on('click', function(event) {
        if ($(event.target).is('#import-modal')) {
            $('#import-modal').addClass('hidden');
        }
    });
});
</script>
@endpush
@endsection 