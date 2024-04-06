<?php

namespace App\Http\Controllers\Mobile\Page;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Transaction\TransactionController;
use Illuminate\Http\Request;

class OrderMerchantController extends Controller
{
    public function listOfTrx(Request $request, TransactionController $trxController)
    {
        $request->input('id_shop');
        $status = $request->input('status');

        $request->merge(['status' => $status]);
        $trxData = $trxController->listOfTrx($request)->getData();

        if ($trxData->status === 'success') {
            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $trxData->data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => $trxData->message], 400);
        }
    }
}
