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


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $imageBaseUrl = env('APP_URL') ; // Retrieve the base URL from .env

        if ($products->count() > 0) {
            $productsArray = $products->map(function ($product) use ($imageBaseUrl) {
                $imageUrl = $imageBaseUrl . $product->image;
                return [
                    'id' => $product->id,
                    'category' => $product->category,
                    'email' => $product->email,
                    'name' => $product->name,
                    'code' => $product->code,
                    'description' => $product->description,
                    'effective_material' => $product->effective_material,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'image' => $imageUrl,
                ];
            });

            return response()->json([
                'status' => 200,
                'products' => $productsArray,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No products found',
            ], 404);
        }
    }


    public function ProductPharmacy(Request $request)
    {
        try {

            // Get the pharmacyId of the user
            $pharmacyId = Auth::user()->pharmacy_id;

            // Retrieve products with the same pharmacy_id
            $products = DB::table('products')
                ->select(
                    'id',
                    'category',
                    'name',
                    'code',
                    'description',
                    'effective_material',
                    'price',
                    'discount',
                    'image'
                )
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            $imageBaseUrl = env('APP_URL')  ; // Retrieve the base URL from .env

            if ($products->count() > 0) {
                $productsArray = $products->map(function ($product) use ($imageBaseUrl) {
                    $imageUrl = $imageBaseUrl . $product->image;
                    return [
                        'id' => $product->id,
                        'category' => $product->category,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'effective_material' => $product->effective_material,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => $imageUrl,
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 404,
                'error' => 'mshkal in try catch',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $pharmacyId = Auth::user()->pharmacy_id;

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'discount' => 'required|numeric',
            'effective_material' => 'required',
            'code' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => "validation error"], 400);
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
            $product->image = env('APP_URL')   . '/images/product/' . $filename;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product created successfully',
            'product' => $product,
        ], 200);
    }



    /**
     * Display the specified resource.
     */


    public function show($id)
    {
        $product = Product::find($id);
        if($product) {
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
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'No product found',
            ], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'discount' => 'required|numeric',
            'effective_material' => 'required',
            'code' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => "validation error"], 400);
        }
        $product->update([
            'category_id'=> $request->category_id,
            'name'=> $request->name,
            'description'=> $request->description,
            'price'=> $request->price,
            'discount'=> $request->discount,
            'effective_material'=> $request->effective_material,
            'code'=> $request->code,
        ]);

        if ($product->image) {
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
        $product->image = url('/images/product/' . $filename);

        return response()->json([
            'status'=> 200,
            'product'=>[
                'id'=>$Product->id,
                'category'=>$Product->category,
                'email'=>$Product->email,
                'name'=>$Product->name,
                'code'=>$Product->code,
                'description'=>$Product->description,
                'effective_material'=>$Product->effective_material,
                'price'=>$Product->price,
                'discount'=>$Product->discount,
                'image'=>$Product->image,
                ]
        ],200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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
