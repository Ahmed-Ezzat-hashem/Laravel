<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index()
    {
        $pharmacies = Pharmacy::with('ratings')->get();

        // Calculate average rating for each pharmacy
        $pharmaciesWithAverageRating = $pharmacies->map(function ($pharmacy) {
            $ratings = $pharmacy->ratings->pluck('rating');

            // Calculate average rating or set it to 0 if no ratings exist
            $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

            return [
                'id' => $pharmacy->id,
                'name' => $pharmacy->name,
                'address' => $pharmacy->address,
                'image' => url($pharmacy->image),
                'average_rating' => $averageRating,
            ];
        });

        return response()->json([
            'status' => 200,
            'pharmacies' => $pharmaciesWithAverageRating,
        ], 200);
    }

    public function show($id)
    {
        $pharmacy = Pharmacy::with('ratings')->find($id);

        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacy not found'], 404);
        }

        // Calculate average rating for the pharmacy
        $ratings = $pharmacy->ratings->pluck('rating');
        $averageRating = $ratings->isEmpty() ? 0 : $ratings->avg();

        // Construct the response array
        $response = [
            'id' => $pharmacy->id,
            'name' => $pharmacy->name,
            'address' => $pharmacy->address,
            'image' => url($pharmacy->image),
            'average_rating' => $averageRating,
        ];

        return response()->json([
            'status' => 200,
            'pharmacy' => $response,
        ], 200);
    }



    public function update(Request $request, $id)
    {
        $pharmacy = Pharmacy::find($id);

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
    }



    public function destroy($id)
    {
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
    }
}
