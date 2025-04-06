@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-trash text-red-500 mr-2"></i>
    Deleted Products
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Notification area -->
        <div id="notification-area" class="mb-4 hidden">
            <div id="notification" class="p-4 rounded shadow-md flex items-center">
                <span class="mr-2 text-lg">
                    <i id="notification-icon" class="fas"></i>
                </span>
                <span id="notification-message"></span>
            </div>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Deleted Products</h2>
                    <a href="{{ route('admin.items.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Products
                    </a>
                </div>
                
                @if(session('success'))
                <div class="bg-green-500 text-white p-4 mb-6 rounded">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-500 text-white p-4 mb-6 rounded">
                    {{ session('error') }}
                </div>
                @endif
                
                <!-- Debug info about trashed items -->
                <div class="bg-gray-700 p-4 mb-6 rounded">
                    <p class="text-white">Found {{ $trashedCount ?? 0 }} deleted products.</p>
                    @if(($trashedCount ?? 0) == 0)
                        <p class="text-gray-300 mt-2">No deleted products to display. When you delete products, they will appear here.</p>
                    @endif
                </div>
                
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow">
                    <div class="overflow-x-auto">
                        <table id="trashed-items-table" class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Deleted At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <!-- Table content will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Force Delete Confirmation Modal -->
<div id="force-delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold text-white mb-4">Confirm Permanent Deletion</h3>
        <p class="text-gray-300 mb-2">Are you sure you want to permanently delete this product?</p>
        <p class="text-red-500 mb-6"><i class="fas fa-exclamation-triangle mr-2"></i>This action cannot be undone!</p>
        <div class="flex justify-end space-x-4">
            <button id="cancel-force-delete" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Cancel
            </button>
            <button id="confirm-force-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                Delete Permanently
            </button>
        </div>
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
    /* Custom DataTables styling */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter, 
    .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_processing, 
    .dataTables_wrapper .dataTables_paginate {
        color: #cbd5e0 !important;
        margin-bottom: 15px;
        padding: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select, 
    .dataTables_wrapper .dataTables_filter input {
        background-color: #2d3748;
        color: white;
        border: 1px solid #4a5568;
        border-radius: 0.375rem;
        padding: 0.5rem;
        margin-left: 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #cbd5e0 !important;
        border: 1px solid transparent;
        background-color: #2d3748;
        margin: 0 2px;
        border-radius: 0.375rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #4299e1 !important;
        color: white !important;
        border-color: #4299e1;
    }
</style>

<script>
    let itemToDelete = null;
    
    $(document).ready(function() {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize DataTable
        let table = $('#trashed-items-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('admin.items.trash') }}",
            columns: [
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'item_name', name: 'item_name' },
                { data: 'category', name: 'category' },
                { data: 'deleted_at', name: 'deleted_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        
        // Restore Product
        $(document).on('click', '.restore-btn', function() {
            const itemId = $(this).data('id');
            
            $.ajax({
                url: `/admin/items/${itemId}/restore`,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        table.ajax.reload();
                    } else {
                        showNotification('Error: ' + response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred while restoring the product.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showNotification('Error: ' + errorMsg, 'error');
                }
            });
        });
        
        // Show force delete modal
        $(document).on('click', '.force-delete-btn', function() {
            itemToDelete = $(this).data('id');
            $('#force-delete-modal').removeClass('hidden');
        });
        
        // Cancel force delete
        $('#cancel-force-delete').on('click', function() {
            $('#force-delete-modal').addClass('hidden');
            itemToDelete = null;
        });
        
        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#force-delete-modal')) {
                $('#force-delete-modal').addClass('hidden');
                itemToDelete = null;
            }
        });
        
        // Confirm force delete
        $('#confirm-force-delete').on('click', function() {
            if (itemToDelete) {
                $.ajax({
                    url: `/admin/items/${itemToDelete}/force-delete`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                            table.ajax.reload();
                        } else {
                            showNotification('Error: ' + response.message, 'error');
                        }
                        $('#force-delete-modal').addClass('hidden');
                        itemToDelete = null;
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while deleting the product.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showNotification('Error: ' + errorMsg, 'error');
                        $('#force-delete-modal').addClass('hidden');
                        itemToDelete = null;
                    }
                });
            }
        });
        
        // Show notification
        function showNotification(message, type) {
            const notification = $('#notification');
            const notificationIcon = $('#notification-icon');
            const notificationArea = $('#notification-area');
            const notificationMessage = $('#notification-message');
            
            if (type === 'success') {
                notification.removeClass('bg-red-500').addClass('bg-green-500');
                notificationIcon.removeClass('fa-times-circle').addClass('fa-check-circle');
            } else {
                notification.removeClass('bg-green-500').addClass('bg-red-500');
                notificationIcon.removeClass('fa-check-circle').addClass('fa-times-circle');
            }
            
            notificationMessage.text(message);
            notificationArea.removeClass('hidden').addClass('visible');
            
            setTimeout(function() {
                notificationArea.removeClass('visible').addClass('hidden');
            }, 3000);
        }
    });
</script>
@endpush
@endsection 