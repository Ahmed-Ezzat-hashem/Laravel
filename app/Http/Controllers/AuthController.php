<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\Password;
//use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Notifications\ResetPasswordVerificationNotification;
use App\Notifications\ResetPasswordVerificationNotificationSMS;
use Otp;
class AuthController extends Controller
{
    // Register Method

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'full_name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
    ]);
        $user = User::create([
            'name' => $request->name,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('token')->accessToken;
        $refreshToken = $user->createToken('authTokenRefresh')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $token,

        ], 200);
    }

    public function registerPharmacy(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
            'company_name' =>'required',
            'company_phone' =>'required',
            'delivary_area' =>'required',
            'company_working_hours' =>'required',
            'company_manager_name' =>'required',
            'company_manager_phone' =>'required',
            'commercial_register' =>'required',
            'tax_card' =>'required',
            'company_license' =>'required',
        ]);
        $user = User::create([
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
            'commercial_register' =>$request->commercial_register,
            'tax_card' =>$request->tax_card,
            'company_license' =>$request->company_license,
        ]);
        $token = $user->createToken('token')->accessToken;
        $refreshToken = $user->createToken('authTokenRefresh')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $token,

        ], 200);
    }

    public function LoginByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:6'
        ]);
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Login Method
    public function LoginByUserName(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|min:6'
        ]);
        $credentials = request(['name', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Logout Method
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users']);
        $input = $request->only('email');
        $user = User::where('email',$input)->first();
        $user->notify(new ResetPasswordVerificationNotification());
        $success['success'] = true;
        return response()->json($success,200);

    }

    public function forgotPasswordSms(Request $request)
    {
        $request->validate(['phone' => 'required|exists:users']);
        $input = $request->only('phone');
        $user = User::where('phone',$input)->first();
        $user->notify(new ResetPasswordVerificationNotificationSMS('sms'));
        $success['success'] = true;
        return response()->json($success,200);

    }

    private $otp;
    public function __construct(){
        $this->otp = new Otp;
    }
    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6|confirmed',
            'otp' => 'required|max:6',
        ]);

        $otp2 = $this->otp->validate($request->email, $request->otp);
            if(! $otp2->status){
                return response()->json(['error'=>$otp2], 401);
            }
            $user = User::where('email',$request->email)->first();
            $user->update(['password'=> Hash::make($request->password)]);
            $user->tokens()->delete();
            $success['success'] = true;
            return response()->json($success,200);
        }

        public function passwordResetSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users',
            'password' => 'required|min:6|confirmed',
            'otp' => 'required|max:6',
        ]);

        $otp2 = $this->otp->validate($request->phone, $request->otp);
            if(! $otp2->status){
                return response()->json(['error'=>$otp2], 401);
            }
            $user = User::where('phone',$request->email)->first();
            $user->update(['password'=> Hash::make($request->password)]);
            $user->tokens()->delete();
            $success['success'] = true;
            return response()->json($success,200);
        }
}
