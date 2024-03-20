<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderProduct = OrderProduct::create($validatedData);

        return response()->json($orderProduct, 201);
    }

    public function update(Request $request, string $id)
    {
        $orderProduct = OrderProduct::findOrFail($id);

        $validatedData = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $orderProduct->update($validatedData);

        return response()->json($orderProduct, 200);
    }

    public function destroy(string $id)
    {
        $orderProduct = OrderProduct::findOrFail($id);
        $orderProduct->delete();

        return response()->json(null, 204);
    }

    public function getOrderItems($orderId)
    {
        $orderItems = OrderProduct::where('order_id', $orderId)->with('product')->get();
        return response()->json(['order_items' => $orderItems], 200);
    }


    public function calculateTotalPrice($orderId)
    {
        $orderItems = OrderProduct::where('order_id', $orderId)->get();
        $totalPrice = 0;

        foreach ($orderItems as $orderItem) {
            $product = Product::find($orderItem->product_id);
            if ($product) {
                $totalPrice += $product->price * $orderItem->quantity;
            }
        }

        return response()->json(['total_price' => $totalPrice], 200);
    }
}
