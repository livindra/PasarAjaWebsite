<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ProductCategories;
use App\Models\Shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function generateTableName($idShop)
    {
        return 'sp_' . $idShop . '_prod';
    }

    public function validateSettings($settings)
    {
        // decode json
        $decodedJson = json_decode($settings, true);

        // cek apakah json berhasil decode atau tidak
        if ($decodedJson === null && json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 'error', 'message' => 'Invalid JSON'];
        }

        // contoh data yang valid,
        $keysToCheck = [
            "is_recommended" => "boolean",
            "is_shown" => "boolean",
            "is_available" => "boolean"
        ];

        $errors = [];

        // cek apakah json yang diinputkan seusai format atau tidak
        foreach ($keysToCheck as $key => $expectedType) {
            if (!array_key_exists($key, $decodedJson)) {
                $errors[] = "Key '$key' tidak ditemukan dalam JSON.";
            } elseif (gettype($decodedJson[$key]) !== $expectedType) {
                $errors[] = "Value dari key '$key' harus bertipe data $expectedType.";
            }
        }

        // jika json tidak sesuai format
        if (!empty($errors)) {
            return ['status' => 'error', 'message' => $errors[0]];
        }

        // jika data valid
        return ['status' => 'success', 'message' => 'settings valid'];
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

    public function isExistCategory($idCategory)
    {
        $isExist = ProductCategories::where('id_cp_prod', '=', $idCategory)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'Category terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'Category tidak terdaftar'];
        }
    }

    public function isExistName($tableName, $prodName)
    {
        $isExist = DB::table($tableName)->where('product_name', '=', $prodName)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'Nama produk sudah terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'Nama produk belum terdaftar'];
        }
    }

    public function createProduct(Request $request)
    {

        // validasi data produk
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'id_cp_prod' => 'required|integer',
            'product_name' => 'required|min:4|max:50',
            'description' => 'nullable|max:250',
            'unit' => 'required|in:Gram,Kilogram,Ons,Kuintal,Ton,Liter,Milliliter,Sendok,Cangkir,Mangkok,Botol,Karton,Dus,Buah,Ekor',
            'selling_unit' => 'required|integer',
            'price' => 'required|integer|min:1',
            'photo' => 'required|file|image|max:512',
            'settings' => 'required|json',
        ], [
            'id_shop.required' => 'ID Shop tidak boleh kosong.',
            'id_shop.integer' => 'ID Shop harus berupa angka',
            'id_cp_prod.required' => 'Kategori produk tidak boleh kosong.',
            'id_cp_prod.integer' => 'ID Shop harus berupa angka',
            'product_name.required' => 'Nama produk tidak boleh kosong.',
            'product_name.min' => 'Nama produk minimal terdiri dari 4 karakter.',
            'product_name.max' => 'Nama produk maksimal terdiri dari 50 karakter.',
            'unit.required' => 'Satuan produk tidak boleh kosong.',
            'unit.in' => 'Satuan produk harus dalam format yang valid.',
            'selling_unit.required' => 'Satuan penjualan tidak boleh kosong.',
            'selling_unit.integer' => 'Satuan penjualan harus berupa angka.',
            'price.required' => 'Harga tidak boleh kosong.',
            'price.integer' => 'Harga harus berupa angka.',
            'price.min' => 'Harga minimal bernilai 1.',
            'photo.required' => 'Foto produk tidak boleh kosong.',
            'photo.max' => 'Ukuran foto produk tidak boleh lebih dari 512 kb',
            'photo.image' => 'File harus berupa gambar.',
            'settings.json' => 'Pengaturan harus berupa data JSON yang valid.',
        ]);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        // get data
        $idShop = $request->input('id_shop');
        $idCategory = $request->input('id_cp_prod');
        $productName = $request->input('product_name');
        $description = $request->input('description');
        $settings = $request->input('settings');
        $unit = $request->input('unit');
        $sellingUnit = $request->input('selling_unit');
        $price = $request->input('price');
        $photo = $request->file('photo');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // validasi data setting
        $validateSetting = $this->validateSettings($settings);

        // jika data setting tidak valid
        if ($validateSetting['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $validateSetting['message']], 400);
        }

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // cek apakah category terdaftar atau tidak
        $isExistCategory = $this->isExistCategory($idCategory);
        if ($isExistCategory['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistCategory['message']], 400);
        }

        // cek apakah nama produk sudah terdatar atau belum
        $isExistName = $this->isExistName($tableName, $productName);
        if ($isExistName['status'] === 'success') {
            return response()->json(['status' => 'error', 'message' => $isExistName['message']], 400);
        }

        // jika foto produk yang diinputkan valid
        if ($photo && $photo->isValid()) {
            // dapatkan format gambar
            $extension = $photo->getClientOriginalExtension();
            // ubah nama foto produk
            $fotoProduk = time() . '.' . $extension;
            // simpan foto produk
            if (app()->environment('local')) {
                $photo->move(public_path('shops/'), $fotoProduk);
            } else {
                // jika file gagal dipindahkan
                $isMoved = $photo->move(public_path(base_path('../public_html/public/shops/')), $fotoProduk);
                if (!$isMoved) {
                    return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan foto produk'], 500);
                }
            }
        } else {
            // Jika $photo bukan file yang valid
            return response()->json(['status' => 'error', 'message' => 'File foto tidak valid atau tidak ditemukan'], 400);
        }

        // simpan data produk
        $insertData = DB::table($tableName)->insert([
            'id_shop' => $idShop,
            'id_cp_prod' => $idCategory,
            'product_name' => $productName,
            'description' => $description,
            'settings' => $settings,
            'unit' => $unit,
            'selling_unit' => $sellingUnit,
            'price' => $price,
            'photo' => $fotoProduk,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // cek data berhasil disimpan atau tidak
        if ($insertData) {
            return response()->json(['status' => 'success', 'message' => 'Produk berhasil disimpan'], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Produk gagal disimpan'], 400);
        }
    }

    public function updateProduct(Request $request)
    {
        //
    }

    public function setStock(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_product');
        $stokStatus = $request->input('stock_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        $isExistProd = $this->isexist

        $settings = DB::table($tableName)->select('settings')
            ->where('id_product', '=', $idProduk)
            ->limit(1)->first();

        echo $settings;

    }

    public function setVisibility(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_produk');
        $visibilityStatus = $request->input('visibility_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        echo $tableName;
    }

    public function setRecomended(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_produk');
        $recomendedStatus = $request->input('recomended_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);
    }
}
