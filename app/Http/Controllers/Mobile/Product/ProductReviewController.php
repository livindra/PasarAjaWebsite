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

class ProductReviewController extends Controller
{

    public function generateTableTrx($idShop)
    {
        return 'sp_' . $idShop . '_trx';
    }

    public function generateTableReview($idShop)
    {
        return 'sp_' . $idShop . '_rvw';
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

    public function isExistTrx($tableTrx, $orderCode)
    {

        $isExistTrx = DB::table($tableTrx)
            ->select()
            ->where('order_code', $orderCode)
            ->exists();

        if ($isExistTrx) {
            return ['status' => 'success', 'message' => 'Trx ditemukan'];
        } else {
            return ['status' => 'error', 'message' => 'Trx tidak ditemukan'];
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

    public function isExistUser($idUser)
    {
        $isExist = DB::table('0users')->where('id_user', '=', $idUser)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'User terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'User tidak terdaftar'];
        }
    }

    /// get rating and user review
    public function getReviews(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // menghitung rating dari produk
        $productAverageRating = DB::table($tableRvw)
            ->select(DB::raw('ROUND(AVG(star), 1) as average_rating'))
            ->where('id_product', $idProd)
            ->first();

        // mendapatkan total review
        $totalReviews = DB::table($tableRvw)
            ->where('id_product', $idProd)
            ->count();

        // mendapatkan data rating produk
        $averageRating = $productAverageRating->average_rating;

        // jika tidak ada review, set rating rata-rata menjadi 0
        if ($averageRating === null) {
            $averageRating = 0;
        }

        $reviews = DB::table(DB::raw("$tableRvw as rvw"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'rvw.id_product')
            ->join('0users as us', 'us.id_user', 'rvw.id_user')
            ->join(DB::raw("$tableTrx as trx"), 'trx.id_trx', 'rvw.id_trx')
            ->select('rvw.*', 'prod.product_name', 'us.full_name', 'us.email', 'us.photo', 'trx.updated_at as order_date')
            ->where('rvw.id_product', $idProd)
            ->orderByDesc('rvw.id_review')
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->get();

        foreach ($reviews as $rvw) {
            $rvw->photo = asset('users/' . $rvw->photo);
        }

        $ratingData = [
            'rating' => $averageRating,
            'total_review' => $totalReviews,
            'reviewers' => $reviews,
        ];

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $ratingData], 200);
    }

    public function isReview(Request $request)
    {
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        $tableRvw = $this->generateTableReview($idShop);

        $isExist = DB::table($tableRvw)
            ->where('id_trx', $idTrx)
            ->where('id_product', $idProd)
            ->exists();

        if ($isExist) {
            return response()->json(['status' => 'success', 'message' => 'User sudah review'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'User belum review'], 400);
        }
    }

    public function addReview(Request $request, TransactionController $trxController)
    {
        $validator = Validator::make($request->all(), [
            'order_code' => 'required|string',
            'id_user' => 'required|integer',
            'id_shop' => 'required|integer',
            'id_product' => 'required|integer',
            'star' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:100',
        ], [
            'order_code.required' => 'Order code harus diisi.',
            'order_code.string' => 'Order code harus berupa teks.',
            'id_user' => 'ID user tidak valid.',
            'id_shop' => 'ID shop tidak valid.',
            'id_product' => 'ID product tidak valid.',
            'star.required' => 'Rating harus diisi.',
            'star.integer' => 'Rating harus berupa bilangan bulat.',
            'star.between' => 'Rating harus di antara 1 dan 5.',
            'comment.max' => 'Komentar maksimal 100 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $orderCode = $request->input('order_code');
        $idUser = $request->input('id_user');
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product');
        $star = $request->input('star');
        $comment = $request->input('comment');

        $tableRvw = $this->generateTableReview($idShop);
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

        // cek apakah id user cocok
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
            return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa menambahkan review'], 400);
        }

        // cek apakah user sudah review
        $request->merge(['id_trx' => $trxData->id_trx]);
        $isRvw = $this->isReview($request)->getData();
        if ($isRvw->status === 'success') {
            return response()->json(['status' => 'error', 'message' => 'User sudah review'], 400);
        }

        // cek apakah id produk ada didalam list transaksi
        foreach ($trxData->details as $trx) {
            // jika ketemu
            if ($trx->id_product === $idProduct) {
                $data = [
                    'id_user' => $idUser,
                    'id_trx' => $trxData->id_trx,
                    'id_product' => $idProduct,
                    'star' => $star,
                    'comment' => $comment,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $addData = DB::table($tableRvw)->insert($data);

                // return response
                if ($addData) {
                    return response()->json(['status' => 'success', 'message' => 'Review berhasil ditambahkan'], 201);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Gagal membuat review'], 404);
                }
            }
        }

        return response()->json(['status' => 'error', 'message' => 'ID Produk tidak termasuk dalam list pembelian'], 404);
    }

    public function updateReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_review' => 'required|integer',
            'id_trx' => 'required|integer',
            'id_shop' => 'required|integer',
            'id_product' => 'required|integer',
            'star' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:100',
        ], [
            'id_review' => 'ID Review harus diisi',
            'id_trx' => 'ID Trx tidak valid.',
            'id_shop' => 'ID shop tidak valid.',
            'id_product' => 'ID product tidak valid.',
            'star.required' => 'Rating harus diisi.',
            'star.integer' => 'Rating harus berupa bilangan bulat.',
            'star.between' => 'Rating harus di antara 1 dan 5.',
            'comment.max' => 'Komentar maksimal 100 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idRvw = $request->input('id_review');
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product');
        $star = $request->input('star');
        $comment = $request->input('comment');

        $tableRvw = $this->generateTableReview($idShop);
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
        // jika tanggal trx sudah lebih dari 7 hari maka sudah tidak bisa update rvw
        $trxDate = Carbon::parse($trxData->updated_at);
        $maxDate = Carbon::now()->addDays(7);
        if ($trxDate->lessThan($maxDate)) {
            // put new data
            $data = [
                'star' => $star,
                'comment' => $comment,
                'updated_at' => Carbon::now(),
            ];

            // update review
            $updateData = DB::table($tableRvw)
                ->where('id_review', $idRvw)
                ->where('id_trx', $idTrx)
                ->where('id_product', $idProduct)
                ->update($data);

            // return response
            if ($updateData) {
                return response()->json(['status' => 'success', 'message' => 'Review berhasil diupdate'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengupdate review'], 404);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa mengedit review'], 400);
        }
    }

    public function deleteReview(Request $request)
    {
        $idReview = $request->input('id_review');
        $idTrx = $request->input('id_trx');
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        $tableRvw = $this->generateTableReview($idShop);
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

                // menghapus rvw
                $isDelete = DB::table($tableRvw)
                    ->where('id_review', $idReview)
                    ->where('id_trx', $idTrx)
                    ->where('id_product', $idProd)
                    ->delete();

                if ($isDelete) {
                    return response()->json(['status' => 'success', 'message' => 'Review berhasil dihapus'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Review gagal dihapus'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Transaksi sudah lebih dari 7 hari, tidak bisa menghapus review'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
        }
    }

    /// get all user review
    public function getAllReview(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product', 0);
        $star = $request->input('star', 0);
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);

        // get all data review
        $getData = DB::table(DB::raw("$tableRvw as rvw"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'rvw.id_product')
            ->join('0users as us', 'us.id_user', 'rvw.id_user')
            ->join(DB::raw("$tableTrx as trx"), 'trx.id_trx', 'rvw.id_trx')
            ->orderByDesc('rvw.id_review')
            ->when($idProduct !== 0, function ($query) use ($idProduct) {
                $query->where('prod.id_product', $idProduct);
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->when($star > 0 && $star <= 5, function ($query) use ($star) {
                $query->where('rvw.star', $star);
            })
            ->select('rvw.*', 'prod.product_name', 'us.full_name', 'us.email', 'us.photo', 'trx.updated_at as order_date')
            ->get();


        foreach ($getData as $prod) {
            $prod->photo = asset('users/' . $prod->photo);
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil didapatkan', 'data' => $getData], 200);
    }

    public function getHighestReview(Request $request)
    {

        $idShop = $request->input('id_shop');
        $idCategory = $request->input('id_category', 0);
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);

        $products = DB::table(DB::raw("$tableProd as prod"))
            ->select(
                'prod.id_product',
                'prod.product_name',
                'prod.id_cp_prod',
                'prod.photo',
                DB::raw("AVG(rvw.star) as rating"),
                DB::raw("COUNT(rvw.id_review) as reviewer")
            )
            ->leftJoin(DB::raw("$tableRvw as rvw"), 'prod.id_product', 'rvw.id_product')
            ->groupBy('prod.id_product', 'prod.product_name', 'prod.id_cp_prod', 'prod.photo')
            ->orderByRaw('AVG(rvw.star) DESC')
            ->when($idCategory !== 0, function ($query) use ($idCategory) {
                $query->where('prod.id_cp_prod', $idCategory);
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->get();

        foreach ($products as $prod) {
            $prod->rating = doubleval($prod->rating);
            $prod->photo = asset('prods/' . $prod->photo);
        }
        return response()->json(['status' => 'success', 'message' => 'Data berhasil didapatkan', 'data' => $products], 200);
    }
}
