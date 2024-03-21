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
        $product = Product::all();
        if($product->count()>0){
        return response()->json([
            'status'=> 200,
            'product'=> $product,
    ],200);
        }else{
        return response()->json([
            'status'=> 404,
            'message'=> 'no products found',
            ],404);
        }
    }

    public function bycat($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();

        if ($products->count() > 0) {
            return response()->json([
                'status' => 200,
                'products' => $products,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No products found for the given category ID.',
            ], 404);
        }
    }

    public function ProductPharmacy(Request $request)
    {

        // Get the pharmacyId of user
        $pharmacyId = Auth::user()->pharmacy_id;

        // Retrieve users with the same pharmacy_id
        $products = DB::table('Product')
                ->select(
                'id',
                'category',
                'email',
                'name',
                'code',
                'description',
                'effective_material',
                'price',
                'discount',
                'image',)
                ->where('pharmacy_id', $pharmacyId)
                ->get();

        if ($products->count() > 0) {
            return response()->json([
                'status' => 200,
                'products' => $products,
                    ], 200);
            } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No products found for the pharmacy.',
                    ], 404);
                    }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            return response()->json(['error' => $validator->errors()], 400);
        }

        $product = Product::create([
            'category_id' => $request->category_id,
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
            $product->image = url('/images/product/' . $filename);
            $product->save();
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
        $product = Product::where('id', $id)->get();
        if($product->count()>0){
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
            }else{
            return response()->json([
                'status'=> 404,
                'message'=> 'no products found',
                ],404);
            }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
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
            return response()->json(['error' => $validator->errors()], 400);
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
            $product->image = url('/images/product/' . $filename);
        }

        $product->save();


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

        $products = Product::with('Images')
            ->where('name', 'LIKE', "%{$name}%")
            ->get();

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



    public function searchByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $code = $request->input('code');

        $products = Product::with('Images')
            ->where('code', $code)
            ->get();

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



    public function searchByColorAndShape(Request $request)
    {
        $request->validate([
            'color' => 'required|string',
            'shape' => 'required|string'
        ]);

        $color = $request->input('color');
        $shape = $request->input('shape');

        $products = Product::with('Images')
            ->where('status', '=', 'published')
            ->where('color', $color)
            ->where('shap', $shape)
            ->get();

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
}
