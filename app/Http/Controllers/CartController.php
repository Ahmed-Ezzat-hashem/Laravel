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
        $cartItems = $user->Cart()->get();
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

        return response()->json(['cart_item' => $cartItem], 201);
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

        return response()->json(['cart_item' => $cartItem], 200);
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



    public function checkout()
    {
        $user = Auth::user();
        $cartItems = $user->Cart()->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'No items in the cart'], 404);
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->product->price * $cartItem->quantity;
        }

        // Create a new order
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Create order products
        foreach ($cartItems as $cartItem) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ]);
        }

        // Delete cart items
        $user->Cart()->delete();

        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    /**
     * Calculate the total amount for the order.
     */
    private function calculateTotalAmount($cartItems)
    {
        // Implement the logic to calculate the total amount based on cart items
        // You can sum the prices of all items in the cart, apply discounts, etc.
        // For demonstration, let's assume each product has a price attribute

        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->product->price * $cartItem->quantity;
        }

        return $totalAmount;
    }
}
