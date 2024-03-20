<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\socialAuthController;
use App\Http\Controllers\UsersContoller;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderContoller;
use App\Http\Controllers\OrderProductController;

use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;

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
    Route::post('/register-pharmacy', 'registerPharmacy');
    //login
    Route::post('/login-username', 'LoginByUserName');
    Route::post('/login-email', 'LoginByEmail');
    //reset password by sent otp in sms
    Route::post('password/forgot-password-sms', 'forgotPasswordSms');
    Route::post('password/reset-sms','passwordResetSms');
    //reset password by sent otp in mail
    Route::post('password/forgot-password', 'forgotPassword');
    Route::post('password/reset', 'passwordReset');

});
Route::controller(socialAuthController::class)->group(function () {

    Route::get('/login-google', 'redirectToGoogleProvider');
    Route::get('/auth/google/callback', 'handleGoogleCallback');

    Route::get('/login-facebook', 'redirectToFacebookProvider');
    Route::get('/auth/facebook/callback', 'handleFacebookCallback');
});

Route::get('/project-status', [AuthController::class,'checkProjectStatus']);

Route::controller(CategoryController::class)->group(function () {
    Route::post('/category/edit/{id}', 'update');
    Route::post('/category/add', 'store');
    Route::delete('/category/{id}', 'destroy');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::get('/category/{id}', 'show');
});


Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products-Pharmacy', 'ProductPharmacy');
    Route::get('/product/{id}', 'show');
    Route::post('/search-by-product-code','searchByProductCode');
    Route::post('/search-by-color-and-shape','searchByColorAndShape');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::post('/product/edit/{id}', 'update');
        Route::post('/product/add', 'store');
        Route::delete('/product/{id}', 'destroy');
    });

    Route::controller(ProductImageController::class)->group(function () {
        Route::post('/product-img/add', 'store');
        Route::delete('/product-img/{id}', 'destroy');
    });

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
//-----------------------------------------------------------------------------------------------
    // Category Manger
    // Route::middleware('checkAdmin')->controller(CategoryController::class)->group(function () {
    //     Route::post('/category/edit/{id}', 'update');
    //     Route::post('/category/add', 'store');
    //     Route::delete('/category/{id}', 'destroy');
    // });

    // Route::controller(CategoryController::class)->group(function () {
    //     Route::get('/categories', 'index');
    //     Route::get('/category/{id}', 'show');
    // });

//-----------------------------------------------------------------------------------------------

    // Route::middleware('checkAdmin')->controller(ProductController::class)->group(function () {
    //     Route::post('/product/edit/{id}', 'update');
    //     Route::post('/product/add', 'store');
    //     Route::delete('/product/{id}', 'destroy');
    // });

    // Route::middleware('CheckAdmin')->controller(ProductImageController::class)->group(function () {
    //     Route::post('/product-img/add', 'store');
    //     Route::delete('/product-img/{id}', 'destroy');
    // });

    // Route::controller(ProductController::class)->group(function () {
    // Route::get('/products', 'index');
    // Route::get('/products-Pharmacy', 'ProductPharmacy');
    // Route::get('/product/{id}', 'show');
    // Route::post('/search-by-product-code','searchByProductCode');
    // Route::post('/search-by-color-and-shape','searchByColorAndShape');
    // });

//-----------------------------------------------------------------------------------------------

    // Cart
    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart/add', 'store');
        Route::post('/cart/remove', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Order
    Route::controller(OrderContoller::class)->group(function () {
        Route::get('/orders', 'index');
        Route::get('/order/{id}', 'show');
        Route::post('/order/add', 'store');
        Route::post('/order/update/{id}', 'update');
        Route::delete('/order/{id}', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Order Product
    Route::controller(OrderProductController::class)->group(function () {
        Route::post('/order-product/add', 'store');
        Route::delete('/order-product/{id}', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Pharmacy
    Route::controller(PharmacyController::class)->group(function () {
        Route::get('/pharmacies', 'index');
        Route::get('/pharmacy/{id}', 'show');
        Route::post('/pharmacy/add', 'store');
        Route::post('/pharmacy/edit/{id}', 'update');
        Route::delete('/pharmacy/{id}', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Prescription
    Route::controller(PrescriptionController::class)->group(function () {
        Route::get('/prescriptions', 'index');
        Route::get('/prescription/{id}', 'show');
        Route::post('/prescription/add', 'store');
        Route::post('/prescription/edit/{id}', 'update');
        Route::delete('/prescription/{id}', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'show');
        Route::post('/profile/edit', 'update');
        Route::post('/profile/change-password', 'changePassword');
        Route::delete('/profile/delete', 'destroy');
    });

//-----------------------------------------------------------------------------------------------

    // Rating
    Route::controller(RatingController::class)->group(function () {
        Route::post('/rating/add', 'store');
        Route::delete('/rating/{id}', 'destroy');
    });

    // Auth
    Route::get('/logout', [AuthController::class, 'logout']);
});
