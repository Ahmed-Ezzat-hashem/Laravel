<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\socialAuthController;
use App\Http\Controllers\UsersContoller;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
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


Route::group([],function () {
    // Public Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register-user', 'registerUser');
        Route::post('/register-verify', 'otpVerfication');
        Route::post('/resend-otp','resendOtp');
        Route::post('/resend-otp-pw','resendOtpPW');

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
        Route::post('password/otp', 'passwordResetOtp');
        Route::post('password/reset', 'passwordReset');

    });
    Route::controller(socialAuthController::class)->group(function () {

        Route::get('/login-google', 'redirectToGoogleProvider');
        Route::get('/auth/google/callback', 'handleGoogleCallback');

        Route::get('/login-facebook', 'redirectToFacebookProvider');
        Route::get('/auth/facebook/callback', 'handleFacebookCallback');
    });

});
// Route::controller(ProductController::class)->group(function () {
//     Route::get('/products', 'index');
//     Route::get('/category/{categoryId}', 'bycat');
//     Route::get('/products-Pharmacy', 'ProductPharmacy');
//     Route::get('/product/{id}', 'show');
//     Route::get('/search-by-name','searchByName');
//     Route::get('/search-by-code', 'searchByCode');
//     Route::get('/search-by-color-and-shape','searchByColorAndShape');
// });

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
    //Category Manger
    Route::middleware('checkAdmin')->controller(CategoryController::class)->group(function () {
        Route::post('/category/edit/{id}', 'update');
        Route::post('/category/add', 'store');
        Route::delete('/category/{id}', 'destroy');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        // categories of pharmacy
        Route::get('/categories-Pharmacy-user', 'CategoryPharmacyUser');
        // details of category
        Route::get('/category/{id}', 'show');
    });

//-----------------------------------------------------------------------------------------------

    Route::middleware('checkAdmin')->controller(ProductController::class)->group(function () {
        Route::post('/product/edit/{id}', 'update');
        Route::post('/product/add', 'store');
        Route::delete('/product/{id}', 'destroy');
    });

    Route::controller(ProductController::class)->group(function () {
        //all product
        Route::get('/products', 'index');
        //product of category
        Route::get('/products-category/{categoryId}', 'bycat');
        //products of pharmacy for admin get his products
        Route::get('/products-Pharmacy/{id}', 'ProductPharmacy');
        //all products of pharmacy for user get product of specific pharmacy
        Route::get('/products-Pharmacy-user/{id}', 'ProductPharmacyUser');
        Route::get('/product/{id}', 'show');
        Route::get('/search-by-name','searchByName');
        Route::get('/search-by-code', 'searchByCode');
        Route::get('/search-by-color-and-shape','searchByColorAndShape');
    });

    Route::controller(FavoriteProductController::class)->group(function () {
        Route::get('/favorites', 'index');
        Route::post('/favorite', 'store');
        Route::delete('/favorite/{id}', 'destroy');
        });


//-----------------------------------------------------------------------------------------------

    // Cart
    Route::controller(CartController::class)->group(function () {
        Route::get('/cart' , 'index');
        Route::post('/cart', 'store');
        Route::post('/cart/{id}', 'update');
        Route::delete('/cart/{id}', 'destroy');
        Route::post('/checkout', 'checkout');
    });

//-----------------------------------------------------------------------------------------------

    // Order
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders','index');
        Route::post('/order/{id}','updateOrderStatus');
        Route::delete('/order/{id}','destroy');

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
        Route::get('/profile', 'show');
        Route::post('/edit-profile', 'update');
        Route::post('/edit-profile-pic', 'profilePic');
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
