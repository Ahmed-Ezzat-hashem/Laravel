<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function show(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        return response()->json([
            'id' => $category->id,
            'title' => $category->title,
            'image' => $category->image,
            // Add other attributes you want to display
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and size as needed
        ]);

        $category = new Category();
        $category->title = $request->title;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('images/category');
            $file->move($path, $filename);
            $category->image = url('/images/category/' . $filename);
        }

        $category->save();
        return $category;
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
        return $category;
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
