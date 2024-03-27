<?php

use App\Http\Controllers\Messenger\MailController;
use App\Http\Controllers\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Mobile\Auth\VerifyController;
use App\Http\Controllers\Mobile\Category\CategoryController;
use App\Http\Controllers\Mobile\Page\ProductMerchantController;
use App\Http\Controllers\Mobile\Page\PromoMerchantController;
use App\Http\Controllers\Mobile\Product\ProductController;
use App\Http\Controllers\Mobile\Product\ProductComplainController;
use App\Http\Controllers\Mobile\Product\ProductHistoryController;
use App\Http\Controllers\Mobile\Product\ProductPromoController;
use App\Http\Controllers\Mobile\Product\ProductReviewController;
use App\Http\Controllers\Website\ShopController;
use App\Models\ProductCategories;
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
            Route::post('/pp', [MobileAuthController::class, 'updatePhotoProfile']);
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

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'allCategories']);
    });

    // product
    Route::group(['prefix' => 'prod'], function () {
        Route::get('/', [ProductController::class, 'allProducts']);
        Route::post('/create', [ProductController::class, 'createProduct']);
        Route::get('/details', [ProductController::class, 'detailProduct']);
        Route::get('/hiddens', [ProductController::class, 'hiddenProducts']);
        Route::get('/recommendeds', [ProductController::class, 'recommendedProducts']);
        Route::get('/unavls', [ProductController::class, 'unavlProducts']);
        Route::get('/bestselling', [ProductController::class, 'bestSelling']);
        Route::group(['prefix' => 'update'], function () {
            Route::post('/data', [ProductController::class, 'updateProduct']);
            Route::put('/stok', [ProductController::class, 'setStock']);
            Route::put('/visibility', [ProductController::class, 'setVisibility']);
            Route::put('/recommended', [ProductController::class, 'setRecommended']);
        });
        Route::delete('/delete', [ProductController::class, 'deleteProduct']);
        
        // review
        Route::group(['prefix' => '/rvw'], function () {
            Route::get('/', [ProductReviewController::class, 'getAllReview']);
            Route::get('/prod', [ProductReviewController::class, 'getReviews']);
            Route::post('/add', [ProductReviewController::class, 'addReview']);
            Route::get('/highest', [ProductReviewController::class, 'getHighestReview']);
        });

        // complain
        Route::group(['prefix' => '/comp'], function () {
            Route::get('/', [ProductComplainController::class, 'getAllComplains']);
            Route::get('/prod', [ProductComplainController::class, 'getComplains']);
        });

        // history
        Route::group(['prefix' => '/hist'], function () {
            Route::get('/prod', [ProductHistoryController::class, 'historyProduct']);
        });

        // promo
        Route::group(['prefix' => '/promo'], function(){
            Route::get('/', [ProductPromoController::class, 'getPromos']);
            Route::get('/ispromo', [ProductPromoController::class, 'isPromo']);
            Route::post('/create', [ProductPromoController::class, 'addPromo']);
            Route::put('/update', [ProductPromoController::class, 'updatePromo']);
            Route::delete('/delete', [ProductPromoController::class, 'deletePromo']);
        });
    });

    Route::group(['prefix' => 'page'], function () {
        // merchant
        Route::group(['prefix' => 'merchant'], function () {
            Route::group(['prefix' => 'home'], function () {
                //
            });
            Route::group(['prefix' => 'prod'], function () {
                Route::get('/', [ProductMerchantController::class, 'page']);
                Route::get('/rvw', [ProductMerchantController::class, 'reviewPage']);
                Route::get('/comp', [ProductMerchantController::class, 'complainPage']);
                Route::get('/unavl', [ProductMerchantController::class, 'unvailablePage']);
                Route::get('/recommended', [ProductMerchantController::class, 'recommendedPage']);
                Route::get('/hidden', [ProductMerchantController::class, 'hiddenPage']);
                Route::get('/highest', [ProductMerchantController::class, 'highestPage']);
                Route::get('/selling', [ProductMerchantController::class, 'bestSellingPage']);
                Route::get('/detail', [ProductMerchantController::class, 'detailProduct']);
            });
            Route::group(['prefix' => 'promo'], function () {
                Route::get('/active', [PromoMerchantController::class, 'activePromo']);
            });
            Route::group(['prefix' => 'trx'], function () {
                //
            });
        });

        // customer
        Route::group(['prefix' => 'merchant'], function () {
            //
        });
    });
});

Route::group(['prefix' => 'shop'], function () {
    Route::post('/create', [ShopController::class, 'createShop']);
    Route::put('/update', [ShopController::class, 'updateShop']);
    Route::put('/operational', [ShopController::class, 'updateOperational']);
    Route::delete('/delete', [ShopController::class, 'deleteShop']);
});
