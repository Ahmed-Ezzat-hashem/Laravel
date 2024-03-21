<?php
namespace App\Http\Controllers;

use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $favoriteProducts = $user->favoriteProducts()->with('product')->get();

        return response()->json(['favoriteProducts' => $favoriteProducts], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Check if the product is already in the user's favorites
        if ($user->favoriteProducts()->where('product_id', $request->product_id)->exists()) {
            return response()->json(['message' => 'Product already in favorites.'], 200);
        }

        // Add the product to the user's favorites
        $favoriteProduct = new FavoriteProduct();
        $favoriteProduct->user_id = $user->id;
        $favoriteProduct->product_id = $request->product_id;
        $favoriteProduct->save();

        return response()->json(['message' => 'Product added to favorites successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;
        $favoriteProduct = FavoriteProduct::where('user_id', $userId)
            ->where('product_id', $id)
            ->first();

        if (!$favoriteProduct) {
            return response()->json(['message' => 'Favorite product not found'], 404);
        }

        $favoriteProduct->delete();

        return response()->json(['message' => 'Product removed from favorites successfully']);
    }
}


