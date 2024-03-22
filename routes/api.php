<?php

use App\Http\Controllers\Messenger\MailController;
use App\Http\Controllers\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Mobile\Auth\VerifyController;
use App\Http\Controllers\Mobile\Merchant\ProductController;
use App\Http\Controllers\Mobile\Merchant\ProductComplainController;
use App\Http\Controllers\Mobile\Merchant\ProductHistoryController;
use App\Http\Controllers\Mobile\Merchant\ProductReviewController;
use App\Http\Controllers\Website\ShopController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => '/m'], function () {
    Route::group(['prefix' => '/test'], function () {
        Route::get('/first', [MobileAuthController::class, 'first']);
    });

    // user route
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/cekemail', [MobileAuthController::class, 'isExistEmail']);
        Route::get('/cekphone', [MobileAuthController::class, 'isExistPhone']);
        Route::get('/islogin', [MobileAuthController::class, 'isOnLogin']);
        Route::post('/signup', [MobileAuthController::class, 'register']);
        Route::group(['prefix' => 'signin'], function () {
            Route::post('/email', [MobileAuthController::class, 'signinEmail']);
            Route::post('/phone', [MobileAuthController::class, 'signinPhone']);
            Route::post('/google', [MobileAuthController::class, 'signinGoogle']);
        });
        Route::group(['prefix' => 'update'], function () {
            Route::put('/pw', [MobileAuthController::class, 'changePassword']);
            Route::put('/pin', [MobileAuthController::class, 'changePin']);
            Route::put('/devicetoken', [MobileAuthController::class, 'updateDeviceToken']);
        });
        Route::delete('/logout', [MobileAuthController::class, 'logout']);
        Route::delete('/delete', [MobileAuthController::class, 'deleteAccount']);
    });

    // verify
    Route::group(['prefix' => 'verify'], function () {
        Route::post('/otp', [VerifyController::class, 'verify']);
        Route::post('/otpbyphone', [VerifyController::class, 'verifyByPhone']);
    });

    // messenger
    Route::group(['prefix' => 'messenger'], function () {
        Route::get('/test', [MailController::class, 'sendEmail']);
    });

    // product
    Route::group(['prefix' => 'prod'], function () {
        Route::post('/create', [ProductController::class, 'createProduct']);
        Route::get('/details', [ProductController::class, 'detailProduct']);
        Route::group(['prefix' => 'update'], function () {
            Route::post('/data', [ProductController::class, 'updateProduct']);
            Route::put('/stok', [ProductController::class, 'setStock']);
            Route::put('/visibility', [ProductController::class, 'setVisibility']);
            Route::put('/recommended', [ProductController::class, 'setRecommended']);
        });

        // review
        Route::group(['prefix' => '/rvw'], function () {
            Route::get('/', [ProductReviewController::class, 'getReviews']);
            Route::post('/add', [ProductReviewController::class, 'addReview']);
        });

        // complain
        Route::group(['prefix' => '/comp'], function () {
            Route::get('/', [ProductComplainController::class, 'getComplains']);
        });

        // history
        Route::group(['prefix' => '/hist'], function () {
            Route::get('/', [ProductHistoryController::class, 'historyProduct']);
        });
    });
});

Route::group(['prefix' => 'shop'], function () {
    Route::post('/create', [ShopController::class, 'createShop']);
    Route::put('/update', [ShopController::class, 'updateShop']);
    Route::put('/operational', [ShopController::class, 'updateOperational']);
    Route::delete('/delete', [ShopController::class, 'deleteShop']);
});
