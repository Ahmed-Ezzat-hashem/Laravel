<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $userRole = Auth::user()->role;

        if ($userRole == 0) {
            $categories = Category::all();
        } else {
            $pharmacyId = Auth::user()->pharmacy_id;
            $categories = Category::where('pharmacy_id', $pharmacyId)->get();
        }

        if ($categories->count() > 0) {
            $imageBaseUrl = env('APP_URL'); // Retrieve the base URL from .env

            $categoriesArray = $categories->map(function ($category) use ($imageBaseUrl) {
                $imageUrl = $imageBaseUrl . $category->image;
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $imageUrl,
                ];
            });

            return response()->json([
                'status' => 200,
                'categories' => $categoriesArray,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No categories found.',
            ], 404);
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

        $categoryData = [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'image' => url($category->image),
        ];

        return response()->json([
            'status' => 200,
            'category' => $categoryData,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and size as needed
        ]);

        // Check if user is authenticated
        if(auth()->check()) {
            // Get authenticated user's pharmacy_id
            $pharmacy_id = auth()->user()->pharmacy_id;
        } else {
            // Handle the case where user is not authenticated
            // You may want to return an error response or redirect the user to login
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized in cat',
            ], 401);
        }

        $category = new Category();
        $category->title = $request->title;
        $category->pharmacy_id = $pharmacy_id;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('images/category');
            $file->move($path, $filename);
            $category->image = '/images/category/' . $filename;
        }
        $category->save();

        $category->image = url('/images/category/' . $filename);
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
            $category->image = '/images/category/' . $filename;
        }

        $category->save();
        //for the pharmacy to check the full path
        $category->image = url('/images/category/' . $filename);

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
