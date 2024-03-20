<?php

namespace App\Http\Controllers;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);


        // Create a new prescription record
        $prescription = Prescription::create([
            'user_id' => $request->user_id,
        ]);

        // Handle the prescription image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/prescription'; // Storage path relative to the public disk

            // Store the image in the storage folder
            $file->storeAs($path, $filename);

            // Update the prescription with the image path
            $prescription->image = $filename;
            $prescription->save();
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

    public function show($id)
    {
        $prescription = Prescription::findOrFail($id);

        // Check if the prescription image exists
        if (Storage::exists($prescription->image)) {
            // Get the image content and return it as a response
            $imageContent = Storage::get($prescription->image);
            return response($imageContent)->header('Content-Type', 'image/jpeg');
        }

        return response()->json(['message' => 'Prescription image not found'], 404);
    }

    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // Find the prescription by ID
        $prescription = Prescription::findOrFail($id);

        // Update prescription data
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete the existing image from storage
            if (Storage::exists($prescription->image)) {
                Storage::delete($prescription->image);
            }

            // Store the new image in the storage folder
            $imagePath = $request->file('image')->store('images/prescription');

            // Update the prescription with the new image path
            $prescription->image = url('/images/prescription/' . $imagePath);
        }

        $prescription->save();

        return response()->json([
            'message' => 'Prescription updated successfully.',
            'image_url' => $prescription->image, // Include the image URL in the response
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

