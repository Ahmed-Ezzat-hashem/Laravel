<?php

namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all items from the cart associated with the authenticated user
        $user = Auth::user();
        $cartItems = $user->Cart()->with('product')->get();
        if ($cartItems->isNotEmpty()) {
            return response()->json([
                'status' => 200,
                'cartItems' => $cartItems,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No cart items found for the authenticated user.',
            ], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create a new cart item associated with the authenticated user
        $cartItem = $user->Cart()->create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'status' => 200,
            'cart_item' => $cartItem,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate request data
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the cart item by ID
        $cartItem = Cart::findOrFail($id);

        // Update the quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'status' => 200,
            'cart_item' => $cartItem,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the cart item by ID and delete it
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Cart item deleted successfully'], 200);
    }



    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cartItems = $user->cart()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'No items in the cart'], 404);
        }

        // Group cart items by pharmacy ID
        $groupedCartItems = $cartItems->groupBy('product.pharmacy_id');

        // Process each group of cart items
        foreach ($groupedCartItems as $pharmacyId => $items) {
            // Calculate total amount for this group of cart items
            $totalAmount = $this->calculateTotalAmount($items);

            // Create a new order for this pharmacy
            $order = Order::create([
                'user_id' => $user->id,
                'pharmacy_id' => $pharmacyId,
                'total_amount' => $totalAmount,
                'status' => 'New Order',

                'tracking_number' => $request->input('tracking_number'),
                'country' => $request->input('country'),
                'street_name' => $request->input('street_name'),
                'city' => $request->input('city'),
                'state_province' => $request->input('state_province'),
                'zip_code' => $request->input('zip_code'),
                'phone_number' => $request->input('phone_number'),
                'coupon_code' => $request->input('coupon_code'),
            ]);

            // Create order products
            foreach ($items as $cartItem) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }
        }

        // Delete cart items
        $user->cart()->delete();

        return response()->json(['message' => 'Order(s) created successfully'], 201);
    }

    /**
     * Calculate the total amount for the order.
     */
    private function calculateTotalAmount($cartItems)
    {
        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->product->price * $cartItem->quantity;
        }

        return $totalAmount;
    }
}
