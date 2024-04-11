<?php

namespace App\Http\Controllers;

use Laravel\Passport\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\Password;
use App\Models\Profile;
use App\Models\Pharmacy;
use Illuminate\Validation\ValidationException;

//use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Notifications\ResetPasswordVerificationNotification;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetPasswordVerificationNotificationSMS;
use Otp;
class AuthController extends Controller
{
    protected function login(Request $request): ?string
    {
        return null;
    }



    // Register Method

    public function registerUser(Request $request)
    {
        try {
            $request->validate([
                'user_name' => 'required|unique:users',
                'phone' => 'required|unique:users',
                'email' => 'required|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);

            $user = User::create([
                'user_name' => $request->user_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $profile = Profile::create([
                'user_id' => $user->id,
                'first_name' => $request->user_name,
                'phone_number'=>$user->phone,
            ]);

            // Generate and send OTP for email verification
            $otp = $this->otp->generate($request->email); // Pass email as a string
            $user->notify(new EmailVerificationNotification($otp));

            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification code.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'validations error '], 402);
        }
    }

    public function registerPharmacy(Request $request)
    {
        try{
            $request->validate([
                'user_name' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users',
                'password' => 'required|min:6|confirmed',
                'company_name' =>'required',
                'company_phone' =>'required',
                'delivary_area' =>'required',
                'company_working_hours' =>'required',
                'company_manager_name' =>'required',
                'company_manager_phone' =>'required',
                'commercial_register' =>'required|file|mimes:pdf|max:10240',
                'tax_card' =>'required|file|mimes:pdf|max:10240',
                'company_license' =>'required|file|mimes:pdf|max:10240',
            ]);

            $user = User::create([
                'role'=>'1',
                'user_name' =>$request->user_name,
                'email' =>$request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'company_name' =>$request->company_name,
                'company_phone' =>$request->company_phone,
                'delivary_area' =>$request->delivary_area,
                'company_working_hours' =>$request->company_working_hours,
                'company_manager_name' =>$request->company_manager_name,
                'company_manager_phone' =>$request->company_manager_phone,
            ]);

            // Handle file uploads
            if ($request->hasFile('commercial_register') && $request->file('commercial_register')->isValid()) {
                // File validation
                $file = $request->file('commercial_register');
                if ($file->getClientOriginalExtension() !== 'pdf' || $file->getSize() > 10240000) {
                    return response()->json(['error' => 'Invalid commercial register file. Please upload a PDF file within 10MB.'], 400);
                }
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/commercial_register');
                $file->move($path, $filename);
                $user->commercial_register = 'images/commercial_register/' . $filename;
            } else {
                return response()->json(['error' => 'Commercial register file not found or invalid.'], 400);
            }

            if ($request->hasFile('tax_card') && $request->file('tax_card')->isValid()) {
                $file = $request->file('tax_card');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/tax_card');
                $file->move($path, $filename);
                $user->tax_card = '/images/tax_card/' . $filename;
            }

            if ($request->hasFile('company_license') && $request->file('company_license')->isValid()) {
                $file = $request->file('company_license');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path('images/company_license');
                $file->move($path, $filename);
                $user->company_license = '/images/company_license/' . $filename;
            }
            $user->save();

            // Create Profile entry
            $profile = Profile::create([
                'user_id' => $user->id,
                'first_name' => $request->user_name,
                'phone_number'=>$user->phone,
            ]);

            // Create pharmacy entry
            $pharmacy = Pharmacy::create([
                'user_id' => $user->id,
                'name' => $request->company_name,
                'address' => $request->delivary_area,
                'phone'=> $request->phone,
                'image'=>"",
            ]);

            //connect the owner with his pharmacy
            $user->update(['pharmacy_id' => $pharmacy->id]);

            // Generate and send OTP for email verification
            $otp = $this->otp->generate($request->email); // Pass email as a string
            $user->notify(new EmailVerificationNotification($otp));

            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification code.',
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }


        //LOGIN

    public function LoginByEmail(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required',
                'password' => 'required|min:6'
            ]);
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json(['error' => 'incorrect email or password '], 401);
            }

            $user = $request->user();
            $token = $user->createToken('token')->accessToken;

            return response()->json([
                'id' => $user->id,
                'user_name' => $user->user_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token,
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    // Login Method
    public function LoginByUserName(Request $request)
    {
        try{
            $request->validate([
                'user_name' => 'required',
                'password' => 'required|min:6'
            ]);
            $credentials = request(['user_name', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json(['error' => 'incorrect user_name or password '], 401);
            }

            $user = $request->user();
            $token = $user->createToken('token')->accessToken;

            return response()->json([
                'id' => $user->id,
                'user_name' => $user->user_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token,
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    // Login Method
    public function LoginByPhone(Request $request)
    {
        try{
            $request->validate([
                'phone' => 'required',
                'password' => 'required|min:6'
            ]);

            // Extract credentials from the request
            $credentials = $request->only('phone', 'password');

            // Attempt to authenticate the user using phone number and password
            if (!Auth::attempt($credentials)) {
                return response()->json(['error' => 'incorrect phone or password '], 401);
            }

            // If authentication succeeds, retrieve the authenticated user
            $user = $request->user();

            // Create a new access token for the user
            $token = $user->createToken('token')->accessToken;

            // Return the user details and access token in the response
            return response()->json([
                'id' => $user->id,
                'user_name' => $user->user_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token,
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    // Logout Method
    public function logout(Request $request)
    {
        try{

            $userId = Auth::Id();

            $tokens = Token::where('user_id', $userId)->get();
            // Revoke each token
            foreach ($tokens as $token) {
                $token->delete();
            }

            return response()->json([
                'message' => 'Logged out successfully.'
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try{

            $request->validate(['email' => 'required|email|exists:users']);
            $input = $request->only('email');
            $user = User::where('email',$input)->first();
            $user->notify(new ResetPasswordVerificationNotification());
            $success['success'] = true;
            return response()->json(['message' => 'we send the otp pls check yor email'],200);
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function forgotPasswordSms(Request $request)
    {
        try{

            $request->validate(['phone' => 'required|exists:users']);
            $input = $request->only('phone');
            $user = User::where('phone',$input)->first();
            $user->notify(new ResetPasswordVerificationNotificationSMS('sms'));
            $success['success'] = true;
            return response()->json($success,200);
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }

    }

    private $otp;
    public function __construct(){
        $this->otp = new Otp;
    }
    public function passwordResetOtp(Request $request)
    {
        try{

            $request->validate([
                'email' => 'required|email|exists:users',
                'otp' => 'required|max:6',
            ]);

            $otp2 = $this->otp->validate($request->email, $request->otp);
            if (!$otp2->status) {
                    return response()->json([
                        'error' => 'OTP is not valid'
                    ], 401);
                }

                return response()->json(['message' => 'success'],200);
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

        public function passwordReset(Request $request)
        {
            try{
                $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|min:6|confirmed'
            ]);

            $user = User::where('email',$request->email)->first();
            $user->update(['password'=> Hash::make($request->password)]);
            $user->tokens()->delete();
            return response()->json(['message' => 'password reseted successfully'],200);
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
                    'error' => $th->getMessage(), // Include the error message in the response
                ], 500);
            }

        }
        public function passwordResetSms(Request $request)
    {

        try{
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
        }



    public function otpVerfication(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'otp' => 'required|max:6',
            ]);

            // Validate the OTP
            $otpValidation = $this->otp->validate($request->email, $request->otp);

            if (!$otpValidation->status) {
                return response()->json(['error' => 'Invalid OTP'], 401);
            }

            // Check if the provided OTP corresponds to a different email
            // if ($otpValidation->email !== $request->email) {
            //     return response()->json(['error' => 'OTP does not match the provided email'], 401);
            // }

            // OTP is valid and belongs to the provided email
            // Update the email_verified_at column
            $user = User::where('email', $request->email)->first();
            $user->email_verified_at = now();
            $user->save();

            // Generate a new access token for the user
            $token = $user->createToken('token')->accessToken;

            // Return success response with the new access token
            return response()->json([
                'success' => true,
                'token' => $token,
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email|exists:users',
            ]);

            $user = User::where('email', $request->email)->first();

            // Generate and send OTP for email verification
            $otp = $this->otp->generate($user->email); // Assuming $this->otp is your OTP generation service
            $user->notify(new ResetPasswordVerificationNotification($otp));

            return response()->json([
                'message' => 'OTP has been resent to your email address.',
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
                'error' => $th->getMessage(), // Include the error message in the response
            ], 500);
        }
    }

}
