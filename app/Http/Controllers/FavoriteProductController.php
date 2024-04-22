<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\FavoriteProduct;
use App\Models\Product;

class FavoriteProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Retrieve favorite products directly based on user ID, eagerly loading the product and pharmacy relationships
            $favoriteProducts = FavoriteProduct::where('user_id', $user->id)
                ->with('product.pharmacy')  // Eager load the product and pharmacy relationships
                ->get();

            // Transform the favorite products to include the necessary information
            $transformedFavoriteProducts = $favoriteProducts->map(function ($favoriteProduct) {
                return [
                    'id' => $favoriteProduct->id,
                    'quantity' => $favoriteProduct->quantity,
                    'created_at' => $favoriteProduct->created_at,
                    'updated_at' => $favoriteProduct->updated_at,
                    'product' => [
                        'id' => $favoriteProduct->product->id,
                        'pharmacy_id' => $favoriteProduct->product->pharmacy_id,
                        'pharmacy_name' => $favoriteProduct->product->pharmacy->name, // Include pharmacy name
                        'category_id' => $favoriteProduct->product->category_id,
                        'category' => $favoriteProduct->product->category,
                        'description' => $favoriteProduct->product->description,
                        'rating' => $favoriteProduct->product->rating,
                        'ratings_number' => $favoriteProduct->product->ratings_number,
                        'price' => $favoriteProduct->product->price,
                        'discount' => $favoriteProduct->product->discount,
                        'name' => $favoriteProduct->product->name,
                        'effective_material' => $favoriteProduct->product->effective_material,
                        'code' => $favoriteProduct->product->code,
                        'image' => $favoriteProduct->product->image,
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        // Include other product attributes as needed
                    ],
                ];
            });

            // Return the response with the transformed data
            return response()->json(['favoriteProducts' => $transformedFavoriteProducts], 200);
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

    public function store(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            // Handle validation errors
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }

            // Check if the product is already in the user's favorites
            $existingFavoriteProduct = FavoriteProduct::where('product_id', $request->product_id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingFavoriteProduct) {
                return response()->json(['message' => 'Product already in favorites.'], 200);
            }

            // Add the product to the user's favorites
            $favoriteProduct = FavoriteProduct::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);

            // Retrieve the updated list of favorite products for the user
            $favoriteProducts = FavoriteProduct::where('user_id', $user->id)
                ->with('product.pharmacy')
                ->get();

            // Transform the favorite products to include the necessary information
            $transformedFavoriteProducts = $favoriteProducts->map(function ($favoriteProduct) {
                return [
                    'id' => $favoriteProduct->id,
                    'quantity' => $favoriteProduct->quantity,
                    'created_at' => $favoriteProduct->created_at,
                    'updated_at' => $favoriteProduct->updated_at,
                    'product' => [
                        'id' => $favoriteProduct->product->id,
                        'pharmacy_id' => $favoriteProduct->product->pharmacy_id,
                        'pharmacy_name' => $favoriteProduct->product->pharmacy->name,
                        'category_id' => $favoriteProduct->product->category_id,
                        'category' => $favoriteProduct->product->category,
                        'description' => $favoriteProduct->product->description,
                        'rating' => $favoriteProduct->product->rating,
                        'ratings_number' => $favoriteProduct->product->ratings_number,
                        'price' => $favoriteProduct->product->price,
                        'discount' => $favoriteProduct->product->discount,
                        'name' => $favoriteProduct->product->name,
                        'effective_material' => $favoriteProduct->product->effective_material,
                        'code' => $favoriteProduct->product->code,
                        'image' => $favoriteProduct->product->image,
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        // Add other product attributes as needed
                    ],
                ];
            });

            // Fetch the updated list of products with relationships
            $products = Product::with('pharmacy')
                ->get();

            // Transform the products to include the necessary information
            $transformedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'description' => $product->description,
                    'effective_material' => $product->effective_material,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'category_id' => $product->category_id,
                    'category' => $product->category,
                    'pharmacy_id' => $product->pharmacy_id,
                    'pharmacy_name' => $product->pharmacy->name,
                    'image' => $product->image,
                    'is_favorite' => FavoriteProduct::where('product_id', $product->id)
                        ->where('user_id',Auth::Id())
                        ->exists(),
                    'type'=> $product->type,
                    'product_origin'=> $product->product_origin,
                    'about'=> $product->about,
                    'title'=> $product->title,
                    // Add other product attributes as needed
                ];
            });

            // Return the response with the message and the updated list of favorite products and products
            return response()->json([
                'message' => 'Product added to favorites successfully',
                'favoriteProducts' => $transformedFavoriteProducts,
                'products' => $transformedProducts,
            ], 200);
        } catch (\Throwable $th) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        try{
            $user = Auth::User();
            $userId = Auth::Id();
            $favoriteProduct = FavoriteProduct::where('user_id', $userId)
                ->where('product_id', $id)
                ->first();

            if (!$favoriteProduct) {
                return response()->json(['error' => 'Favorite product not found'], 404);
            }

            $favoriteProduct->delete();
            // Retrieve the updated list of favorite products for the user
            $favoriteProducts = FavoriteProduct::where('user_id', $user->id)
                ->with('product.pharmacy')
                ->get();

            // Transform the favorite products to include the necessary information
            $transformedFavoriteProducts = $favoriteProducts->map(function ($favoriteProduct) {
                return [
                    'id' => $favoriteProduct->id,
                    'quantity' => $favoriteProduct->quantity,
                    'created_at' => $favoriteProduct->created_at,
                    'updated_at' => $favoriteProduct->updated_at,
                    'product' => [
                        'id' => $favoriteProduct->product->id,
                        'pharmacy_id' => $favoriteProduct->product->pharmacy_id,
                        'pharmacy_name' => $favoriteProduct->product->pharmacy->name,
                        'category_id' => $favoriteProduct->product->category_id,
                        'category' => $favoriteProduct->product->category,
                        'description' => $favoriteProduct->product->description,
                        'rating' => $favoriteProduct->product->rating,
                        'ratings_number' => $favoriteProduct->product->ratings_number,
                        'price' => $favoriteProduct->product->price,
                        'discount' => $favoriteProduct->product->discount,
                        'name' => $favoriteProduct->product->name,
                        'effective_material' => $favoriteProduct->product->effective_material,
                        'code' => $favoriteProduct->product->code,
                        'image' => $favoriteProduct->product->image,
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        // Add other product attributes as needed
                    ],
                ];
            });

            // Fetch the updated list of products with relationships
            $products = Product::with('pharmacy')
                ->get();

            // Transform the products to include the necessary information
            $transformedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'description' => $product->description,
                    'effective_material' => $product->effective_material,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'category_id' => $product->category_id,
                    'category' => $product->category,
                    'pharmacy_id' => $product->pharmacy_id,
                    'pharmacy_name' => $product->pharmacy->name,
                    'image' => $product->image,
                    'is_favorite' => FavoriteProduct::where('product_id', $product->id)
                        ->where('user_id',Auth::Id())
                        ->exists(),
                    'type'=> $product->type,
                    'product_origin'=> $product->product_origin,
                    'about'=> $product->about,
                    'title'=> $product->title,
                    // Add other product attributes as needed
                ];
            });



            return response()->json([
                'message' => 'Product removed from favorites successfully',
                'favoriteProducts' => $transformedFavoriteProducts,
                'products' => $transformedProducts,]);
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


