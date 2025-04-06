<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderInfo;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Dompdf\Dompdf;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cartItems = Cart::with(['item.images', 'item.inventory'])
            ->where('account_id', Auth::user()->account->account_id)
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $subtotal = $cartItems->sum(function($cart) {
            return $cart->item->price * $cart->quantity;
        });
        
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping;
        
        // Check item availability
        $unavailableItems = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->item->inventory || $cartItem->item->inventory->quantity < $cartItem->quantity) {
                $unavailableItems[] = [
                    'item_id' => $cartItem->item_id,
                    'item_name' => $cartItem->item->item_name,
                    'available_quantity' => $cartItem->item->inventory ? $cartItem->item->inventory->quantity : 0,
                    'requested_quantity' => $cartItem->quantity
                ];
            }
        }
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total', 'unavailableItems'));
    }
    
    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'terms' => 'required'
        ]);
        
        $cartItems = Cart::with(['item.inventory'])
            ->where('account_id', Auth::user()->account->account_id)
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        // Check item availability
        $unavailableItems = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->item->inventory || $cartItem->item->inventory->quantity < $cartItem->quantity) {
                $unavailableItems[] = $cartItem->item->item_name;
            }
        }
        
        if (!empty($unavailableItems)) {
            return redirect()->route('checkout.index')->with('error', 'Some items are no longer available: ' . implode(', ', $unavailableItems));
        }
        
        // Process payment (simplified)
        $total = $cartItems->sum(function($cart) {
            return $cart->item->price * $cart->quantity;
        });
        
        try {
            DB::beginTransaction();
            
            // Create order
            $order = Order::create([
                'account_id' => Auth::user()->account->account_id,
                'date_ordered' => now(),
                'total_amount' => $total,
                'status' => 'pending' // Set status as pending until payment is confirmed
            ]);
            
            // Create order items and update inventory
            foreach ($cartItems as $cartItem) {
                OrderInfo::create([
                    'order_id' => $order->order_id,
                    'item_id' => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                    'created' => now()
                ]);
                
                // Update inventory
                $inventory = Inventory::where('item_id', $cartItem->item_id)->first();
                if ($inventory) {
                    $inventory->quantity -= $cartItem->quantity;
                    $inventory->save();
                }
            }
            
            // Clear cart
            Cart::where('account_id', Auth::user()->account->account_id)->delete();
            
            DB::commit();
            
            // Send order confirmation email with PDF receipt
            try {
                // Generate PDF receipt
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
                Mail::send('emails.order-receipt', ['order' => $order], function($message) use ($order, $tempPath, $filename) {
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
                // Log email error but don't fail the order
                Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
            
            return redirect()->route('user.orders')->with('success', 'Order placed successfully! Order #' . $order->order_id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}