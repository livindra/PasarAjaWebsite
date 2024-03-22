<?php

namespace App\Http\Controllers\Mobile\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductComplainController extends Controller
{
    public function generateTableName($idShop)
    {
        return 'sp_' . $idShop . '_comp';
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

    public function getComplains(Request $request){
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        $complains = DB::table($tableName)
        ->select()
        ->where('id_product', $idProd)
        ->orderByDesc('id_complain')
        ->get();

        foreach ($complains as $comp) {
            // mendapatkan data nama dan email
            $userData = User::select(['full_name', 'email'])
                ->where('id_user', $comp->id_user)
                ->limit(1)->first();

            // menyimpan data nama dan email
            $comp->full_name = $userData->full_name;
            $comp->email = $userData->email;
        }

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $complains], 200);
    }
}
