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
            $averageRating = $ratings->avg();

            return [
                'id' => $pharmacy->id,
                'name' => $pharmacy->name,
                'address' => $pharmacy->address,
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

        $pharmacy->average_rating = $pharmacy->calculateRating();

        // Exclude individual ratings from the response
        unset($pharmacy->ratings);

        return response()->json($pharmacy, 200);
    }

    public function update(Request $request, string $id)
    {
        // Logic for updating a pharmacy goes here
        // For example:
        $pharmacy = Pharmacy::find($id);

        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacy not found'], 404);
        }

        // Perform validation on the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|regex:/^\+201\d{9}$/',
            ]);

        // Update the pharmacy's attributes
        $pharmacy->update($validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'Pharmacy updated successfully'
        ], 200);
    }

    public function destroy(string $id)
    {
        // Logic for deleting a pharmacy goes here
        // For example:
        $pharmacy = Pharmacy::find($id);

        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacy not found'], 404);
        }

        // Delete the pharmacy
        $pharmacy->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Pharmacy deleted successfully'
        ], 200);
    }
}
