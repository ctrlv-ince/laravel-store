@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-users text-blue-400 mr-2 text-xl"></i>
    <span class="text-xl font-semibold">User Management</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-3">User Accounts</h2>
                
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
                        <div>
                            <p class="mb-1">Manage user accounts, roles, and permissions.</p>
                            <div class="text-sm flex mt-2 space-x-4">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-key text-blue-400 mr-1"></i>
                                    Users can log in with email or username
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-ban text-red-400 mr-1"></i>
                                    Inactive users cannot log in
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="users-table" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
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
    #users-table {
        color: white;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #users-table thead th {
        background-color: #1e293b; /* darker blue-gray */
        color: white;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #4b5563;
    }
    
    #users-table tbody tr {
        background-color: #1f2937; /* dark blue-gray */
    }
    
    #users-table tbody tr:nth-child(even) {
        background-color: #111827; /* darker for alternating rows */
    }
    
    #users-table tbody tr:hover {
        background-color: #374151; /* highlight on hover */
    }
    
    #users-table td {
        padding: 12px 10px;
        font-size: 14px;
        border-bottom: 1px solid #4b5563;
    }
    
    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-badge-active {
        background-color: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }
    
    .status-badge-inactive {
        background-color: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }
    
    /* Role badges */
    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .role-badge-admin {
        background-color: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    
    .role-badge-user {
        background-color: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
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
    .btn-edit {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 4px;
        font-weight: bold;
        background-color: #3b82f6;
        color: white;
        transition: background-color 0.2s;
    }
    
    .btn-edit:hover {
        background-color: #2563eb;
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
    
    /* Custom Select Styling */
    .custom-select {
        background-color: #1f2937;
        color: white;
        border: 1px solid #4b5563;
        padding: 0.5rem;
        border-radius: 0.375rem;
        width: 100%;
        appearance: none;
        background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>');
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('admin.users.index') }}",
        columns: [
            { data: 'user_id', name: 'user_id' },
            { 
                data: null, 
                name: 'name',
                render: function(data) {
                    return data.first_name + ' ' + data.last_name;
                } 
            },
            { data: 'email', name: 'email' },
            { 
                data: 'role', 
                name: 'role',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isCurrentUser = row.user_id == "{{ Auth::id() }}";
                        const disabled = isCurrentUser ? 'disabled' : '';
                        
                        return `
                            <div class="select-wrapper">
                                <select ${disabled} class="role-select custom-select" data-user-id="${row.user_id}">
                                    <option value="user" ${data === 'user' ? 'selected' : ''}>User</option>
                                    <option value="admin" ${data === 'admin' ? 'selected' : ''}>Admin</option>
                                </select>
                                <div class="select-loading-overlay">
                                    <div class="spinner"></div>
                                </div>
                            </div>
                        `;
                    }
                    // For sorting/filtering show the actual value
                    const badgeClass = data === 'admin' ? 'role-badge-admin' : 'role-badge-user';
                    return `<span class="role-badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isCurrentUser = row.user_id == "{{ Auth::id() }}";
                        const disabled = isCurrentUser ? 'disabled' : '';
                        
                        return `
                            <div class="select-wrapper">
                                <select ${disabled} class="status-select custom-select" data-user-id="${row.user_id}">
                                    <option value="active" ${data === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${data === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                                <div class="select-loading-overlay">
                                    <div class="spinner"></div>
                                </div>
                            </div>
                        `;
                    }
                    // For sorting/filtering show the actual value
                    const badgeClass = data === 'active' ? 'status-badge-active' : 'status-badge-inactive';
                    return `<span class="status-badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const editUrl = "{{ route('admin.users.edit', ':id') }}".replace(':id', row.user_id);
                    return `
                        <a href="${editUrl}" class="btn-edit">Manage Account</a>
                    `;
                }
            }
        ],
        drawCallback: function() {
            // Initialize select change events after table is drawn
            $('.role-select').on('change', function() {
                const $select = $(this);
                const userId = $select.data('user-id');
                const role = $select.val();
                const $overlay = $select.closest('.select-wrapper').find('.select-loading-overlay');
                
                // Show loading overlay
                $overlay.addClass('active');
                
                updateUserRole(userId, role, $overlay);
            });
            
            $('.status-select').on('change', function() {
                const $select = $(this);
                const userId = $select.data('user-id');
                const status = $select.val();
                const $overlay = $select.closest('.select-wrapper').find('.select-loading-overlay');
                
                // Show loading overlay
                $overlay.addClass('active');
                
                updateUserStatus(userId, status, $overlay);
            });
        }
    });
    
    // Improved notification function
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
    
    function updateUserRole(userId, role, $overlay) {
        $.ajax({
            url: "{{ route('admin.users.updateRole') }}",
            type: 'POST',
            data: {
                user_id: userId,
                role: role,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    showNotification('User role updated successfully!', 'success');
                    table.ajax.reload(null, false); // Reload table without resetting pagination
                } else {
                    showNotification('Error: ' + response.message, 'error');
                    // Hide loading overlay
                    $overlay.removeClass('active');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while updating user role.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showNotification('Error: ' + errorMsg, 'error');
                // Hide loading overlay
                $overlay.removeClass('active');
            }
        });
    }
    
    function updateUserStatus(userId, status, $overlay) {
        $.ajax({
            url: "{{ route('admin.users.updateStatus') }}",
            type: 'POST',
            data: {
                user_id: userId,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    showNotification('User status updated successfully!', 'success');
                    table.ajax.reload(null, false); // Reload table without resetting pagination
                } else {
                    showNotification('Error: ' + response.message, 'error');
                    // Hide loading overlay
                    $overlay.removeClass('active');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while updating user status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showNotification('Error: ' + errorMsg, 'error');
                // Hide loading overlay
                $overlay.removeClass('active');
            }
        });
    }
});
</script>
@endpush
@endsection 