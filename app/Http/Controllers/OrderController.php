<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Retrieve orders based on user role
        if ($user->role == 0) {
            // Retrieve all orders associated with the user by their ID
            $orders = Order::where('user_id', $user->id);
        } else {
            // Retrieve all orders associated with the pharmacy by its ID
            $orders = Order::where('pharmacy_id', $user->pharmacy_id);
        }

        // Filter orders based on status if provided in the request
        if ($request->has('status')) {
            $status = $request->status;
            $orders->where('status', $status);
        }

        // Get the filtered orders
        $filteredOrders = $orders->get();

        return response()->json([
            'status' => 200,
            'orders' => $filteredOrders
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function updateOrderStatus(Request $request, string $id)
    {
        // Find the order by ID
        $order = Order::findOrFail($id);

        // Perform validation on the request data
        $validatedData = $request->validate([
            'status' => 'required|in::New Order,Complete,Rejected',
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
