<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try{

            $userRole = Auth::user()->role;

            if ($userRole == 0) {
                $categories = Category::all();
            } else {
                $pharmacyId = Auth::user()->pharmacy_id;
                $categories = Category::where('pharmacy_id', $pharmacyId)->get();
            }

            if ($categories->count() > 0) {
                $categoriesArray = $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                        'image' => url($category->image),
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'categories' => $categoriesArray,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No categories found.',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function CategoryPharmacyUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pharmacy_id' => 'required|exists:pharmacies,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation error'], 400);
            }

            // Get the pharmacyId of the user
            $pharmacyId = $request->pharmacy_id;

            // Retrieve categories with the same pharmacy_id
            $categories = category::where('pharmacy_id', $pharmacyId)->get();

            if ($categories->count() > 0) {
                $categoriesArray = $categories->map(function ($category) {
                    // Construct image URL using the category's image field
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                        'image' => url($category->image),
                    ];
                });

                return response()->json([
                    'status' => 200,
                    'categories' => $categoriesArray,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'No categories found for the pharmacy.',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try{

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 404,
                    'error' => 'Category not found',
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
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'image' => 'nullable', // Adjust file types and size as needed
            ]);

            $pharmacy_id = auth()->user()->pharmacy_id;
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

            $category->image = url($category->image);
            return response()->json([
                'status' => 200,
                'category' => $category,
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $user = Auth::user();

            $request->validate([
                'title' => 'required',
                'image' => 'nullable',
            ]);

            $category = Category::findOrFail($id);
            if($category->pharmacy_id != $user->pharmacy_id) {
                return response()->json(['status'=> 400,'error' => 'not your pharmacy product'],400);
            }
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
            $category->image = url($category->image);

            return response()->json([
                'status' => 200,
                'category' => $category,
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'error' => 'Internal server error',
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function destroy($id)
    {
        try{

            $category = Category::findOrFail($id);
            $imagePath = public_path('images/category/' . basename($category->image));

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            $category->delete();
            return response()->json(['message' => 'Category deleted successfully']);
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
                // 'message' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }
}
