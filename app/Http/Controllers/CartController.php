<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cartItems = Cart::with(['item.images', 'item.inventory'])
                ->where('account_id', Auth::user()->account->account_id);
            
            return DataTables::of($cartItems)
                ->addColumn('item_image', function($cart) {
                    $image = $cart->item->images()->where('is_primary', true)->first();
                    return $image ? asset('storage/' . $image->image_path) : null;
                })
                ->addColumn('total_price', function($cart) {
                    return $cart->item->price * $cart->quantity;
                })
                ->addColumn('action', function($cart) {
                    return view('cart.actions', compact('cart'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Get cart items directly and pass to the view
        $cartItems = Cart::with(['item.images', 'item.inventory'])
            ->where('account_id', Auth::user()->account->account_id)
            ->get();
            
        // Calculate subtotal
        $subtotal = $cartItems->sum(function($cart) {
            return $cart->item->price * $cart->quantity;
        });
        
        return view('cart.index', compact('cartItems', 'subtotal'));
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
            'item_id' => 'required|exists:items,item_id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $item = Item::with('inventory')->find($request->item_id);
        
        if (!$item->inventory || $item->inventory->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 422);
        }

        $cartItem = Cart::updateOrCreate(
            [
                'account_id' => Auth::user()->account->account_id,
                'item_id' => $request->item_id
            ],
            [
                'quantity' => $request->quantity,
                'date_placed' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $cartItem
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, Cart $cart)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $item = Item::with('inventory')->find($cart->item_id);
        
        if (!$item->inventory || $item->inventory->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 422);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => $cart
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully'
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        Cart::where('account_id', Auth::user()->account->account_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart items
     */
    public function getItems()
    {
        $cartItems = Cart::with(['item.images', 'item.inventory'])
            ->where('account_id', Auth::user()->account->account_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems
        ]);
    }

    /**
     * Get cart total
     */
    public function getTotal()
    {
        $total = Cart::with('item')
            ->where('account_id', Auth::user()->account->account_id)
            ->get()
            ->sum(function($cart) {
                return $cart->item->price * $cart->quantity;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total
            ]
        ]);
    }

    /**
     * Check cart items availability
     */
    public function checkAvailability()
    {
        $cartItems = Cart::with(['item.inventory'])
            ->where('account_id', Auth::user()->account->account_id)
            ->get();

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

        return response()->json([
            'success' => true,
            'data' => [
                'unavailable_items' => $unavailableItems,
                'has_unavailable_items' => !empty($unavailableItems)
            ]
        ]);
    }

    /**
     * Get cart item by item_id
     */
    public function getCartByItemId($itemId)
    {
        $cart = Cart::where('item_id', $itemId)
            ->where('account_id', Auth::user()->account->account_id)
            ->firstOrFail();
            
        return $cart;
    }

    /**
     * Update cart item by item_id
     */
    public function updateByItemId(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $cart = $this->getCartByItemId($itemId);
        $item = Item::with('inventory')->find($itemId);
        
        if (!$item->inventory || $item->inventory->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 422);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => $cart
        ]);
    }

    /**
     * Remove cart item by item_id
     */
    public function destroyByItemId($itemId)
    {
        $cart = $this->getCartByItemId($itemId);
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully'
        ]);
    }
}
