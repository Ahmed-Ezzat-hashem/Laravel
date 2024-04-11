<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\FavoriteProduct;

class FavoriteProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Retrieve favorite products directly based on user ID
            $favoriteProducts = FavoriteProduct::where('user_id', $user->id)
                ->with('product')
                ->get();

            return response()->json(['favoriteProducts' => $favoriteProducts], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }

            // Check if the product is already in the user's favorites
            if (FavoriteProduct::where('product_id', $request->product_id)
                                ->where('user_id', $user->id)
                                ->exists()) {
                return response()->json(['error' => 'Product already in favorites.'], 200);
            }

            // Add the product to the user's favorites
            $favoriteProduct = FavoriteProduct::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);

            return response()->json(['message' => 'Product added to favorites successfully']);
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


    public function destroy(Request $request, $id)
    {
        try{

            $userId = Auth::Id();
            $favoriteProduct = FavoriteProduct::where('user_id', $userId)
                ->where('product_id', $id)
                ->first();

            if (!$favoriteProduct) {
                return response()->json(['error' => 'Favorite product not found'], 404);
            }

            $favoriteProduct->delete();



            return response()->json(['message' => 'Product removed from favorites successfully']);
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
}


