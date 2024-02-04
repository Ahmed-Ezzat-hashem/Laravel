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
    public function GetUsers()
    {
        return User::all();
    }
    // Get Auth User
    public function authUser()
    {
        return Auth::user();
    }

    // Get Specific User
    public function getUser($id)
    {
        return User::findOrFail($id);
    }

    // Add User

    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'company_name' =>'required',
            'company_phone' =>'required',
            'delivary_area' =>'required',
            'company_working_hours' =>'required',
            'company_manager_name' =>'required',
            'company_manager_phone' =>'required',

            'role' => 'required'
        ]);
        $user =  DB::table('users')->insert([
            'full_name' =>$request->full_name,
            'name' =>$request->name,
            'email' =>$request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'company_name' =>$request->company_name,
            'company_phone' =>$request->company_phone,
            'delivary_area' =>$request->delivary_area,
            'company_working_hours' =>$request->company_working_hours,
            'company_manager_name' =>$request->company_manager_name,
            'company_manager_phone' =>$request->company_manager_phone,
            'role' => $request->role,
        ]);
        return response()->json([
            'user' => $user,
        ], 200);
    }

    // Edit User
    public function editUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'sometimes|min:6', // Optional password change
            'company_name' => 'required',
            'company_phone' => 'required',
            'delivary_area' => 'required',
            'company_working_hours' => 'required',
            'company_manager_name' => 'required',
            'company_manager_phone' => 'required',
            'role' => 'required'
        ]);

        $userData = [
            'full_name' => $request->full_name,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'company_phone' => $request->company_phone,
            'delivary_area' => $request->delivary_area,
            'company_working_hours' => $request->company_working_hours,
            'company_manager_name' => $request->company_manager_name,
            'company_manager_phone' => $request->company_manager_phone,
            'role' => $request->role,
        ];

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user = User::findOrFail($id);
        $user->update($userData);

        return response()->json([
            'user' => $user,
        ], 200);
    }


    // Delete User
    public function destroy($id)
    {
        return  User::findOrFail($id)->delete();
    }
}
