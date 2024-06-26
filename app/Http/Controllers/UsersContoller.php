<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use App\Traits\ExceptionHandlingTrait;

class UsersContoller extends Controller
{
    public function getUsers(Request $request)
    {
        try{
            // Get the authenticated user's pharmacy_id
            $pharmacyId = Auth::user()->pharmacy_id;

            // Retrieve users with the same pharmacy_id
            $users = DB::table('users')
                ->select('id', 'user_name', 'email', 'phone', 'role')
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            // Check if any users are found
            if ($users->isEmpty()) {
                return response()->json([
                    'error' => 'No users found for this pharmacy.'
                ], 404);
            }

            return response()->json([
                'message' => 'Users retrieved successfully.',
                'users' => $users
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

    // Add User
    public function addUser(Request $request)
    {
        try{
            $request->validate([
                'user_name' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users',
                'password' => 'required|min:6',
                'role' => 'required'
            ]);

            $pharmacyId = auth()->user()->pharmacy_id;

            $user = User::create([
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'pharmacy_id' => $pharmacyId,
            ]);

            // Create Profile entry
            $profile = Profile::create([
                'user_id' => $user->id,
                'first_name' => $request->user_name,
                'phone_number'=>$user->phone,
            ]);

            return response()->json([
                'message' => 'User Added successfully.',
                'id' => $user->id,
                'user_name' => $user->user_name,
                'phone' => $user->phone,
                'email' => $user->email,
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

    // Edit User
    public function editUser(Request $request, $id)
    {
        try {
            $request->validate([
                'user_name' => 'nullable|unique:users,user_name,' . $id,
                'email' => 'nullable|email|unique:users,email,' . $id,
                'phone' => 'nullable|unique:users,phone,' . $id,
                'password' => 'sometimes|min:6', // Optional password change
                'role' => 'required'
            ]);

            $user = User::findOrFail($id);

            $userData = [];

            // Compare the new data with the existing data
            if ($request->has('user_name') && $request->user_name !== $user->user_name) {
                $userData['user_name'] = $request->user_name;
            }

            if ($request->has('email') && $request->email !== $user->email) {
                $userData['email'] = $request->email;
            }

            if ($request->has('phone') && $request->phone !== $user->phone) {
                $userData['phone'] = $request->phone;
            }

            if ($request->has('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->has('role') && $request->role !== $user->role) {
                $userData['role'] = $request->role;
            }

            if (empty($userData)) {
                return response()->json([
                    'message' => 'User data unchanged.'
                ], 200);
            }

            $user->update($userData);

            return response()->json([
                'user' => $user,
                'message' => 'User updated successfully.'
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


    // Delete User
    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);
            $user->profile()->delete();
            $user->delete();

            $pharmacyId = Auth::user()->pharmacy_id;

            // Retrieve users with the same pharmacy_id
            $users = DB::table('users')
                ->select('id', 'user_name', 'email', 'phone', 'role')
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            return response()->json([
                'message' => 'User and associated profile deleted successfully.',
                'users' => $users,
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
