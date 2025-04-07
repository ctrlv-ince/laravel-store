@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-tags text-blue-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Category Management</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">Product Categories</h2>
                
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
                        <p>Categories help organize your products for better navigation. Add, edit, or remove categories as needed.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <button id="create-category-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Add New Category
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table id="categories-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Products</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Category Modal -->
<div id="category-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-3">
            <h3 id="modal-title" class="text-xl font-bold text-white">Add New Category</h3>
            <button id="close-modal" class="text-gray-400 hover:text-white transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="category-form" method="POST" action="{{ route('admin.groups.store') }}">
            @csrf
            <div id="method-field"></div>
            
            <div class="mb-4">
                <label for="group_name" class="block text-sm font-medium text-gray-400 mb-1">Category Name</label>
                <input type="text" name="group_name" id="group_name" class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="group_description" class="block text-sm font-medium text-gray-400 mb-1">Description (Optional)</label>
                <textarea name="group_description" id="group_description" rows="3" class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-btn" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" id="save-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Confirm Delete</h3>
        <p class="text-gray-300 mb-3">Are you sure you want to delete this category? This action cannot be undone.</p>
        <p class="text-yellow-500 mb-6 flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i> Products in this category will be set to uncategorized.</p>
        
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="flex justify-end">
                <button type="button" id="cancel-delete" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<style>
    /* Custom DataTable styling for better readability */
    #categories-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #categories-table thead th {
        background-color: #1e293b; /* darker blue-gray */
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #categories-table tbody tr {
        background-color: #1f2937; /* dark blue-gray */
    }
    
    #categories-table tbody tr:nth-child(even) {
        background-color: #111827; /* darker for alternating rows */
    }
    
    #categories-table tbody tr:hover {
        background-color: #374151; /* highlight on hover */
    }
    
    #categories-table td {
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
    .edit-btn, .delete-btn {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .edit-btn {
        background-color: #3b82f6;
        color: white;
    }
    
    .edit-btn:hover {
        background-color: #2563eb;
    }
    
    .delete-btn {
        background-color: #ef4444;
        color: white;
    }
    
    .delete-btn:hover {
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
        // Initialize DataTable
        const table = $('#categories-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('admin.groups.index') }}",
                type: 'GET'
            },
            columns: [
                { data: 'group_id', name: 'group_id' },
                { data: 'group_name', name: 'group_name' },
                { data: 'items_count', name: 'items_count', defaultContent: '0' },
                { data: 'created_at', name: 'created_at', defaultContent: 'N/A' },
                { 
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
            language: {
                processing: '<div class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>',
                emptyTable: 'No categories found',
                zeroRecords: 'No matching categories found'
            },
            drawCallback: function() {
                // Setup click handlers for edit and delete buttons
                $('.edit-btn').on('click', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const description = $(this).data('description');
                    
                    $('#modal-title').text('Edit Category');
                    $('#group_name').val(name);
                    $('#group_description').val(description);
                    
                    $('#method-field').html('<input type="hidden" name="_method" value="PUT">');
                    $('#category-form').attr('action', `/admin/groups/${id}`);
                    
                    $('#category-modal').removeClass('hidden');
                });
                
                $('.delete-btn').on('click', function() {
                    const id = $(this).data('id');
                    $('#delete-form').attr('action', `/admin/groups/${id}`);
                    $('#delete-modal').removeClass('hidden');
                });
            }
        });
        
        // Open create modal
        $('#create-category-btn').on('click', function() {
            $('#modal-title').text('Add New Category');
            $('#group_name').val('');
            $('#group_description').val('');
            
            $('#method-field').html('');
            $('#category-form').attr('action', "{{ route('admin.groups.store') }}");
            
            $('#category-modal').removeClass('hidden');
        });
        
        // Close modals
        $('#close-modal, #cancel-btn').on('click', function() {
            $('#category-modal').addClass('hidden');
        });
        
        $('#cancel-delete').on('click', function() {
            $('#delete-modal').addClass('hidden');
        });
        
        // Close modals when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#category-modal')) {
                $('#category-modal').addClass('hidden');
            }
            
            if ($(event.target).is('#delete-modal')) {
                $('#delete-modal').addClass('hidden');
            }
        });
    });
</script>
@endpush
@endsection 