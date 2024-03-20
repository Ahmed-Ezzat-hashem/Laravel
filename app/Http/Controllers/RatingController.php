<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $rating = Rating::create($request->all());

        return response()->json([
            'status'=> 200,
            'message' => 'Rating created successfully',
            'rating' => $rating,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $rating = Rating::findOrFail($id);
        $rating->update($request->all());

        return response()->json([
            'status'=> 200,
            'message' => 'Rating updated successfully',
            'rating' => $rating,
        ], 200);
    }

    public function destroy(string $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->delete();

        return response()->json([
            'status'=> 200,
            'message' => 'Rating deleted successfully',
        ], 204);
    }
}
