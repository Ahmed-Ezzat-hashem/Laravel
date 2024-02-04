<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\socialAuthController;
use App\Http\Controllers\UsersContoller;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Public Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register-user', 'registerUser');
    Route::post('/register-pharmacy', 'registerPharmace');
    //login
    Route::post('/login-username', 'LoginByUserName');
    Route::post('/login-email', 'LoginByEmail');
    //reset password by sent otp in sms
    Route::post('password/forgot-password-sms', [AuthController::class, 'forgotPasswordSms']);
    Route::post('password/reset-sms', [AuthController::class, 'passwordResetSms']);
    //reset password by sent otp in mail
    Route::post('password/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'passwordReset']);

});

Route::get('/login-google', [socialAuthController::class, 'redirectToGoogleProvider']);
Route::get('/auth/google/callback', [socialAuthController::class, 'handleGoogleCallback']);

Route::get('/login-facebook', [socialAuthController::class, 'redirectToFacebookProvider']);
Route::get('/auth/facebook/callback', [socialAuthController::class, 'handleFacebookCallback']);


// Protected Routes
Route::middleware('auth:api')->group(function () {
    // Users
    Route::get('/user', [UsersContoller::class, 'authUser']);
    Route::middleware('checkAdmin')->controller(UsersContoller::class)->group(function () {
        Route::get('/users', 'GetUsers');
        Route::get('/user/{id}', 'getUser');
        Route::post('/user/edit/{id}', 'editUser');
        Route::post('/user/add', 'addUser');
        Route::delete('/user/{id}', 'destroy');
    });
    // Product Manger
    Route::middleware('checkProductManager')->controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/category/{id}', 'show');
        Route::post('/category/edit/{id}', 'update');
        Route::post('/category/add', 'store');
        Route::delete('/category/{id}', 'destroy');
    });

    Route::middleware('checkProductManager')->controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/product/{id}', 'show');
        Route::post('/product/edit/{id}', 'update');
        Route::post('/product/add', 'store');
        Route::delete('/product/{id}', 'destroy');

    });
    Route::middleware('checkProductManager')->controller(ProductImageController::class)->group(function () {
        Route::post('/product-img/add', 'store');
        Route::delete('/product-img/{id}', 'destroy');
    });

    // Auth
    Route::get('/logout', [AuthController::class, 'logout']);
});
