<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Traits\ExceptionHandlingTrait;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the specified profile.
     */
    public function show()
    {
        try{
            $userid = Auth::Id();
            // Retrieve the profile based on the provided user ID
            $profile = Profile::where('user_id', $userid)->first();


            if ($profile) {

                if ($profile->profile_picture) {
                    $profile->profile_picture = url($profile->profile_picture);
                }
                return response()->json([
                    'status' => 200,
                    'profile' => $profile,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'Profile not found for the provided user ID.',
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try{
            $userid = Auth::Id();
            // Validate request data
            $request->validate([
                'first_name' => 'nullable|string|max:255',
                'family_name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            // Find the profile by user ID
            $profile = Profile::where('user_id', $userid)->first();

            if ($profile) {
                // Update profile data
                $profile->update($request->all());
                return response()->json(['message' => 'Profile updated successfully.'], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'Profile not found for the provided user ID.',
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

    public function profilePic(Request $request)
    {
        try{
            $userid = Auth::user()->id;

            // Validate request data
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Find the profile by user ID
            $profile = Profile::where('user_id', $userid)->first();

            // Handle profile picture upload
            if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
                // Delete old profile picture if exists
                if ($profile->profile_picture) {
                    $oldImagePath = public_path('images/profile_pictures/') . basename($profile->profile_picture);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                // Store new profile picture
                $file = $request->file('profile_picture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/profile_pictures');
                $file->move($path, $filename);
                $profile->profile_picture = '/images/profile_pictures/' . $filename;
                $profile->save();
                $profile->profile_picture = url('/images/profile_pictures/' . $filename);

                return response()->json(['message' => 'Profile picture updated successfully.'], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid profile picture.',
                ], 400);
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
                'headers' => $request->header(),
                'params' => $request->all(),
                'name' => $request->input('name'),
                'body' => $request->getContent(),
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
