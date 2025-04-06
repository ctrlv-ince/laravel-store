@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-star text-yellow-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Review Management</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">Product Reviews</h2>
                
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
                        <p>Review management allows you to monitor and moderate customer feedback. Reviews with inappropriate content can be removed.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="reviews-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Comment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
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
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Confirm Delete</h3>
        <p class="text-gray-300 mb-6 text-base">Are you sure you want to delete this review? This action cannot be undone.</p>
        <div class="flex justify-end">
            <button id="cancel-delete" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 transition-colors duration-200">Cancel</button>
            <button id="confirm-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i> Delete
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
    #reviews-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #reviews-table thead th {
        background-color: #1e293b; /* darker blue-gray */
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #reviews-table tbody tr {
        background-color: #1f2937; /* dark blue-gray */
    }
    
    #reviews-table tbody tr:nth-child(even) {
        background-color: #111827; /* darker for alternating rows */
    }
    
    #reviews-table tbody tr:hover {
        background-color: #374151; /* highlight on hover */
    }
    
    #reviews-table td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #4b5563;
    }
    
    /* Rating stars styling */
    .rating-stars {
        color: #fbbf24; /* amber/yellow color */
        letter-spacing: 2px;
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
    .delete-review {
        display: inline-block;
        padding: 6px 12px;
        background-color: #ef4444;
        color: white;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .delete-review:hover {
        background-color: #dc2626;
    }
    
    /* Limit review text display */
    .review-text {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .review-text.expanded {
        white-space: normal;
        max-height: none;
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
    let reviewsTable = $('#reviews-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('admin.reviews.index') }}",
        columns: [
            { data: 'review_id', name: 'review_id' },
            { data: 'product', name: 'item.item_name' },
            { data: 'customer', name: 'account.user.first_name' },
            { 
                data: 'rating', 
                name: 'rating',
                render: function(data) {
                    let stars = '';
                    const rating = parseInt(data);
                    for (let i = 0; i < rating; i++) {
                        stars += '<i class="fas fa-star"></i>';
                    }
                    for (let i = rating; i < 5; i++) {
                        stars += '<i class="far fa-star"></i>';
                    }
                    return '<span class="rating-stars">' + stars + '</span>';
                }
            },
            { 
                data: 'comment', 
                name: 'comment',
                render: function(data) {
                    if (!data) return '<em class="text-gray-400">No review text</em>';
                    return '<div class="review-text" title="' + data.replace(/"/g, '&quot;') + '">' + data + '</div>';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    const date = new Date(data);
                    return date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            { 
                data: 'review_id', 
                name: 'actions',
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return '<button class="delete-review" data-id="' + data + 
                           '"><i class="fas fa-trash-alt mr-1"></i> Delete</button>';
                }
            }
        ],
        order: [[0, 'desc']],
        drawCallback: function() {
            // Make review text expandable on click
            $('.review-text').on('click', function() {
                $(this).toggleClass('expanded');
            });
        },
        language: {
            processing: '<div class="flex justify-center"><i class="fas fa-spinner fa-spin fa-2x text-yellow-500"></i></div>'
        }
    });
    
    // Delete review functionality
    let reviewIdToDelete = null;
    const deleteModal = document.getElementById('delete-modal');
    
    // Show delete confirmation modal
    $(document).on('click', '.delete-review', function() {
        reviewIdToDelete = $(this).data('id');
        deleteModal.classList.remove('hidden');
    });
    
    // Cancel delete
    document.getElementById('cancel-delete').addEventListener('click', function() {
        deleteModal.classList.add('hidden');
        reviewIdToDelete = null;
    });
    
    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', function() {
        if (reviewIdToDelete) {
            $.ajax({
                url: "{{ url('admin/reviews') }}/" + reviewIdToDelete,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the table
                        reviewsTable.ajax.reload();
                        
                        // Show success message
                        const successMessage = document.createElement('div');
                        successMessage.className = 'bg-green-600 text-white p-4 mb-6 rounded-lg shadow-md flex items-center';
                        successMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Review deleted successfully!';
                        
                        const tableParent = document.querySelector('#reviews-table').parentNode;
                        tableParent.insertBefore(successMessage, document.querySelector('#reviews-table'));
                        
                        // Remove message after 3 seconds
                        setTimeout(function() {
                            successMessage.remove();
                        }, 3000);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred while deleting the review.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMsg);
                },
                complete: function() {
                    // Hide modal and reset reviewIdToDelete
                    deleteModal.classList.add('hidden');
                    reviewIdToDelete = null;
                }
            });
        }
    });
    
    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            reviewIdToDelete = null;
        }
    });
});
</script>
@endpush
@endsection 