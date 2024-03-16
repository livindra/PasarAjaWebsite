<?php

use App\Http\Controllers\Messenger\MailController;
use App\Http\Controllers\Mobile\MobileAuthController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\ShopModel\ShopControllers;
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

Route::group(['prefix'=>'/m'], function(){
    Route::group(['prefix'=>'/test'], function(){
        Route::get('/first', [MobileAuthController::class, 'first']);
    });

    // user route
    Route::group(['prefix'=>'auth'], function(){
        Route::get('/cekemail', [MobileAuthController::class, 'isExistEmail']);
        Route::get('/cekphone', [MobileAuthController::class, 'isExistPhone']);
        Route::get('/islogin', [MobileAuthController::class, 'isOnLogin']);
        Route::post('/signup', [MobileAuthController::class, 'register']);
        Route::group(['prefix'=>'signin'], function(){
            Route::post('/email', [MobileAuthController::class, 'signinEmail']);
            Route::post('/phone', [MobileAuthController::class, 'signinPhone']);
            Route::post('/google', [MobileAuthController::class, 'signinGoogle']);
        });
        Route::group(['prefix'=>'update'], function(){
            Route::post('/pw', [MobileAuthController::class, 'changePassword']);
            Route::post('/pin', [MobileAuthController::class, 'changePin']);
        });
        Route::delete('/logout', [MobileAuthController::class, 'logout']);
    });

    // messenger
    Route::group(['prefix'=>'messenger'], function(){
        Route::get('/test', [MailController::class, 'sendEmail']);
        Route::post('/otp', [MailController::class, 'sendOtpCode']);
        Route::post('/otphone', [MailController::class, 'sendOtpCodeByPhone']);
    });

    Route::group(['prefix'=>'shop'], function(){
        Route::post('/create', [ShopController::class, 'store']);
        Route::post('/read', [ShopController::class, 'showUserData']);
        Route::post('/update', [ShopController::class, 'update']);
        Route::post('/delete', [ShopController::class, 'destroy']);
    });


});
