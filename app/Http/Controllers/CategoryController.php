<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;


class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        if($category->count()>0){
        return response()->json([
            'status'=> 200,
            'category'=> $category,
    ],200);
        }else{
        return response()->json([
            'status'=> 404,
            'message'=> 'no categories found',
            ],404);
        }
    }

    public function show(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'category' => $category,
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and size as needed
        ]);

        $category = new Category();
        $category->title = $request->title;
        $category->pharmacy_id = auth()->user()->pharmacy_id;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('images/category');
            $file->move($path, $filename);
            $category->image = url('/images/category/' . $filename);
        }

        $category->save();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and size as needed
        ]);

        $category = Category::findOrFail($id);
        $category->title = $request->title;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $oldImagePath = public_path('images/category/' . basename($category->image));
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('images/category');
            $file->move($path, $filename);
            $category->image = url('/images/category/' . $filename);
        }

        $category->save();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $imagePath = public_path('images/category/  ' . basename($category->image));

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
