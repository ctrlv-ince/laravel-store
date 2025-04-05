@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-400">
                <i class="fas fa-microchip mr-2"></i>Order Management
            </h1>
            @if(Auth::user()->account->isAdmin())
            <div class="flex space-x-4">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>Export Orders
                </button>
            </div>
            @endif
        </div>

        <div class="bg-gray-700 rounded-lg p-4">
            <table id="orders-table" class="w-full">
                <thead>
                    <tr class="text-left text-gray-300">
                        <th class="p-3">Order ID</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .status-badge {
        @apply px-3 py-1 rounded-full text-sm font-medium;
    }
    .status-pending {
        @apply bg-yellow-500 text-yellow-100;
    }
    .status-shipped {
        @apply bg-blue-500 text-blue-100;
    }
    .status-for_confirm {
        @apply bg-purple-500 text-purple-100;
    }
    .status-completed {
        @apply bg-green-500 text-green-100;
    }
    .status-cancelled {
        @apply bg-red-500 text-red-100;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('orders.index') }}",
        columns: [
            { data: 'order_id', name: 'order_id' },
            { data: 'customer', name: 'customer' },
            { data: 'date_ordered', name: 'date_ordered' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            search: '<i class="fas fa-search"></i>',
            searchPlaceholder: 'Search orders...'
        }
    });
});
</script>
@endpush
@endsection 