<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductComplainController extends Controller
{

    public function generateTableTrx($idShop)
    {
        return 'sp_' . $idShop . '_trx';
    }

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
        $idProd = $request->input('id_product', 0);
        $limit = $request->input('limit', 0);

        // generate table product and complain
        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // get data complain
        $complains = DB::table(DB::raw("$tableComp as comp"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'comp.id_product')
            ->join('0users as us', 'us.id_user', 'comp.id_user')
            ->join(DB::raw("$tableTrx as trx"), 'trx.id_trx', 'comp.id_trx')
            ->select('comp.*', 'prod.product_name', 'prod.photo as product_photo', 'us.full_name', 'us.email', 'us.photo as user_photo', 'trx.updated_at as order_date')
            ->orderByDesc('comp.id_complain')
            ->when($idProd !== 0, function ($query) use ($idProd) {
                $query->where('comp.id_product', $idProd);
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->get();

        foreach ($complains as $prod) {
            $prod->product_photo = asset('prods/' . $prod->product_photo);
            $prod->user_photo = asset('users/' . $prod->user_photo);
        }

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $complains], 200);
    }

}
