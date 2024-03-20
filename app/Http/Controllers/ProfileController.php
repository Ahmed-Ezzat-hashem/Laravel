<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified profile.
     */
    public function show($id)
    {
        // Retrieve the profile based on the provided user ID
        $profile = Profile::where('user_id', $id)->first();

        if ($profile) {
            return response()->json([
                'status' => 200,
                'profile' => $profile,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Profile not found for the provided user ID.',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'family_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Find the profile by user ID
        $profile = Profile::where('user_id', $id)->first();

        if ($profile) {
            // Update profile data
            $profile->update($request->all());

            // Handle profile picture upload
            if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/profile_pictures');
                $file->move($path, $filename);
                $profile->profile_picture = url('/images/profile_pictures/' . $filename);
                $profile->save();
            }

            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Profile not found for the provided user ID.',
            ], 404);
        }
    }
}
