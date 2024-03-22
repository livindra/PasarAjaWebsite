<?php

namespace App\Http\Controllers\Mobile\Product;

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

    public function generateTableProd($idShop)
    {
        return 'sp_' . $idShop . '_prod';
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
        $tableProd = $this->generateTableProd($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // get history transaction
        $dtls = DB::table(DB::raw("$tableDtl as dtl"))
            ->join(DB::raw("$tableTrx as trx"), 'trx.id_trx', 'dtl.id_trx')
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'dtl.id_product')
            ->join('0users as us', 'us.id_user', 'trx.id_user')
            ->select(['dtl.*', 'trx.taken_date', 'trx.status', 'trx.created_at', 'prod.product_name', 'us.full_name', 'us.email'])
            ->where('dtl.id_product', $idProd)
            ->orderByDesc('dtl.id_trx')
            ->get();


        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $dtls], 200);
    }
}
