<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductPromoController extends Controller
{
    public function generateTableProd($idShop)
    {
        return 'sp_' . $idShop . '_prod';
    }

    public function generateTablePromo($idShop)
    {
        return 'sp_' . $idShop . '_promo';
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

    public function isExistProduct($tableName, $idProd)
    {
        $isExist = DB::table($tableName)->where('id_product', '=', $idProd)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'ID produk sudah terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'ID produk belum terdaftar'];
        }
    }

    public function isPromo(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');
        $type = $request->input('type');

        $tableProd = $this->generateTableProd($idShop);
        $tablePromo = $this->generateTablePromo($idShop);

        // cek apakah toko exist atu tidak f
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // cek apakah product exist atau tidak
        $isExistProd = $this->isExistProduct($tableProd, $idProd);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Product tidak ditemukan'], 400);
        }

        // Ambil tanggal saat ini
        $currentDate = Carbon::now()->toDateString();

        $promoData = [];

        if ($type === 'active') {
            // get promo data
            $promoData = DB::table($tablePromo)->select()
                ->where("id_product",  $idProd)
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->limit(1)->get();
        } else if ($type === 'soon') {
            // get promo data
            $promoData = DB::table($tablePromo)->select()
                ->where("id_product",  $idProd)
                ->where('start_date', '>=', $currentDate)
                ->limit(1)->get();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Type not found'], 400);
        }

        // cek apakah $promoData = [] atau tidak
        if ($promoData->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data promo yang ditemukan'], 404);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Promo ditemukan'], 200);
        }
    }

    public function getPromos(Request $request)
    {
        $idShop = $request->input('id_shop');
        $type = $request->input('type');

        $tableProd = $this->generateTableProd($idShop);
        $tablePromo = $this->generateTablePromo($idShop);

        $isExistShop = $this->isExistShop($idShop);

        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // Ambil tanggal saat ini
        $currentDate = Carbon::now()->toDateString();

        $promoData = [];

        if ($type === 'soon') {
            $promos = DB::table(DB::raw("$tablePromo as prm"))
                ->join(DB::raw("$tableProd as prod"), "prod.id_product", "prm.id_product")
                ->select("prm.*", "prod.id_shop", "prod.product_name", "prod.id_cp_prod", "prod.price", "prod.photo")
                ->where('prm.start_date', '>', $currentDate)
                ->orderByDesc('prm.end_date')
                ->get();
        } else if ($type === 'active') {
            $promos = DB::table(DB::raw("$tablePromo as prm"))
                ->join(DB::raw("$tableProd as prod"), "prod.id_product", "prm.id_product")
                ->select("prm.*", "prod.id_shop", "prod.product_name", "prod.id_cp_prod", "prod.price", "prod.photo")
                ->where('prm.start_date', '<=', $currentDate)
                ->where('prm.end_date', '>=', $currentDate)
                ->orderByDesc('prm.end_date')
                ->get();
        } else if ($type === 'expired') {
            $promos = DB::table(DB::raw("$tablePromo as prm"))
                ->join(DB::raw("$tableProd as prod"), "prod.id_product", "prm.id_product")
                ->select("prm.*", "prod.id_shop", "prod.product_name", "prod.id_cp_prod", "prod.price", "prod.photo")
                ->where('prm.end_date', '<', $currentDate)
                ->orderByDesc('prm.end_date')
                ->get();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Type not found'], 400);
        }

        // cek apakah $promoData = [] atau tidak
        if ($promos->isEmpty()) {
            return response()->json(['status' => 'success', 'message' => 'Data promo kosong', 'data' => []], 200);
        } else {
            // add photo path
            foreach ($promos as $prm) {
                $prm->photo = asset('prods/' . $prm->photo);
            }
            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $promos], 200);
        }
    }

    public function addPromo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "id_shop" => "required|numeric",
            "id_product" => "required|numeric",
            'promo_price' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ], [
            'id_shop' => 'Id Toko tidak valid',
            'id_product' => 'Id Product tidak valid',
            'promo_price.required' => 'Harga promo harus diisi.',
            'promo_price.numeric' => 'Harga promo harus berupa integer.',
            'start_date.required' => 'Tanggal awal promo harus diisi.',
            'start_date.date' => 'Tanggal awal harus berupa Date',
            'end_date.required' => 'Tanggal akhir promo harus diisi.',
            'end_date.date' => 'Tanggal akhir harus berupa Date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');
        $promoPrice = $request->input('promo_price');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $tableProd = $this->generateTableProd($idShop);
        $tablePromo = $this->generateTablePromo($idShop);

        // cek apakah toko exist atu tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // cek apakah product exist atau tidak
        $isExistProd = $this->isExistProduct($tableProd, $idProd);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Product tidak ditemukan'], 400);
        }

        // cek apakah produk sudah memiliki promo yang akan aktif atau tidak
        $request->merge(['type' => 'soon']);
        $isExistSoon = $this->isPromo($request)->getData();
        if ($isExistSoon->status === 'success') {
            return response()->json(['status' => 'error', 'message' => 'Produk masih memiliki promo yang akan aktif'], 400);
        }

        // cek apakah produk sudah memiliki promo yang aktif atau tidak
        $request->merge(['type' => 'active']);
        $isExistSoon = $this->isPromo($request)->getData();
        if ($isExistSoon->status === 'success') {
            return response()->json(['status' => 'error', 'message' => 'Produk masih memiliki promo yang aktif'], 400);
        }

        // get product data
        $productData = DB::table($tableProd)->select()
            ->where("id_product", $idProd)
            ->limit(1)->first();

        // harga promo harus min rp. 1
        if ($promoPrice <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Harga promo minimal Rp. 1'], 400);
        }

        // cek apakah harga promo < harga asli produk
        if ($promoPrice >= $productData->price) {
            return response()->json(['status' => 'error', 'message' => 'Harga promo harus kurang dari harga product'], 400);
        }

        // get date now
        $currentDate = new DateTime();

        // convert to object
        $sDate = new DateTime($startDate);
        $eDate = new DateTime($endDate);

        // cek apakah tanggal awak < tgl saat ini
        if ($sDate < $currentDate) {
            // Jika salah satu tanggal kurang dari tanggal saat ini
            return response()->json(['status' => 'error', 'message' => 'Tanggal awal tidak boleh kurang dari tanggal saat ini'], 400);
        }

        // cek apakah tanggal awak < tgl saat ini
        if ($eDate < $currentDate) {
            // Jika salah satu tanggal kurang dari tanggal saat ini
            return response()->json(['status' => 'error', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal saat ini'], 400);
        }

        // jika tanggal akhir kurang dari tanggal awal
        if ($eDate <= $sDate) {
            return response()->json(['status' => 'error', 'message' => 'Tanggal akhir promo harus lebih besar dari tanggal awal'], 400);
        }

        $percentage = (($productData->price - $promoPrice) / $productData->price) * 100;

        // Format hasil perhitungan persentase agar hanya menampilkan dua angka dibelakang koma
        $formattedPercentage = number_format($percentage, 2);

        // put data
        $data = [
            "id_product" => $idProd,
            "promo_price" => $promoPrice,
            "percentage" => $formattedPercentage,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ];

        // save data
        $saveData = DB::table($tablePromo)->insert($data);

        // return response
        if ($saveData) {
            return response()->json(['status' => 'success', 'message' => 'Promo ditambahkan'], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Promo gagal ditambahkan'], 400);
        }
    }

    public function updatePromo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id_promo" => "required|numeric",
            "id_shop" => "required|numeric",
            'promo_price' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ], [
            'id_promo' => 'Id Promo tidak valid',
            'id_shop' => 'Id Toko tidak valid',
            'promo_price.required' => 'Harga promo harus diisi.',
            'promo_price.numeric' => 'Harga promo harus berupa integer.',
            'start_date.required' => 'Tanggal awal promo harus diisi.',
            'start_date.date' => 'Tanggal awal harus berupa Date',
            'end_date.required' => 'Tanggal akhir promo harus diisi.',
            'end_date.date' => 'Tanggal akhir harus berupa Date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idPromo = $request->input('id_promo');
        $idShop = $request->input('id_shop');
        $promoPrice = $request->input('promo_price');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $tableProd = $this->generateTableProd($idShop);
        $tablePromo = $this->generateTablePromo($idShop);

        // cek apakah toko exist atu tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // cek apakah product exist atau tidak
        // $isExistProd = $this->isExistProduct($tableProd, $idProd);
        // if ($isExistProd['status'] === 'error') {
        //     return response()->json(['status' => 'error', 'message' => 'Product tidak ditemukan'], 400);
        // }

        // get product
        $productData = DB::table(DB::raw("$tablePromo as promo"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'promo.id_product')
            ->select('prod.*')
            ->limit(1)->first();

        //

        // get product data
        // $productData = DB::table($tableProd)->select()
        //     ->where("id_product", $idProd)
        //     ->limit(1)->first();

        // harga promo harus min rp. 1
        if ($promoPrice <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Harga promo minimal Rp. 1'], 400);
        }

        // cek apakah harga promo < harga asli produk
        if ($promoPrice >= $productData->price) {
            return response()->json(['status' => 'error', 'message' => 'Harga promo harus kurang dari harga product'], 400);
        }

        // get date now
        $currentDate = new DateTime();

        // convert to object
        $sDate = new DateTime($startDate);
        $eDate = new DateTime($endDate);

        // cek apakah tanggal awak < tgl saat ini
        if ($sDate < $currentDate) {
            // Jika salah satu tanggal kurang dari tanggal saat ini
            return response()->json(['status' => 'error', 'message' => 'Tanggal awal tidak boleh kurang dari tanggal saat ini'], 400);
        }

        // cek apakah tanggal awak < tgl saat ini
        if ($eDate < $currentDate) {
            // Jika salah satu tanggal kurang dari tanggal saat ini
            return response()->json(['status' => 'error', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal saat ini'], 400);
        }

        // convert to object
        $sDate = new DateTime($startDate);
        $eDate = new DateTime($endDate);

        // jika tanggal akhir kurang dari tanggal awal
        if ($eDate <= $sDate) {
            return response()->json(['status' => 'error', 'message' => 'Tanggal akhir promo harus lebih besar dari tanggal awal'], 400);
        }

        $percentage = (($productData->price - $promoPrice) / $productData->price) * 100;

        // Format hasil perhitungan persentase agar hanya menampilkan dua angka dibelakang koma
        $formattedPercentage = number_format($percentage, 2);

        // put data
        $data = [
            "promo_price" => $promoPrice,
            "percentage" => $formattedPercentage,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "updated_at" => Carbon::now(),
        ];

        // save data
        $updateData = DB::table($tablePromo)
            ->where("id_promo", $idPromo)
            ->update($data);

        // return response
        if ($updateData) {
            return response()->json(['status' => 'success', 'message' => 'Promo diupdate'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Promo gagal diupdate'], 400);
        }
    }

    public function deletePromo(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idPromo = $request->input('id_promo');

        $tablePromo = $this->generateTablePromo($idShop);

        // cek apakah toko exist atu tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // delete data
        $deleteData = DB::table($tablePromo)
            ->where("id_promo", $idPromo)
            ->limit(1)
            ->delete();

        // return response
        if ($deleteData) {
            return response()->json(['status' => 'success', 'message' => 'Promo dihapus'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Promo gagal dihapus'], 400);
        }
    }
}
