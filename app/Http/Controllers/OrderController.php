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
        $dompdf->loadHtml(view('orders.receipt', compact('order'))->render());
        $dompdf->render();
        return $dompdf->stream('order-' . $order->order_id . '-receipt.pdf');
    }

    protected function sendOrderConfirmationEmail($order)
    {
        $order->load(['account.user', 'orderInfos.item']);
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('emails.order-receipt', compact('order'))->render());
        $dompdf->render();

        Mail::send('emails.order-confirmation', ['order' => $order], function($message) use ($order, $dompdf) {
            $message->to($order->account->user->email)
                ->subject('Order Confirmation - #' . $order->order_id)
                ->attachData($dompdf->output(), 'order-receipt.pdf');
        });
    }

    protected function sendOrderStatusUpdateEmail($order)
    {
        $order->load(['account.user', 'orderInfos.item']);
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('emails.order-receipt', compact('order'))->render());
        $dompdf->render();

        Mail::send('emails.order-status-update', ['order' => $order], function($message) use ($order, $dompdf) {
            $message->to($order->account->user->email)
                ->subject('Order Status Update - #' . $order->order_id)
                ->attachData($dompdf->output(), 'order-receipt.pdf');
        });
    }
}
