<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('Images')->where('status', '=', 'published')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = new Product();
        $request->validate([
            'name'=>'required',
            'description'=>'required',
            'category'=>'required',
            'title' => 'required',
            'price' => 'required | numeric',
            'discount' => 'required | numeric',
            'type'=>'required',
            'product_origin'=>'required',
            'effective_material'=>'required',
            'color'=>'required',
            'shap'=>'required',
            'code'=>'required',
            'About' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $productCreated = Product::create($request->all());

        return $productCreated;
        // $productCreated = $product->create([
        //     'name'=> $request->name,
        //     'description' => $request->description,
        //     'category' => $request->category,
        //     'title' => $request->title,
        //     'price' => $request->price,
        //     'About' => $request->About,
        //     'discount' => $request->discount,
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Product::where('id', $id)->get();
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
            'name'=>'required',
            'description'=>'required',
            'category'=>'required',
            'price' => 'required | numeric',
            'discount' => 'required | numeric',
            'type'=>'required',
            'product_origin'=>'required',
            'effective_material'=>'required',
            'color'=>'required',
            'shap'=>'required',
            'code'=>'required | numeric',
            'About' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $product->update([
            'category' => $request->category,
            'title'  => $request->title,
            'description'  => $request->description,
            'rating'  => $request->rating,
            'ratings_number'  => $request->ratings_number,
            'price'  => $request->price,
            'discount'  => $request->discount,
            'About'  => $request->About,
            'status'  => $request->status,
            'name'  => $request->name,
            'type'  => $request->type,
            'product_origin'  => $request->product_origin,
            'effective_material'  => $request->effective_material,
            'color'  => $request->color,
            'shap'  => $request->shap,
            'code'  => $request->code,

        ]);
        $product->status = 'published';
        $product->save();
        $productId = $product->id;
        if ($request->hasFile('images')) {
            $files = $request->file("images");
            $i = 0;
            foreach ($files as $file) {
                $i = $i + 1;
                $image = new ProductImage();
                $image->product_id = $productId;
                $filename = date('YmdHis') . $i . '.' . $file->getClientOriginalExtension();
                $path = 'images';
                $file->move($path, $filename);
                $image->image = url('/') . '/images/' . $filename;
                $image->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $productImages = ProductImage::where('product_id', '=', $id)->get();
        foreach ($productImages as $productImage) {
            $path = public_path() . '/images/' . substr($productImage['image'], strrpos($productImage['image'], '/') + 1);
            if (File::exists($path)) {
                File::delete($path);
            }
        }
        DB::table('products')->where('id', '=', $id)->delete();
    }

    public function searchByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $name = $request->input('name');

        $products = Product::with('Images')
            ->where('status', '=', 'published')
            ->where('name', 'LIKE', "%{$name}%")
            ->get();

        return $products;
    }

    public function searchByProductCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $code = $request->input('code');

        $products = Product::with('Images')
            ->where('status', '=', 'published')
            ->where('code', $code)
            ->get();

        return $products;
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

        return $products;
    }
}
