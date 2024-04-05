<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\socialAuthController;
use App\Http\Controllers\UsersContoller;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderContoller;
use App\Http\Controllers\OrderProductController;

use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FavoriteProductController;

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
    Route::post('/register-verify', 'otpVerfication');
    Route::post('/resend-otp','resendOtp');

    Route::post('/register-pharmacy', 'registerPharmacy');
    //login
    Route::post('/login-username', 'LoginByUserName');
    Route::post('/login-email', 'LoginByEmail');
    Route::post('/login-phone', 'LoginByPhone');
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



// Protected Routes
Route::middleware(['checkToken'])->group(function () {
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
    //Category Manger
    Route::middleware('checkAdmin')->controller(CategoryController::class)->group(function () {
        Route::post('/category/edit/{id}', 'update');
        Route::post('/category/add', 'store');
        Route::delete('/category/{id}', 'destroy');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/category/{id}', 'show');
    });

//-----------------------------------------------------------------------------------------------

    Route::middleware('checkAdmin')->controller(ProductController::class)->group(function () {
        Route::post('/product/edit/{id}', 'update');
        Route::post('/product/add', 'store');
        Route::delete('/product/{id}', 'destroy');
    });

    Route::middleware('checkToken')->controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/category/{categoryId}', 'bycat');
        Route::get('/products-Pharmacy', 'ProductPharmacy');
        Route::get('/product/{id}', 'show');
        Route::get('/search-by-name','searchByName');
        Route::get('/search-by-code', 'searchByCode');
        Route::get('/search-by-color-and-shape','searchByColorAndShape');
    });

    Route::middleware('checkUser')->controller(FavoriteProductController::class)->group(function () {
        Route::get('/favorite-products', 'index');
        Route::post('/favorite-products', 'store');
        Route::delete('/favorite-products/{id}', 'destroy');
        });


//-----------------------------------------------------------------------------------------------

    // Cart
    Route::middleware('checkUser')->controller(CartController::class)->group(function () {
        Route::get('/cart' , 'index');
        Route::post('/cart', 'store');
        Route::put('/cart/{id}', 'update');
        Route::delete('/cart/{id}', 'destroy');
        Route::post('/checkout', 'checkout');
    });

//-----------------------------------------------------------------------------------------------

    // Order
    Route::controller(OrderContoller::class)->group(function () {
        Route::get('/orders','index');
        Route::put('/orders/{id}','updateOrderStatus');
        Route::delete('/orders/{id}','destroy');

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
        Route::get('profile/{id}', 'show');
        Route::post('profile/{id}', 'update');
        Route::post('profile/{id}/profile-pic', 'profilePic');
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
