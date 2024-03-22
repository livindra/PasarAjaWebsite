<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductComplainController extends Controller
{
    public function generateTableComp($idShop)
    {
        return 'sp_' . $idShop . '_comp';
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

    public function getComplains(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table product and complain
        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // get data complain
        $complains = DB::table(DB::raw("$tableComp as comp"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'comp.id_product')
            ->select('comp.*', 'prod.product_name')
            ->where('comp.id_product', $idProd)
            ->orderByDesc('comp.id_complain')
            ->get();

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $complains], 200);
    }

    public function getAllComplains(Request $request)
    {
        $idShop = $request->input('id_shop');

        // generate table product and complain
        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);

        // get complain data
        $complains = DB::table(DB::raw("$tableComp AS comp"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'comp.id_product')
            ->join('0users as us', 'us.id_user', 'comp.id_user')
            ->select('comp.*', 'prod.product_name', 'us.full_name', 'us.email')
            ->get();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $complains]);
    }
}
