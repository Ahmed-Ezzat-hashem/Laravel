<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user is a pharmacy user (role = 1)
        if ($user->role == 1) {
            // Retrieve all orders associated with the pharmacy by its ID
            $orders = Order::where('pharmacy_id', $user->pharmacy_id)->get();
        } else {
            // Retrieve all orders associated with the user by their ID
            $orders = Order::where('user_id', $user->id)->get();
        }

        return response()->json(['status' => 200, 'orders' => $orders], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     // Perform validation on the request data
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'total_amount' => 'required|numeric|min:0',
    //         'status' => 'required|in:pending,processing,completed,cancelled',
    //         'tracking_number' => 'nullable|string',
    //         'country' => 'nullable|string',
    //         'street_name' => 'nullable|string',
    //         'city' => 'nullable|string',
    //         'state_province' => 'nullable|string',
    //         'zip_code' => 'nullable|string',
    //         'phone_number' => 'nullable|string',
    //         'coupon_code' => 'nullable|string',
    //     ]);

    //     // Create a new order with the validated data
    //     $order = Order::create($request->all());

    //     return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    // }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the order by ID
        $order = Order::findOrFail($id);

        // Perform validation on the request data
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            // Add validation rules for other fields if needed
        ]);

        // Update the order attributes with the validated data
        $order->update($request->all());

        return response()->json(['message' => 'Order updated successfully', 'order' => $order], 200);
    }


    public function updateOrderStatus(Request $request, string $id)
    {
        // Find the order by ID
        $order = Order::findOrFail($id);

        // Perform validation on the request data
        $validatedData = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        // Update the order status
        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the order by ID
        $order = Order::findOrFail($id);

        // Delete the associated order products
        $order->orderProducts()->delete();

        // Delete the order
        $order->delete();

        return response()->json(['message' => 'Order and associated products deleted successfully'], 200);
    }
}
