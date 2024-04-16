<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Traits\ExceptionHandlingTrait;

class PharmacyController extends Controller
{
    public function index()
    {
        try {
            $pharmacies = Pharmacy::with('ratings')->get();

            // Calculate average rating for each pharmacy
            $pharmaciesWithAverageRating = $pharmacies->map(function ($pharmacy) {
                $ratings = $pharmacy->ratings->pluck('rating');

                // Calculate average rating or set it to 0 if no ratings exist
                $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

                // Check if the image field is empty, if so, set it to null
                $image = $pharmacy->image ? url($pharmacy->image) : null;

                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'address' => $pharmacy->address,
                    'image' => $image, // Use the $image variable here
                    'average_rating' => $averageRating,
                ];
            });

            return response()->json([
                'status' => 200,
                'pharmacies' => $pharmaciesWithAverageRating,
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

    public function show($id)
{
    try {
        // Fetch categories for the pharmacy
        $categories = Category::where('pharmacy_id', $id)->get();

        // Fetch pharmacy details
        $pharmacy = Pharmacy::with('ratings')->find($id);

        if (!$pharmacy) {
            // Pharmacy not found
            return response()->json(['error' => 'Pharmacy not found'], 404);
        }

        // Calculate average rating for the pharmacy
        $ratings = $pharmacy->ratings->pluck('rating');
        $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

        // Construct the response array for pharmacy details
        $image = $pharmacy->image ? url($pharmacy->image) : null;
        $pharmacyData = [
            'id' => $pharmacy->id,
            'name' => $pharmacy->name,
            'address' => $pharmacy->address,
            'image' => $image,
            'average_rating' => $averageRating,
        ];

        // Map categories to construct response with image URLs
        $categoriesArray = $categories->map(function ($category) {
            // Construct image URL using the category's image field
            $imageUrl = env('APP_URL') . $category->image;
            return [
                'id' => $category->id,
                'title' => $category->title,
                'image' => $imageUrl,
            ];
        });

        // Response with pharmacy details and categories
        return response()->json([
            'status' => 200,
            'pharmacy' => $pharmacyData,
            'categories' => $categoriesArray,
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $exception) {
        return $this->handleException($exception);
    } catch (\Throwable $th) {
        return $this->handleException($th);
    }
}



    public function update(Request $request)
    {
        try{
            $pharmacy_id = Auth::user()->pharmacy_id;
            $pharmacy = Pharmacy::find($pharmacy_id);

            if (!$pharmacy) {
                return response()->json(['message' => 'Pharmacy not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|regex:/^\+201\d{9}$/',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $pharmacy->update($validatedData);

            // Handle pharmacy picture upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old pharmacy picture if exists
                if ($pharmacy->image) {
                    $oldImagePath = public_path('images/pharmacy_pictures/') . basename($pharmacy->image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                // Store new pharmacy picture
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/pharmacy_pictures');
                $file->move($path, $filename);
                $pharmacy->image = '/images/pharmacy_pictures/' . $filename;
                $pharmacy->save();
                $pharmacy->image = url('/images/pharmacy_pictures/' . $filename);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Pharmacy updated successfully',
                'pharmacy' => $pharmacy,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return $this->handleException($exception);
        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }



    public function destroy($id)
    {
        try{

            $pharmacy = Pharmacy::find($id);

            if (!$pharmacy) {
                return response()->json(['message' => 'Pharmacy not found'], 404);
            }

            // Delete the pharmacy's image if exists
            if ($pharmacy->image) {
                $oldImagePath = public_path('images/pharmacy_pictures/') . basename($pharmacy->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $pharmacy->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Pharmacy deleted successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return $this->handleException($exception);
        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    public function getPharmacies(Request $request)
    {
        try {
            $pharmacies = Pharmacy::where('type','pharmacy')->with('ratings')->get();

            // Calculate average rating for each pharmacy
            $pharmaciesWithAverageRating = $pharmacies->map(function ($pharmacy) {
                $ratings = $pharmacy->ratings->pluck('rating');

                // Calculate average rating or set it to 0 if no ratings exist
                $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

                // Check if the image field is empty, if so, set it to null
                $image = $pharmacy->image ? url($pharmacy->image) : null;

                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'address' => $pharmacy->address,
                    'image' => $image, // Use the $image variable here
                    'average_rating' => $averageRating,
                ];
            });

            return response()->json([
                'status' => 200,
                'pharmacies' => $pharmaciesWithAverageRating,
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

    public function getHospitals(Request $request)
    {
        try {
            $pharmacies = Pharmacy::where('type','hospital')->with('ratings')->get();

            // Calculate average rating for each pharmacy
            $pharmaciesWithAverageRating = $pharmacies->map(function ($pharmacy) {
                $ratings = $pharmacy->ratings->pluck('rating');

                // Calculate average rating or set it to 0 if no ratings exist
                $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

                // Check if the image field is empty, if so, set it to null
                $image = $pharmacy->image ? url($pharmacy->image) : null;

                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'address' => $pharmacy->address,
                    'image' => $image, // Use the $image variable here
                    'average_rating' => $averageRating,
                ];
            });

            return response()->json([
                'status' => 200,
                'pharmacies' => $pharmaciesWithAverageRating,
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

    public function getbytype(Request $request ,$id)
    {
        try {
            $typeExists = Pharmacy::where('type', $id)->exists();

            if (!$typeExists) {
                // If the type does not exist, return an error response
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid type. No matching pharmacy found.',
                ], 400);
            }

            $pharmacies = Pharmacy::where('type',$id)->with('ratings')->get();

            // Calculate average rating for each pharmacy
            $pharmaciesWithAverageRating = $pharmacies->map(function ($pharmacy) {
                $ratings = $pharmacy->ratings->pluck('rating');

                // Calculate average rating or set it to 0 if no ratings exist
                $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

                // Check if the image field is empty, if so, set it to null
                $image = $pharmacy->image ? url($pharmacy->image) : null;

                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'address' => $pharmacy->address,
                    'image' => $image, // Use the $image variable here
                    'average_rating' => $averageRating,
                ];
            });

            return response()->json([
                'status' => 200,
                'pharmacies' => $pharmaciesWithAverageRating,
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
}
