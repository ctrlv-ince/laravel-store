@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-chart-line text-blue-500 mr-2"></i>
    Admin Dashboard
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="rounded-full bg-white bg-opacity-20 p-3 mr-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Total Orders</p>
                        <h3 class="text-2xl font-semibold">{{ $totalOrders }}</h3>
                    </div>
                </div>
                <div class="mt-2 text-sm text-white text-opacity-70">
                    <span class="{{ $ordersChangePercentage >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        <i class="fas fa-{{ $ordersChangePercentage >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ abs($ordersChangePercentage) }}%
                    </span>
                    from previous period
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="rounded-full bg-white bg-opacity-20 p-3 mr-4">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Total Revenue</p>
                        <h3 class="text-2xl font-semibold">₱{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
                <div class="mt-2 text-sm text-white text-opacity-70">
                    <span class="{{ $revenueChangePercentage >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        <i class="fas fa-{{ $revenueChangePercentage >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ abs($revenueChangePercentage) }}%
                    </span>
                    from previous period
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="rounded-full bg-white bg-opacity-20 p-3 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Total Users</p>
                        <h3 class="text-2xl font-semibold">{{ $totalUsers }}</h3>
                    </div>
                </div>
                <div class="mt-2 text-sm text-white text-opacity-70">
                    <span class="{{ $usersChangePercentage >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        <i class="fas fa-{{ $usersChangePercentage >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ abs($usersChangePercentage) }}%
                    </span>
                    from previous period
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-red-700 rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="rounded-full bg-white bg-opacity-20 p-3 mr-4">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Total Products</p>
                        <h3 class="text-2xl font-semibold">{{ $totalProducts }}</h3>
                    </div>
                </div>
                <div class="mt-2 text-sm text-white text-opacity-70">
                    <span class="{{ $productsChangePercentage >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        <i class="fas fa-{{ $productsChangePercentage >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ abs($productsChangePercentage) }}%
                    </span>
                    from previous period
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Sales Chart -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Sales Overview</h3>
                    <form id="dateRangeForm" class="flex items-center">
                        <div class="flex space-x-2">
                            <div>
                                <label for="start_date" class="block text-xs text-gray-400 mb-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" 
                                       class="bg-gray-700 text-white border border-gray-600 rounded px-2 py-1 text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs text-gray-400 mb-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" 
                                       class="bg-gray-700 text-white border border-gray-600 rounded px-2 py-1 text-sm">
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm ml-2 mt-4 hover:bg-blue-700">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </form>
                </div>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Products by Category Chart -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-4">Products by Category</h3>
                <div style="height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Second Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Product Sales Percentage Pie Chart -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-4">Product Sales Percentage</h3>
                <div style="height: 300px;">
                    <canvas id="productSalesChart"></canvas>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-4">Order Status Distribution</h3>
                <div style="height: 300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Low Stock -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Orders -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-white">
                        <thead>
                            <tr class="text-left text-gray-400 text-sm">
                                <th class="pb-3 pr-2">Order ID</th>
                                <th class="pb-3 pr-2">Customer</th>
                                <th class="pb-3 pr-2">Date</th>
                                <th class="pb-3 pr-2">Total</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                                <td class="py-3 pr-2">
                                    <a href="{{ route('admin.orders.show', $order->order_id) }}" class="hover:text-blue-400">
                                        #{{ $order->order_id }}
                                    </a>
                                </td>
                                <td class="py-3 pr-2">{{ $order->account->user->name ?? 'Guest' }}</td>
                                <td class="py-3 pr-2">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="py-3 pr-2">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($order->status == 'completed') bg-green-500
                                        @elseif($order->status == 'processing') bg-blue-500
                                        @elseif($order->status == 'pending') bg-yellow-500
                                        @elseif($order->status == 'cancelled') bg-red-500
                                        @else bg-gray-500
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach

                            @if($recentOrders->isEmpty())
                            <tr class="border-t border-gray-700">
                                <td colspan="5" class="py-4 text-center text-gray-400">No recent orders found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Low Stock Products</h3>
                    <a href="{{ route('admin.items.index') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-white">
                        <thead>
                            <tr class="text-left text-gray-400 text-sm">
                                <th class="pb-3 pr-2">Product</th>
                                <th class="pb-3 pr-2">Category</th>
                                <th class="pb-3 pr-2">Price</th>
                                <th class="pb-3">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                                <td class="py-3 pr-2">
                                    <a href="{{ route('admin.items.edit', $product->item_id) }}" class="hover:text-blue-400">
                                        {{ $product->item_name }}
                                    </a>
                                </td>
                                <td class="py-3 pr-2">{{ $product->group->group_name ?? 'Uncategorized' }}</td>
                                <td class="py-3 pr-2">₱{{ number_format($product->price, 2) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($product->inventory->quantity <= 0) bg-red-500
                                        @elseif($product->inventory->quantity < 5) bg-yellow-500
                                        @else bg-green-500
                                        @endif">
                                        {{ $product->inventory->quantity }} left
                                    </span>
                                </td>
                            </tr>
                            @endforeach

                            @if($lowStockProducts->isEmpty())
                            <tr class="border-t border-gray-700">
                                <td colspan="4" class="py-4 text-center text-gray-400">No low stock products found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart color palette
    const colors = {
        blue: 'rgba(59, 130, 246, 0.8)',
        green: 'rgba(16, 185, 129, 0.8)',
        red: 'rgba(239, 68, 68, 0.8)',
        purple: 'rgba(139, 92, 246, 0.8)',
        yellow: 'rgba(245, 158, 11, 0.8)',
        gray: 'rgba(107, 114, 128, 0.8)',
        orange: 'rgba(249, 115, 22, 0.8)',
        teal: 'rgba(20, 184, 166, 0.8)',
        indigo: 'rgba(99, 102, 241, 0.8)',
        pink: 'rgba(236, 72, 153, 0.8)'
    };

    // Chart data
    const salesLabels = @json($salesChartData['labels']);
    const salesData = @json($salesChartData['data']);
    
    const categoryLabels = @json($categoryChartData['labels']);
    const categoryValues = @json($categoryChartData['data']);
    
    const statusLabels = @json($orderStatusChartData['labels']);
    const statusValues = @json($orderStatusChartData['data']);
    
    const productLabels = @json($productSalesData['labels']);
    const productValues = @json($productSalesData['data']);

    // Generate color array for products
    const productColors = [];
    const colorKeys = Object.keys(colors);
    for (let i = 0; i < productLabels.length; i++) {
        productColors.push(colors[colorKeys[i % colorKeys.length]]);
    }

    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Sales Amount',
                data: salesData,
                backgroundColor: colors.blue,
                borderColor: 'rgba(20, 20, 20, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    callbacks: {
                        label: function(context) {
                            return 'Sales: ₱' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryValues,
                backgroundColor: Object.values(colors),
                borderColor: 'rgba(20, 20, 20, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        font: {
                            size: 11
                        },
                        boxWidth: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw} products`;
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Number of Orders',
                data: statusValues,
                backgroundColor: [
                    colors.yellow,  // pending
                    colors.blue,    // processing
                    colors.green,   // completed
                    colors.red,     // cancelled
                    colors.gray     // other
                ],
                borderColor: 'rgba(20, 20, 20, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)'
                }
            }
        }
    });
    
    // Product Sales Percentage Chart
    const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
    const productSalesChart = new Chart(productSalesCtx, {
        type: 'pie',
        data: {
            labels: productLabels,
            datasets: [{
                data: productValues,
                backgroundColor: productColors,
                borderColor: 'rgba(20, 20, 20, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        font: {
                            size: 11
                        },
                        boxWidth: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw}% of sales`;
                        }
                    }
                }
            }
        }
    });
    
    // Date range form submit
    document.getElementById('dateRangeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("admin.dashboard") }}' + 
            '?start_date=' + document.getElementById('start_date').value + 
            '&end_date=' + document.getElementById('end_date').value;
    });
</script>
@endpush
@endsection 