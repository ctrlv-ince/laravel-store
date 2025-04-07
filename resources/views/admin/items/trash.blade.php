@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-trash text-red-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Deleted Products</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Notification area -->
        <div id="notification-area" class="mb-4 hidden">
            <div id="notification" class="px-4 py-3 rounded-lg shadow-md flex items-center">
                <span class="mr-2 text-lg">
                    <i id="notification-icon" class="fas"></i>
                </span>
                <span id="notification-message"></span>
            </div>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">Trash - Deleted Products</h2>
                
                <div class="flex items-center mb-6">
                    <a href="{{ route('admin.items.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Products
                    </a>
                </div>
                
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
                
                <!-- Info box about trashed items -->
                <div class="bg-gray-900 p-4 mb-6 rounded-lg text-gray-300 shadow-inner">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        <p>Found {{ $trashedCount ?? 0 }} deleted products. You can restore items or permanently delete them.</p>
                    </div>
                    @if(($trashedCount ?? 0) == 0)
                        <p class="mt-2 ml-6 text-gray-400">No deleted products to display. When you delete products, they will appear here.</p>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table id="trashed-items-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Deleted At</th>
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

<!-- Force Delete Confirmation Modal -->
<div id="force-delete-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Confirm Permanent Deletion</h3>
        <p class="text-gray-300 mb-2">Are you sure you want to permanently delete this product?</p>
        <p class="text-red-400 mb-6 flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i>This action cannot be undone!</p>
        <div class="flex justify-end">
            <button id="cancel-force-delete" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">
                Cancel
            </button>
            <button id="confirm-force-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i> Delete Permanently
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
    /* Custom DataTable styling for better readability */
    #trashed-items-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #trashed-items-table thead th {
        background-color: #1e293b; /* darker blue-gray */
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #trashed-items-table tbody tr {
        background-color: #1f2937; /* dark blue-gray */
    }
    
    #trashed-items-table tbody tr:nth-child(even) {
        background-color: #111827; /* darker for alternating rows */
    }
    
    #trashed-items-table tbody tr:hover {
        background-color: #374151; /* highlight on hover */
    }
    
    #trashed-items-table td {
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
    .restore-btn, .force-delete-btn {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .restore-btn {
        background-color: #10b981;
        color: white;
    }
    
    .restore-btn:hover {
        background-color: #059669;
    }
    
    .force-delete-btn {
        background-color: #ef4444;
        color: white;
    }
    
    .force-delete-btn:hover {
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
            ajax: {
                url: "{{ route('admin.items.trash') }}",
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
                    data: 'deleted_at', 
                    name: 'deleted_at',
                    render: function(data) {
                        let date = new Date(data);
                        return date.toLocaleString();
                    }
                },
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
            
            notification.removeClass('bg-green-600 bg-red-600');
            notificationIcon.removeClass('fa-check-circle fa-times-circle');
            
            if (type === 'success') {
                notification.addClass('bg-green-600 text-white');
                notificationIcon.addClass('fa-check-circle');
            } else {
                notification.addClass('bg-red-600 text-white');
                notificationIcon.addClass('fa-times-circle');
            }
            
            notificationMessage.text(message);
            notificationArea.removeClass('hidden').addClass('visible');
            
            // Hide after 3 seconds
            setTimeout(function() {
                notificationArea.removeClass('visible');
                setTimeout(function() {
                    notificationArea.addClass('hidden');
                }, 300);
            }, 3000);
        }
    });
</script>
@endpush
@endsection 