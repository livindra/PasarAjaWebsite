<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Transaction\TransactionController;
use App\Models\Shops;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function isExistUser($idUser)
    {
        $isExist = DB::table('0users')->where('id_user', '=', $idUser)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'User terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'User tidak terdaftar'];
        }
    }

    public function isExistProduct($tableName, $idProd)
    {
        $isExist = DB::table($tableName)->where('id_product', '=', $idProd)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'ID produk terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'ID produk tidak terdaftar'];
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

    public function isComplain(Request $request)
    {
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        $tableName = $this->generateTableComp($idShop);

        // cek apakah user sudah mengkomplain atau tidak
        $isExist = DB::table($tableName)
            ->where('id_trx', $idTrx)
            ->where('id_product', $idProd)
            ->exists();

        // return response
        if ($isExist) {
            return response()->json(['status' => 'success', 'message' => 'User sudah mengkomplain'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'User belum mengkomplain'], 400);
        }
    }

    public function addComplain(Request $request, TransactionController $trxController)
    {
        $validator = Validator::make($request->all(), [
            'order_code' => 'required|string',
            'id_user' => 'required|integer',
            'id_shop' => 'required|integer',
            'id_product' => 'required|integer',
            'reason' => 'required|string|max:100',
        ], [
            'order_code.required' => 'Order code harus diisi.',
            'order_code.string' => 'Order code harus berupa teks.',
            'id_user' => 'ID user tidak valid.',
            'id_shop' => 'ID shop tidak valid.',
            'id_product' => 'ID product tidak valid.',
            'reason.required' => 'Alasan harus diisi',
            'reason.max' => 'Komentar maksimal 100 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $orderCode = $request->input('order_code');
        $idUser = $request->input('id_user');
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product');
        $reason = $request->input('reason');

        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // cek apakah user exist atau tidak
        $isExistUser = $this->isExistUser($idUser);
        if ($isExistUser['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistUser['message']], 404);
        }

        // cek apakah shop ada atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah produk exist atau tidak
        $isExistProd = $this->isExistProduct($tableProd, $idProduct);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistProd['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $trxController->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $trxController->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 404);
        }

        // get data
        $trxData = $trx->data;
        if ($trxData->user_data->id_user !== $idUser) {
            return response()->json(['status' => 'error', 'message' => 'ID user tidak cocok'], 400);
        }

        // jika transaksi belum selesai
        if ($trxData->status !== 'Finished') {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa review karena transaksi belum selesai'], 400);
        }


        // jika tanggal trx sudah lebih dari 7 hari maka sudah tidak bisa add rvw
        $trxDate = Carbon::parse($trxData->updated_at);
        $maxDate = Carbon::now()->addDays(7);
        if ($trxDate->greaterThan($maxDate)) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa menambahkan komplain'], 400);
        }

        // cek apakah user sudah komplain
        $request->merge(['id_trx' => $trxData->id_trx]);
        $isRvw = $this->isComplain($request)->getData();
        if ($isRvw->status === 'success') {
            return response()->json(['status' => 'error', 'message' => 'User sudah komplain'], 400);
        }

        // cek apakah id produk ada didalam list transaksi
        foreach ($trxData->details as $trx) {
            // jika ketemu
            if ($trx->id_product === $idProduct) {
                $data = [
                    'id_user' => $idUser,
                    'id_shop' => $idShop,
                    'id_trx' => $trxData->id_trx,
                    'id_product' => $idProduct,
                    'reason' => $reason,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $addData = DB::table($tableComp)->insert($data);

                // return response
                if ($addData) {
                    return response()->json(['status' => 'success', 'message' => 'Komplain berhasil ditambahkan'], 201);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Gagal membuat komplain'], 404);
                }
            }
        }
        return response()->json(['status' => 'error', 'message' => 'ID Produk tidak termasuk dalam list pembelian'], 404);
    }

    public function updateComplain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_complain' => 'required|integer',
            'id_trx' => 'required|integer',
            'id_shop' => 'required|integer',
            'id_product' => 'required|integer',
            'reason' => 'required|string|max:100',
        ], [
            'id_complain' => 'ID complain tidak valid',
            'id_trx' => 'ID Trx tidak valid.',
            'id_shop' => 'ID shop tidak valid.',
            'id_product' => 'ID product tidak valid.',
            'reason.required' => 'Alasan harus diisi',
            'reason.max' => 'Komentar maksimal 100 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idComp = $request->input('id_complain');
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product');
        $reason = $request->input('reason');

        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // cek apakah shop ada atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 404);
        }

        // mendapatkan data transaksi
        $trxData = DB::table($tableTrx)
            ->select()
            ->where('id_trx', $idTrx)
            ->first();
        if (is_null($trxData)) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mendapatkan data transaksi'], 400);
        }

        // jika data transaksi berhasil didapatkan
        // jika tanggal trx sudah lebih dari 7 hari maka sudah tidak bisa update comp
        $trxDate = Carbon::parse($trxData->updated_at);
        $maxDate = Carbon::now()->addDays(7);
        if ($trxDate->lessThan($maxDate)) {
            // put new data
            $data = [
                'reason' => $reason,
                'updated_at' => Carbon::now(),
            ];

            // update review
            $updateData = DB::table($tableComp)
                ->where('id_complain', $idComp)
                ->where('id_trx', $idTrx)
                ->where('id_product', $idProduct)
                ->update($data);

            // return response
            if ($updateData) {
                return response()->json(['status' => 'success', 'message' => 'Komplain berhasil diupdate'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengupdate komplain'], 404);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa mengedit komplain'], 400);
        }
    }

    public function deleteComplain(Request $request)
    {
        $idComp = $request->input('id_complain');
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        $tableComp = $this->generateTableComp($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // mendapatkan data transaksi
        $trxData = DB::table($tableTrx)
            ->select()
            ->where('id_trx', $idTrx)
            ->first();

        // jika data transaksi berhasil didapatkan
        if ($trxData) {
            // jika tanggal trx sudah lebih dari 7 hari maka sudah tidak bisa hapus rvw
            $trxDate = Carbon::parse($trxData->updated_at);
            $maxDate = Carbon::now()->addDays(7);

            if ($trxDate->lessThan($maxDate)) {
                // menghapus comp
                $isDelete = DB::table($tableComp)
                    ->where('id_complain', $idComp)
                    ->where('id_trx', $idTrx)
                    ->where('id_product', $idProd)
                    ->delete();

                // return response
                if ($isDelete) {
                    return response()->json(['status' => 'success', 'message' => 'Komplain berhasil dihapus'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Komplain gagal dihapus'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa menghapus komplain'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
        }
    }
}
