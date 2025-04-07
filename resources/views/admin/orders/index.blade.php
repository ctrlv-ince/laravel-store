@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-shopping-basket text-green-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">Order Management</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">Customer Orders</h2>
                
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
                
                @if(session('info'))
                <div class="bg-blue-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
                @endif
                
                <div class="bg-gray-900 p-4 mb-6 rounded-lg text-gray-300 shadow-inner">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        <p>Order management allows you to track and update customer orders. Click on an order ID to view detailed information.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="orders-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
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

<div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-4 rounded-lg shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center text-white mb-2">
                <i class="fas fa-question-circle text-blue-400 mr-2"></i>
                <h3 class="font-semibold">Need Help?</h3>
            </div>
            <p class="text-gray-300 text-sm">For assistance with order processing, please refer to the admin documentation or contact technical support.</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg shadow-lg border-l-4 border-yellow-500">
            <div class="flex items-center text-white mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                <h3 class="font-semibold">Order Updates</h3>
            </div>
            <p class="text-gray-300 text-sm">Updating an order's status will automatically notify the customer via email about their order progress.</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg shadow-lg border-l-4 border-green-500">
            <div class="flex items-center text-white mb-2">
                <i class="fas fa-chart-line text-green-400 mr-2"></i>
                <h3 class="font-semibold">Order Statistics</h3>
            </div>
            <p class="text-gray-300 text-sm">View detailed reports and statistics about orders by visiting the Dashboard section of the admin panel.</p>
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
    #orders-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #orders-table thead th {
        background-color: #1e293b;
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #orders-table tbody tr {
        background-color: #1f2937;
    }
    
    #orders-table tbody tr:nth-child(even) {
        background-color: #111827;
    }
    
    #orders-table tbody tr:hover {
        background-color: #374151;
    }
    
    #orders-table td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #4b5563;
    }
    
    /* Action buttons styling */
    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin: 2px;
        border-radius: 4px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .btn-info {
        background-color: #3b82f6;
        color: white;
    }
    
    .btn-info:hover {
        background-color: #2563eb;
    }
    
    .btn-primary {
        background-color: #6366f1;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #4f46e5;
    }
    
    .mr-1 {
        margin-right: 0.25rem;
    }
    
    /* Status badges styling */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 9999px;
    }
    
    /* Pagination styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: white !important;
        background-color: #374151;
        border-radius: 4px;
        margin: 2px;
        padding: 5px 10px !important;
        border: none !important;
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
    
    /* Length menu styling */
    .dataTables_wrapper .dataTables_length select {
        background-color: #1f2937;
        color: white;
        border: 1px solid #4b5563;
        border-radius: 4px;
        padding: 5px;
    }
    
    /* Info text styling */
    .dataTables_wrapper .dataTables_info {
        color: #d1d5db !important;
        margin-top: 10px;
    }
</style>

<script>
$(document).ready(function() {
    var table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.orders.index') }}",
            cache: false
        },
        columns: [
            { data: 'order_id', name: 'order_id' },
            { data: 'customer', name: 'account.user.first_name' },
            { data: 'date', name: 'date_ordered' },
            { data: 'items', name: 'items', orderable: false, searchable: false },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data) {
                    return '₱' + parseFloat(data).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            },
            { data: 'status_badge', name: 'status', orderable: true, searchable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        drawCallback: function() {
            // Re-initialize any tooltips or other UI elements here
        }
    });

    // Refresh table every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);

    // Force refresh when returning from other pages
    $(document).on('show.bs.modal', function() {
        table.ajax.reload(null, false);
    });

    // Add event listener for status updates
    $(document).on('orderStatusUpdated', function() {
        table.ajax.reload(null, false);
    });
});
</script>
@endpush
@endsection 