<?php

namespace App\Http\Controllers;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Query prescriptions associated with the authenticated user
        $prescriptions = Prescription::where('user_id', $userId)->get();

        // Check if prescriptions exist
        if ($prescriptions->isEmpty()) {
            return response()->json(['message' => 'No prescriptions found for the authenticated user.'], 404);
        }

        // Prepare prescriptions data with image URLs
        $prescriptionsData = $prescriptions->map(function ($prescription) {
            $imageUrl = ''; // Set default image URL
            if (!empty($prescription->image)) {
                $imageUrl = env('APP_URL') . '/storage/' . $prescription->image;
            }
            return [
                'id' => $prescription->id,
                'image' => $imageUrl,
                // Add other attributes as needed
            ];
        });

        // Return the prescriptions with image URLs
        return response()->json([
            'status' => 200,
            'prescriptions' => $prescriptionsData,
        ], 200);
    }


    public function store(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);


        // Create a new prescription record
        $prescription = Prescription::create([
            'user_id' => $userId,
        ]);

        // Handle the prescription image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/prescription';
            $file->move($path, $filename);
            $prescription->image = '/images/prescription/' . $filename;
            $prescription->save();
            $prescription->image = env('APP_URL') . '/storage' . '/images/product/' . $filename;
        } else {
            // If image upload fails, delete the prescription record
            $prescription->delete();
            return response()->json(['message' => 'Failed to upload prescription image.'], 400);
        }

        // Return the response with the created prescription
        return response()->json([
            'status' => 200,
            'prescription' => $prescription,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        // Find the prescription by ID
        $prescription = Prescription::find($id);

        // Check if prescription exists
        if (!$prescription) {
            return response()->json(['message' => 'Prescription not found.'], 404);
        }

        // Prepare prescription data with image URL
        $imageUrl = ''; // Set default image URL
        if (!empty($prescription->image)) {
            $imageUrl = env('APP_URL') . '/storage/' . $prescription->image;
        }

        $prescriptionData = [
            'id' => $prescription->id,
            'image' => $imageUrl,
            // Add other attributes as needed
        ];

        // Return the prescription with image URL
        return response()->json([
            'status' => 200,
            'prescription' => $prescriptionData,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Find the prescription by ID
        $prescription = Prescription::find($id);

        // Check if prescription exists
        if (!$prescription) {
            return response()->json(['message' => 'Prescription not found.'], 404);
        }

        // Validate the request data
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // Handle the prescription image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete the previous image if exists
            if (!empty($prescription->image)) {
                Storage::delete($prescription->image);
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/prescription';
            $file->move($path, $filename);
            $prescription->image = '/images/prescription/' . $filename;
        }

        // Save the updated prescription
        $prescription->save();

        // Prepare prescription data with image URL
        $imageUrl = ''; // Set default image URL
        if (!empty($prescription->image)) {
            $imageUrl = env('APP_URL') . '/storage/' . $prescription->image;
        }

        $prescriptionData = [
            'id' => $prescription->id,
            'image' => $imageUrl,
        ];

        // Return the response with the updated prescription
        return response()->json([
            'status' => 200,
            'prescription' => $prescriptionData,
        ], 200);
    }

    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);

        // Delete the image from storage
        if (Storage::exists($prescription->image)) {
            Storage::delete($prescription->image);
        }

        // Delete the prescription record
        $prescription->delete();

        return response()->json(['message' => 'Prescription deleted successfully.'], 204);
    }
}

