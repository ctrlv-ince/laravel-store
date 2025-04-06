<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get date range from request or use default (last 7 days)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(6)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // Date ranges
        $now = Carbon::now();
        $thisMonth = [
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        ];
        $lastMonth = [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth()
        ];

        // Total orders with percentage change
        $ordersThisMonth = Order::whereBetween('created_at', $thisMonth)->count();
        $ordersLastMonth = Order::whereBetween('created_at', $lastMonth)->count();
        $ordersChangePercentage = $this->calculatePercentageChange($ordersLastMonth, $ordersThisMonth);

        // Total revenue with percentage change
        $revenueThisMonth = Order::whereBetween('created_at', $thisMonth)->sum('total_amount');
        $revenueLastMonth = Order::whereBetween('created_at', $lastMonth)->sum('total_amount');
        $revenueChangePercentage = $this->calculatePercentageChange($revenueLastMonth, $revenueThisMonth);

        // Total users with percentage change
        $usersThisMonth = User::whereBetween('created_at', $thisMonth)->count();
        $usersLastMonth = User::whereBetween('created_at', $lastMonth)->count();
        $usersChangePercentage = $this->calculatePercentageChange($usersLastMonth, $usersThisMonth);

        // Total products with percentage change
        $productsThisMonth = Item::whereBetween('created_at', $thisMonth)->count();
        $productsLastMonth = Item::whereBetween('created_at', $lastMonth)->count();
        $productsChangePercentage = $this->calculatePercentageChange($productsLastMonth, $productsThisMonth);

        // Recent orders
        $recentOrders = Order::with('account.user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Low stock products
        $lowStockProducts = Item::with(['inventory', 'group'])
            ->whereHas('inventory', function ($query) {
                $query->where('quantity', '<', 10);
            })
            ->join('inventories', 'items.item_id', '=', 'inventories.item_id')
            ->orderBy('inventories.quantity', 'asc')
            ->select('items.*')
            ->take(5)
            ->get();

        // Sales chart data with date range
        $salesChartData = $this->getSalesChartDataWithRange($startDate, $endDate);

        // Products by category chart data
        $categoryChartData = $this->getCategoryChartData();

        // Order status distribution chart data
        $orderStatusChartData = $this->getOrderStatusChartData();
        
        // Product sales percentage pie chart
        $productSalesData = $this->getProductSalesPercentage();

        return view('admin.dashboard', [
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::sum('total_amount'),
            'totalUsers' => User::count(),
            'totalProducts' => Item::count(),
            'ordersChangePercentage' => $ordersChangePercentage,
            'revenueChangePercentage' => $revenueChangePercentage,
            'usersChangePercentage' => $usersChangePercentage,
            'productsChangePercentage' => $productsChangePercentage,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'salesChartData' => $salesChartData,
            'categoryChartData' => $categoryChartData,
            'orderStatusChartData' => $orderStatusChartData,
            'productSalesData' => $productSalesData,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    /**
     * Calculate percentage change between two values
     *
     * @param float $oldValue
     * @param float $newValue
     * @return float
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }

    /**
     * Get data for sales chart
     *
     * @return array
     */
    private function getSalesChartData()
    {
        $days = 7;
        $period = [];
        $salesData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $period[] = Carbon::now()->subDays($i)->format('M d');

            $sales = Order::whereDate('created_at', $date)->sum('total_amount');
            $salesData[] = $sales;
        }

        // Reverse arrays to show dates in ascending order
        $period = array_reverse($period);
        $salesData = array_reverse($salesData);

        return [
            'labels' => $period,
            'data' => $salesData
        ];
    }

    /**
     * Get data for categories chart
     *
     * @return array
     */
    private function getCategoryChartData()
    {
        $categories = Group::withCount('items')->get();
        
        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $labels[] = $category->group_name;
            $data[] = $category->items_count;
        }

        // Add uncategorized items
        $uncategorizedCount = Item::whereDoesntHave('groups')->count();
        if ($uncategorizedCount > 0) {
            $labels[] = 'Uncategorized';
            $data[] = $uncategorizedCount;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get data for order status chart
     *
     * @return array
     */
    private function getOrderStatusChartData()
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        $statusCounts = [];
        foreach ($statuses as $status) {
            $statusCounts[$status] = Order::where('status', $status)->count();
        }

        // Check for other statuses
        $otherCount = Order::whereNotIn('status', $statuses)->count();
        if ($otherCount > 0) {
            $statusCounts['other'] = $otherCount;
        }

        return [
            'labels' => array_map('ucfirst', array_keys($statusCounts)),
            'data' => array_values($statusCounts)
        ];
    }

    /**
     * Get sales data within a specified date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getSalesChartDataWithRange($startDate, $endDate)
    {
        // Calculate the number of days in the range
        $daysDiff = $startDate->diffInDays($endDate) + 1;
        
        // If the range is more than 31 days, group by weeks or months
        if ($daysDiff > 31) {
            return $this->getMonthlyOrWeeklySales($startDate, $endDate);
        }
        
        // Get daily sales for the date range
        $period = [];
        $salesData = [];
        
        for ($i = 0; $i < $daysDiff; $i++) {
            $date = $startDate->copy()->addDays($i);
            $formattedDate = $date->format('Y-m-d');
            $period[] = $date->format('M d');
            
            $sales = Order::whereDate('created_at', $formattedDate)->sum('total_amount');
            $salesData[] = $sales;
        }
        
        return [
            'labels' => $period,
            'data' => $salesData
        ];
    }
    
    /**
     * Get monthly or weekly sales data for longer periods
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getMonthlyOrWeeklySales($startDate, $endDate)
    {
        $daysDiff = $startDate->diffInDays($endDate);
        
        // If more than 90 days, group by months
        if ($daysDiff > 90) {
            return $this->getMonthlySales($startDate, $endDate);
        }
        
        // Otherwise, group by weeks
        return $this->getWeeklySales($startDate, $endDate);
    }
    
    /**
     * Get weekly sales data
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getWeeklySales($startDate, $endDate)
    {
        $period = [];
        $salesData = [];
        
        // Adjust to start from the beginning of the week
        $currentDate = $startDate->copy()->startOfWeek();
        
        while ($currentDate->lte($endDate)) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->endOfWeek();
            
            // Ensure we don't go past the end date
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }
            
            $period[] = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
            
            $sales = Order::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_amount');
            $salesData[] = $sales;
            
            $currentDate->addWeek();
        }
        
        return [
            'labels' => $period,
            'data' => $salesData
        ];
    }
    
    /**
     * Get monthly sales data
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getMonthlySales($startDate, $endDate)
    {
        $period = [];
        $salesData = [];
        
        // Adjust to start from the beginning of the month
        $currentDate = $startDate->copy()->startOfMonth();
        
        while ($currentDate->lte($endDate)) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            // Ensure we don't go past the end date
            if ($monthEnd->gt($endDate)) {
                $monthEnd = $endDate->copy();
            }
            
            $period[] = $currentDate->format('M Y');
            
            $sales = Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            $salesData[] = $sales;
            
            $currentDate->addMonth();
        }
        
        return [
            'labels' => $period,
            'data' => $salesData
        ];
    }
    
    /**
     * Get percentage of total sales by product
     *
     * @return array
     */
    private function getProductSalesPercentage()
    {
        // Get total sales amount
        $totalSales = Order::sum('total_amount');
        
        if ($totalSales <= 0) {
            return [
                'labels' => ['No Sales Data'],
                'data' => [100]
            ];
        }
        
        // Get top 10 products with the highest sales
        $productSales = DB::table('orderinfos')
            ->join('items', 'orderinfos.item_id', '=', 'items.item_id')
            ->select('items.item_name', DB::raw('SUM(orderinfos.quantity * items.price) as total_sales'))
            ->groupBy('items.item_id', 'items.item_name')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();
            
        // Calculate the percentage of each product
        $labels = [];
        $data = [];
        $otherSales = $totalSales;
        
        foreach ($productSales as $product) {
            $labels[] = $product->item_name;
            $percentage = ($product->total_sales / $totalSales) * 100;
            $data[] = round($percentage, 1);
            $otherSales -= $product->total_sales;
        }
        
        // Add "Others" category if there are more products
        if ($otherSales > 0) {
            $labels[] = 'Others';
            $percentage = ($otherSales / $totalSales) * 100;
            $data[] = round($percentage, 1);
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
} 