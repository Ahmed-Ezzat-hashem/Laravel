<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Password;

class socialAuthController extends Controller
{
    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function redirectToFacebookProvider()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }


    public function handleGoogleCallback(Response $request)
    {
        $google_user = Socialite::driver('google')->stateless()->user();
        // Check if the user already exists in the database
        $existingUser = User::where('email', $google_user->email)->first();
        if ($existingUser) {
            // If the user already exists, generate an access token for the user
            $token = $existingUser->createToken('Token Name')->accessToken;
            return response()->json([
                'user' => $existingUser,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } else {
            $user = User::where('google_id', $google_user->id)->first();
            if (!$user) {
                $user = User::Create([
                    'google_id' => $google_user->id,
                    'name' => $google_user->name,
                    'email' => $google_user->email,
                    'google_token' => $google_user->token,
                ]);
                // Generate an access token for the new user
                $token = $user->createToken('access_token')->accessToken;
                return response()->json([
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
            } else {
                $token = $user->createToken('access_token')->accessToken;
                return response()->json([
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
            }
        }
    }

    public function handleFacebookCallback(Response $request)
    {
        $facebook_user = Socialite::driver('facebook')->stateless()->user();
        // Check if the user already exists in the database
        $existingUser = User::where('email', $facebook_user->email)->first();
        if ($existingUser) {
            // If the user already exists, generate an access token for the user
            $token = $existingUser->createToken('Token Name')->accessToken;
            return response()->json([
                'user' => $existingUser,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } else {
            $user = User::where('facebook_id', $facebook_user->id)->first();
            if (!$user) {
                $user = User::Create([
                    'facebook_id' => $facebook_user->id,
                    'name' => $facebook_user->name,
                    'email' => $facebook_user->email,
                    'facebook_token' => $facebook_user->token,
                ]);
                // Generate an access token for the new user
                $token = $user->createToken('access_token')->accessToken;
                return response()->json([
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
            } else {
                $token = $user->createToken('access_token')->accessToken;
                return response()->json([
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
            }
        }
    }
}
