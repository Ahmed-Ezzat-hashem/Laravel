<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ExceptionHandlingTrait;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try{

            // Retrieve the authenticated user
            $user = Auth::user();

            // Retrieve orders based on user role
            if ($user->role == 0) {
                // Retrieve all orders associated with the user by their ID
                $orders = Order::where('user_id', $user->id)->with('orderProducts');
            } else {
                // Retrieve all orders associated with the pharmacy by its ID
                $orders = Order::where('pharmacy_id', $user->pharmacy_id)->with('orderProducts');
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

    public function updateOrderStatus(Request $request, string $id)
    {
        try{

            // Find the order by ID
            $order = Order::findOrFail($id);

            // Perform validation on the request data
            $validatedData = $request->validate([
                'status' => 'required|in:new_order,complete,rejected',
            ]);

            // Update the order status
            $order->status = $validatedData['status'];
            $order->save();

            return response()->json(['message' => 'Order status updated successfully', 'order' => $order], 200);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{

            // Find the order by ID
            $order = Order::findOrFail($id);

            // Delete the associated order products
            $order->orderProducts()->delete();

            // Delete the order
            $order->delete();

            return response()->json(['message' => 'Order and associated products deleted successfully'], 200);
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
        }
    }


}
