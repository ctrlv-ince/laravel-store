<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = false;
        
        // Check if user has an account and if the role is admin
        if ($user->account && $user->account->role === 'admin') {
            $isAdmin = true;
        }
        
        // Initialize values to ensure we always have data to display
        $totalOrders = 0;
        $totalProducts = 0;
        $totalRevenue = 0;
        $totalUsers = 0;
        $recentOrders = collect(); // Empty collection
        $popularProducts = collect(); // Empty collection
        $userOrders = collect(); // User's personal orders
        
        if ($isAdmin) {
            // Admin view - display overall stats
            try {
                $totalOrders = Order::count() ?? 0;
                $totalProducts = Item::count() ?? 0;
                $totalRevenue = Order::where('status', 'completed')->sum('total_amount') ?? 0;
                $totalUsers = User::count() ?? 0;
            } catch (\Exception $e) {
                // If there's an error (like missing tables), keep the defaults
                Log::error('Error loading admin dashboard stats: ' . $e->getMessage());
            }
            
            // Try to get recent orders for admin with all relationships
            try {
                $recentOrders = Order::with(['account.user', 'orderInfos.item'])
                    ->orderBy('date_ordered', 'desc')
                    ->take(5)
                    ->get();
                    
                // Try to convert date_ordered to Carbon instances
                foreach ($recentOrders as $order) {
                    if (isset($order->date_ordered) && !($order->date_ordered instanceof Carbon)) {
                        try {
                            $order->date_ordered = Carbon::parse($order->date_ordered);
                        } catch (\Exception $e) {
                            // If we can't parse it, leave as is
                        }
                    }
                }
            } catch (\Exception $e) {
                // Keep empty collection if error
                Log::error('Error loading admin recent orders: ' . $e->getMessage());
            }
            
            // Get popular products with sales data for admin
            try {
                // Get products with their order info to calculate sales
                $popularProducts = Item::with(['images', 'orderInfos', 'inventory'])
                    ->withCount('orderInfos')
                    ->orderBy('order_infos_count', 'desc')
                    ->take(4)
                    ->get();
            } catch (\Exception $e) {
                // Keep empty collection if error
                Log::error('Error loading admin popular products: ' . $e->getMessage());
            }
        } else {
            // Regular user view - display only personal stats
            try {
                // Get user's personal orders if they have an account
                if ($user->account) {
                    $userOrders = Order::where('account_id', $user->account->account_id)
                        ->orderBy('date_ordered', 'desc')
                        ->get();
                    
                    $totalOrders = $userOrders->count();
                    $totalRevenue = $userOrders->sum('total_amount');
                    
                    // For pagination/display in view
                    $recentOrders = $userOrders->take(5);
                }
            } catch (\Exception $e) {
                // Keep empty collection if error
                Log::error('Error loading user orders: ' . $e->getMessage());
            }
            
            // Try to get recommended products for users
            try {
                // For regular users, get popular products that are in stock
                $popularProducts = Item::with(['images', 'inventory'])
                    ->whereHas('inventory', function($query) {
                        $query->where('quantity', '>', 0);
                    })
                    ->take(4)
                    ->get();
            } catch (\Exception $e) {
                // Keep empty collection if error
                Log::error('Error loading recommended products: ' . $e->getMessage());
            }
        }
        
        return view('dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalRevenue',
            'totalUsers',
            'recentOrders',
            'popularProducts',
            'isAdmin',
            'user'
        ));
    }
} 