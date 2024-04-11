<?php

namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Retrieve all items from the cart associated with the authenticated user
            $user = Auth::user();
            $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

            if ($cartItems->isNotEmpty()) {
                // Transform each cart item into an object with additional product information
                $transformedCartItems = $cartItems->map(function ($cartItem) {
                    return [
                        'id' => $cartItem->id,
                        'quantity' => $cartItem->quantity,
                        'created_at' => $cartItem->created_at,
                        'updated_at' => $cartItem->updated_at,
                        'product' => [
                            'id' => $cartItem->product->id,
                            'pharmacy_id' => $cartItem->product->pharmacy_id,
                            'category_id' => $cartItem->product->category_id,
                            'category' => $cartItem->product->category,
                            'description' => $cartItem->product->description,
                            'rating' => $cartItem->product->rating,
                            'ratings_number' => $cartItem->product->ratings_number,
                            'price' => $cartItem->product->price,
                            'discount' => $cartItem->product->discount,
                            'name' => $cartItem->product->name,
                            'effective_material' => $cartItem->product->effective_material,
                            'code' => $cartItem->product->code,
                            'image' => $cartItem->product->image,
                            // Add other product attributes as needed
                        ]
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'cartItems' => $transformedCartItems,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No cart items found for the authenticated user.',
                ], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            return response()->json([
                'error' => $errorMessage,
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            // Validate request data
            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            // Get the authenticated user
            $user = Auth::user();

            // Create a new cart item associated with the authenticated user
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);

            return response()->json([
                'status' => 200,
                'cart_item' => $cartItem,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            return response()->json([
                'error' => $errorMessage,
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
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
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            return response()->json([
                'error' => $errorMessage,
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userId = Auth::Id();
            // Find the cart item by ID and delete it
            $cartItem = Cart::findOrFail($id);

            $cartItem->delete();
            $cartItems = Cart::where('user_id',$userId)->with('product')->get();
            if ($cartItems->isNotEmpty()) {
                // Transform each cart item into an object with additional product information
                $transformedCartItems = $cartItems->map(function ($cartItem) {
                    return [
                        'id' => $cartItem->id,
                        'quantity' => $cartItem->quantity,
                        'created_at' => $cartItem->created_at,
                        'updated_at' => $cartItem->updated_at,
                        'product' => [
                            'id' => $cartItem->product->id,
                            'pharmacy_id' => $cartItem->product->pharmacy_id,
                            'category_id' => $cartItem->product->category_id,
                            'category' => $cartItem->product->category,
                            'description' => $cartItem->product->description,
                            'rating' => $cartItem->product->rating,
                            'ratings_number' => $cartItem->product->ratings_number,
                            'price' => $cartItem->product->price,
                            'discount' => $cartItem->product->discount,
                            'name' => $cartItem->product->name,
                            'effective_material' => $cartItem->product->effective_material,
                            'code' => $cartItem->product->code,
                            'image' => $cartItem->product->image,
                            // Add other product attributes as needed
                        ]
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'message' => 'Cart item deleted successfully',
                    'cartItems' => $transformedCartItems,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No cart items found for the authenticated user.',
                ], 404);
            }

        } catch (ModelNotFoundException $exception) {

            return response()->json(['error' => 'Cart item not found'], 404);

        }catch (\Illuminate\Validation\ValidationException $exception) {
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            return response()->json([
                'error' => $errorMessage,
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }



    public function checkout(Request $request)
    {
        try{
            $user = Auth::user();
            $cartItems = Cart::where('user_id',$user->id)->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'No items in the cart'], 404);
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
                    'status' => 'new_order',
                    'customer' =>$user->user_name,

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
                        'price' => $cartItem->product->price,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                    ]);
                }
            }

            // Delete cart items
            Cart::where('user_id', $user->id)->delete();

            return response()->json(['message' => 'Order(s) created successfully'], 201);

        } catch (\Illuminate\Validation\ValidationException $exception) {
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            return response()->json([
                'error' => $errorMessage,
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
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
