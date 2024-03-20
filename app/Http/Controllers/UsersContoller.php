<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersContoller extends Controller
{
    public function getUsers(Request $request)
    {
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
                'message' => 'No users found for this pharmacy.'
            ], 404);
        }

        return response()->json([
            'message' => 'Users retrieved successfully.',
            'users' => $users
        ], 200);
    }

    // Add User
    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:1,2'
        ]);

        $pharmacyId = auth()->user()->pharmacy_id;

        $user =  DB::table('users')->insert([
            'user_name' =>$request->user_name,
            'email' =>$request->email,
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
    }

    // Edit User
    public function editUser(Request $request, $id)
    {
        $request->validate([
            'user_name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'sometimes|min:6', // Optional password change
            'role' => 'required|in:1,2'
        ]);

        $userData = [
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user = User::findOrFail($id);
        $user->update($userData);

        return response()->json([
            'user' => $user,
            'message' => 'User and updated successfully.'
        ], 200);
    }


    // Delete User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->profile()->delete();
        $user->delete();

        return response()->json([
            'message' => 'User and associated profile deleted successfully.'
        ], 200);
    }
}
