<?php

namespace App\Http\Controllers\Mobile\Page;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Product\ProductPromoController;
use Illuminate\Http\Request;

class PromoMerchantController extends Controller
{
    public function activePromo(Request $request, ProductPromoController $promoController)
    {
        $request->input('id_shop');

        $request->merge(['type' => 'active']);
        $activeData = $promoController->getPromos($request)->getData();

        if ($activeData->status === 'success') {
            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $activeData->data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => $activeData->message], 400);
        }
    }
}
