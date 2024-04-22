<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\category;
use App\Models\Pharmacy;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteProduct;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Retrieve all products
            $products = Product::with('pharmacy')->get();

            // Get the ID of the currently authenticated user
            $userId = Auth::id();

            // Map the products to an array with additional information
            if ($products->count() > 0) {
                $productsArray = $products->map(function ($product) use ($userId) {
                    // Check if the current product is in the user's favorites
                    $isFavorite = FavoriteProduct::where('user_id', $userId)
                        ->where('product_id', $product->id)
                        ->exists();

                    // Construct the product array
                    return [
                        'id' => $product->id,
                        'pharmacy_name'=> $product->pharmacy->name,
                        'category_title' => $product->category,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'effective_material' => $product->effective_material,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => url($product->image),
                        'is_favorite' => $isFavorite, // Add the is_favorite field
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                    ];
                });

                // Return the products array in the response
                return response()->json([
                    'products' => $productsArray,
                ], 200);
            } else {
                // Return a 404 response if no products are found
                return response()->json([
                    'status' => 404,
                    'message' => 'No products found',
                ], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            // Handle validation errors
            $validator = $exception->validator;
            $messages = [];
            foreach ($validator->errors()->all() as $error) {
                $messages[] = $error;
            }
            $errorMessage = implode(' and ', $messages);

            // Return a response with the validation error
            return response()->json([
                'error' => $errorMessage,
            ], 400);
        }
    }


    public function ProductPharmacy(Request $request)
    {
        try {

            // Get the pharmacyId of the user
            $pharmacyId = Auth::user()->pharmacy_id;

            // Retrieve products with the same pharmacy_id
            $products = Product::with('pharmacy')
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            if ($products->count() > 0) {
                $productsArray = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'pharmacy_name'=> $product->pharmacy->name,
                        'category_title' => $product->category,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'effective_material' => $product->effective_material,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => url($product->image),
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        'is_favorite' => FavoriteProduct::where('product_id', $product->id)
                        ->where('user_id',Auth::Id())
                        ->exists(),
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'products' => $productsArray,
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No products found for the pharmacy.',
                ], 200);
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
        }
    }

    public function ProductPharmacyUser(Request $request, $id)
    {
        try {
            // Get the pharmacyId of the user
            $pharmacyId = $id;

            // Retrieve products with the same pharmacy_id
            $products = DB::table('products')
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            if ($products->count() > 0) {
                $productsArray = $products->map(function ($product) use ($pharmacyId) {
                    $imageUrl =url($product->image);
                    return [
                        'id' => $product->id,
                        'pharmacy_name' => Pharmacy::find($pharmacyId)->name,
                        'category_title' => $product->category,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'effective_material' => $product->effective_material,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => $imageUrl,
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        'is_favorite' => FavoriteProduct::where('product_id', $product->id)
                            ->where('user_id', Auth::id())
                            ->exists(),
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'products' => $productsArray,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No products found for the pharmacy.',
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
        }
    }





    /**
     * Display the specified resource.
     */


    public function show($id)
    {
        try{

            $product = Product::find($id)->with('pharmacy');
            if($product) {
                return response()->json([
                    'status' => 200,
                    'product' => [
                        'id' => $product->id,
                        'pharmacy_name'=> $product->pharmacy->name,
                        'category' => $product->category,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'effective_material' => $product->effective_material,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => url($product->image),
                        'type'=> $product->type,
                        'product_origin'=> $product->product_origin,
                        'about'=> $product->about,
                        'title'=> $product->title,
                        'is_favorite' => FavoriteProduct::where('product_id', $product->id)
                        ->where('user_id',Auth::Id())
                        ->exists(),
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No product found',
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
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try{

            $pharmacyId = Auth::user()->pharmacy_id;

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:category',
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'discount' => 'required|numeric',
                //'product_origin' => 'required',
                'effective_material' => 'required',
                'code' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            if ($request->hasFile('image') == null) {
                return response()->json(['error' => 'image not valid',]);
            }
            $category = Category::find($request->category_id);
            if (!$category) {
                return response()->json(['error' => "Category not found"], 404);
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'category' => $category->title,
                'pharmacy_id'=>$pharmacyId,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'discount' => $request->discount,
                //'product_origin'=> $request->product_origin,
                'effective_material' => $request->effective_material,
                'code' => $request->code,
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/product');
                $file->move($path, $filename);
                $product->image = '/images/product/' . $filename;
                $product->save();
                //for the pharmacy to check the full path
                $product->image = url($product->image);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Product created successfully',
                'product' => $product,
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
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $user = Auth::user();
            $product = Product::findOrFail($id);

            if($product->pharmacy_id != $user->pharmacy_id) {
                return response()->json(['status'=> 400,'error' => 'not your pharmacy product'],400);
            }

            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'discount' => 'required|numeric',
                //'product_origin' => 'required',
                'effective_material' => 'required',
                'code' => 'required',
                'image' => 'nullable',
            ]);

            $product->update([
                'category_id'=> $request->category_id,
                'name'=> $request->name,
                'description'=> $request->description,
                'price'=> $request->price,
                'discount'=> $request->discount,
                //'product_origin'=> $request->product_origin,
                'effective_material'=> $request->effective_material,
                'code'=> $request->code,
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $oldImagePath = public_path('images/product/' . basename($product->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Handle file upload for the new image
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/product');
                $file->move($path, $filename);
                $product->image = '/images/product/' . $filename;
                $product->save();

            }

            $product->save();
                //for the pharmacy to check the full path
                if ($product->image != null) {
                    $product->image = url($product->image);
                }

            return response()->json([
                'status'=> 200,
                'product'=>[
                    'id'=>$product->id,
                    'category'=>$product->category,
                    'name'=>$product->name,
                    'code'=>$product->code,
                    'description'=>$product->description,
                    'product_origin'=>$product->product_origin,
                    'effective_material'=>$product->effective_material,
                    'price'=>$product->price,
                    'discount'=>$product->discount,
                    'image'=>$product->image,
                    ]
            ],200);
        }  catch (\Illuminate\Validation\ValidationException $exception) {
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
    public function destroy($id)
    {
        try{

            $product = Product::findOrFail($id);

            // Delete associated image(s) if they exist
            if ($product->image) {
                $oldImagePath = public_path('images/product/' . basename($product->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Delete the product from the database
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
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
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function bycat($categoryId)
    {
        try {
            // Retrieve products based on the category ID
            $products = Product::where('category_id', $categoryId)->get();

            if ($products->isEmpty()) {
                return response()->json(['error' => 'No products found for the specified category'], 404);
            }

            return response()->json(['products' => $products], 200);
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
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function searchByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $name = $request->input('name');

        // Retrieve product(s) by name
        $products = Product::where('name', 'LIKE', "%{$name}%")->get();

        // Check if products exist
        if ($products->isEmpty()) {
            return response()->json([
                'status' => 404,
                'error' => 'No products found with the given name.'
            ], 404);
        }

        // Iterate through each product to construct the response with image link
        $response = [];
        foreach ($products as $product) {
            $response[] = [
                'id' => $product->id,
                'category' => $product->category,
                'email' => $product->email,
                'name' => $product->name,
                'code' => $product->code,
                'description' => $product->description,
                'effective_material' => $product->effective_material,
                'price' => $product->price,
                'discount' => $product->discount,
                'image' => url($product->image),
            ];
        }

        return response()->json([
            'status' => 200,
            'products' => $response,
        ], 200);
    }

    public function searchByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $code = $request->input('code');

        // Retrieve product(s) by code
        $products = Product::where('code', $code)->get();

        // Check if products exist
        if ($products->isEmpty()) {
            return response()->json([
                'status' => 404,
                'error' => 'No products found with the given code.'
            ], 404);
        }

        // Construct the response with image link
        $product = $products->first(); // Assuming there's only one product with the given code
        return response()->json([
            'status' => 200,
            'product' => [
                'id' => $product->id,
                'category' => $product->category,
                'email' => $product->email,
                'name' => $product->name,
                'code' => $product->code,
                'description' => $product->description,
                'effective_material' => $product->effective_material,
                'price' => $product->price,
                'discount' => $product->discount,
                'image' => url($product->image),
            ],
        ], 200);
    }



    public function searchByColorAndShape(Request $request)
    {
        $request->validate([
            'color' => 'required|string',
            'shape' => 'required|string'
        ]);

        $color = $request->input('color');
        $shape = $request->input('shape');

        $products = Product::with('Images')
            ->where('color', $color)
            ->where('shape', $shape)
            ->get();

        if ($products->count() > 0) {
            return response()->json([
                'status' => 200,
                'products' => $products,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'No products found for the given color and shape.',
            ], 404);
        }
    }
}
