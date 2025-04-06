<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderInfo;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['account.user', 'orderInfos.item'])
                ->when(!Auth::user()->account->isAdmin(), function($query) {
                    return $query->where('account_id', Auth::user()->account->account_id);
                });
            
            return DataTables::of($orders)
                ->addColumn('customer', function($order) {
                    return $order->account->user->first_name . ' ' . $order->account->user->last_name;
                })
                ->addColumn('total_amount', function($order) {
                    return '₱' . number_format($order->total_amount, 2);
                })
                ->addColumn('status', function($order) {
                    return view('orders.status', compact('order'))->render();
                })
                ->addColumn('action', function($order) {
                    return view('orders.actions', compact('order'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_items' => 'required|array',
            'cart_items.*.item_id' => 'required|exists:items,item_id',
            'cart_items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check item availability
        foreach ($request->cart_items as $cartItem) {
            $item = Item::with('inventory')->find($cartItem['item_id']);
            if (!$item->inventory || $item->inventory->quantity < $cartItem['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock for item: ' . $item->item_name
                ], 422);
            }
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($request->cart_items as $cartItem) {
            $item = Item::find($cartItem['item_id']);
            $totalAmount += $item->price * $cartItem['quantity'];
        }

        // Create order
        $order = Order::create([
            'account_id' => Auth::user()->account->account_id,
            'date_ordered' => now(),
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ]);

        // Create order items and update inventory
        foreach ($request->cart_items as $cartItem) {
            $item = Item::with('inventory')->find($cartItem['item_id']);
            
            OrderInfo::create([
                'order_id' => $order->order_id,
                'item_id' => $cartItem['item_id'],
                'quantity' => $cartItem['quantity']
            ]);

            // Update inventory
            $item->inventory->update([
                'quantity' => $item->inventory->quantity - $cartItem['quantity']
            ]);
        }

        // Clear cart
        Cart::where('account_id', Auth::user()->account->account_id)->delete();

        // Send order confirmation email
        $this->sendOrderConfirmationEmail($order);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->order_id
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['account.user', 'orderInfos.item.images', 'orderInfos.item.inventory']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,shipped,for_confirm,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order->update([
            'status' => $request->status
        ]);

        // Send status update email
        $this->sendOrderStatusUpdateEmail($order);

        return redirect()->back()
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Generate receipt
     */
    public function generateReceipt(Order $order)
    {
        $order->load(['account.user', 'orderInfos.item.images', 'orderInfos.item.inventory']);
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setOptions(new \Dompdf\Options([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'isPhpEnabled' => true,
        ]));
        $dompdf->loadHtml(view('orders.receipt', compact('order'))->render());
        $dompdf->render();
        return $dompdf->stream('order-' . $order->order_id . '-receipt.pdf');
    }

    /**
     * Display all orders for the current logged-in user
     */
    public function userOrders()
    {
        $user = Auth::user();
        
        if (!$user->account) {
            return redirect()->route('dashboard')
                ->with('error', 'No account found for this user.');
        }
        
        $orders = Order::where('account_id', $user->account->account_id)
            ->orderBy('date_ordered', 'desc')
            ->paginate(10);
            
        foreach ($orders as $order) {
            if (isset($order->date_ordered) && !($order->date_ordered instanceof \Carbon\Carbon)) {
                try {
                    $order->date_ordered = \Carbon\Carbon::parse($order->date_ordered);
                } catch (\Exception $e) {
                    // If we can't parse it, leave as is
                }
            }
        }
        
        return view('orders.user-orders', compact('orders'));
    }

    /**
     * Process a direct purchase for a single item (Buy Now)
     */
    public function buyNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,item_id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check item availability
        $item = Item::with('inventory')->find($request->item_id);
        if (!$item->inventory || $item->inventory->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock for item: ' . $item->item_name
            ], 422);
        }

        // Calculate total amount
        $totalAmount = $item->price * $request->quantity;

        // Create order
        $order = Order::create([
            'account_id' => Auth::user()->account->account_id,
            'date_ordered' => now(),
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ]);

        // Create order item and update inventory
        OrderInfo::create([
            'order_id' => $order->order_id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity
        ]);

        // Update inventory
        $item->inventory->update([
            'quantity' => $item->inventory->quantity - $request->quantity
        ]);

        // Send order confirmation email
        $this->sendOrderConfirmationEmail($order);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->order_id
            ]
        ]);
    }

    protected function sendOrderConfirmationEmail($order)
    {
        try {
            $order->load(['account.user', 'orderInfos.item']);
            
            // Generate PDF content using Dompdf
            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOptions(new \Dompdf\Options([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isPhpEnabled' => true,
            ]));
            
            $dompdf->loadHtml(view('orders.receipt', compact('order'))->render());
            $dompdf->render();
            $output = $dompdf->output();
            
            // Generate a temp filename
            $filename = 'order-' . $order->order_id . '-receipt-' . time() . '.pdf';
            $tempPath = storage_path('app/public/temp/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save PDF to temp file
            file_put_contents($tempPath, $output);
            
            // Use simple Mail facade to send email with attachment
            Mail::send('emails.order-confirmation', ['order' => $order], function($message) use ($order, $tempPath, $filename) {
                $message->to($order->account->user->email)
                       ->subject('Order Confirmation - #' . $order->order_id)
                       ->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                       ]);
            });
            
            // Delete temp file after sending
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            Log::info('Order confirmation email sent successfully for order #' . $order->order_id);
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    protected function sendOrderStatusUpdateEmail($order)
    {
        try {
            $order->load(['account.user', 'orderInfos.item']);
            
            // Generate PDF content using Dompdf
            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOptions(new \Dompdf\Options([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isPhpEnabled' => true,
            ]));
            
            $dompdf->loadHtml(view('orders.receipt', compact('order'))->render());
            $dompdf->render();
            $output = $dompdf->output();
            
            // Generate a temp filename
            $filename = 'order-' . $order->order_id . '-receipt-' . time() . '.pdf';
            $tempPath = storage_path('app/public/temp/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save PDF to temp file
            file_put_contents($tempPath, $output);
            
            // Use simple Mail facade to send email with attachment
            Mail::send('emails.order-status-update', ['order' => $order], function($message) use ($order, $tempPath, $filename) {
                $message->to($order->account->user->email)
                       ->subject('Order Status Update - #' . $order->order_id)
                       ->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                       ]);
            });
            
            // Delete temp file after sending
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            Log::info('Order status update email sent successfully for order #' . $order->order_id);
        } catch (\Exception $e) {
            Log::error('Failed to send order status update email: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
