<?php

namespace App\Http\Controllers\Mobile\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\FirePHPHandler;

class ProductHistoryController extends Controller
{

    public function generateTableTrx($idShop)
    {
        return 'sp_' . $idShop . '_trx';
    }

    public function generateTableDtl($idShop)
    {
        return 'sp_' . $idShop . '_trx_dtl';
    }

    public function isExistShop($idShop)
    {
        $isExist = Shops::where('id_shop', '=', $idShop)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'Toko terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'Toko tidak terdaftar'];
        }
    }

    public function historyProduct(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table
        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        $dtls = DB::table($tableDtl)
        ->select()
        ->where('id_product', $idProd)
        ->orderByDesc('id_trx')
        ->get();

        foreach ($dtls as $dtl) {
            $trx = DB::table($tableTrx)
                ->select()
                ->where('id_trx', $dtl->id_trx)
                ->limit(1)->first();
        
            $userData = User::select(['full_name', 'email'])
                ->where('id_user', $trx->id_user)
                ->limit(1)->first();
        
            // Menambahkan properti 'full_name' ke objek $dtl
            $dtl->status = $trx->status;
            $dtl->taken_date = $trx->taken_date;
            $dtl->rejected_message = $trx->rejected_message;
            $dtl->full_name = $userData->full_name;
            $dtl->email = $userData->email;
        }

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $dtls], 200);
    }
}
