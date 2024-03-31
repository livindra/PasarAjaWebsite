<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategories;
use App\Models\Shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

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

    public function isExistProduct($tableName, $idProd)
    {
        $isExist = DB::table($tableName)->where('id_product', '=', $idProd)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'ID produk sudah terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'ID produk belum terdaftar'];
        }
    }

    // Method untuk menyimpan foto produk
    public function saveProductPhoto(Request $request)
    {
        $photo = $request->file('photo');

        // Mengecek apakah file foto produk valid
        if ($photo && $photo->isValid()) {
            // Mendapatkan format gambar
            $extension = $photo->getClientOriginalExtension();
            // Mengubah nama foto produk
            $fotoProduk = time() . '.' . $extension;
            // Menyimpan foto produk
            if (app()->environment('local')) {
                $photo->move(public_path('prods/'), $fotoProduk);
            } else {
                $isMoved = $photo->move(public_path(base_path('../public_html/public/prods/')), $fotoProduk);
                if (!$isMoved) {
                    return ['status' => 'error', 'message' => 'Gagal menyimpan foto produk'];
                }
            }
            // Jika foto produk berhasil disimpan
            return ['status' => 'success', 'message' => 'Foto produk berhasil disimpan', 'data' => ['filename' => $fotoProduk]];
        } else {
            // Jika $photo bukan file yang valid
            return ['status' => 'error', 'message' => 'File foto tidak valid atau tidak ditemukan'];
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
            'unit' => 'required|in:Gram,Kilogram,Ons,Kuintal,Ton,Liter,Milliliter,Sendok,Cangkir,Mangkok,Botol,Karton,Dus,Buah,Ekor,Gelas,Piring,Bungkus',
            'selling_unit' => 'required|integer',
            'price' => 'required|integer|min:1',
            'photo' => 'required|file|image|max:512',
            'settings' => 'nullable|json',
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
        $productName = strtolower($request->input('product_name'));
        $description = $request->input('description');
        // $settings = $request->input('settings');
        $unit = $request->input('unit');
        $sellingUnit = $request->input('selling_unit');
        $price = $request->input('price');
        $request->file('photo');

        // $settings = ;

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // validasi data setting
        // $validateSetting = $this->validateSettings($settings);

        // jika data setting tidak valid
        // if ($validateSetting['status'] === 'error') {
        //     return response()->json(['status' => 'error', 'message' => $validateSetting['message']], 400);
        // }

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

        // save foto produk
        $photoSaveResponse = $this->saveProductPhoto($request);

        // jika foto produk berhasil disimpan
        if ($photoSaveResponse['status'] == 'success') {
            // simpan data produk
            $insertData = DB::table($tableName)->insert([
                'id_shop' => $idShop,
                'id_cp_prod' => $idCategory,
                'product_name' => $productName,
                'description' => $description,
                // 'settings' => $settings,
                'unit' => $unit,
                'selling_unit' => $sellingUnit,
                'price' => $price,
                'photo' => $photoSaveResponse['data']['filename'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // cek data berhasil disimpan atau tidak
            if ($insertData) {
                return response()->json(['status' => 'success', 'message' => 'Produk berhasil disimpan'], 201);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Produk gagal disimpan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => $photoSaveResponse['message']], 500);
        }
    }

    public function updateProduct(Request $request)
    {

        // validasi data produk
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'id_product' => 'required|integer',
            'id_cp_prod' => 'required|integer',
            'product_name' => 'required|min:4|max:50',
            'description' => 'nullable|max:250',
            'unit' => 'required|in:Gram,Kilogram,Ons,Kuintal,Ton,Liter,Milliliter,Sendok,Cangkir,Mangkok,Botol,Karton,Dus,Buah,Ekor,Gelas,Piring,Bungkus',
            'selling_unit' => 'required|integer',
            'price' => 'required|integer|min:1',
            'photo' => 'required|file|image|max:512',
            'settings' => 'required|json',
        ], [
            'id_shop.required' => 'ID Shop tidak boleh kosong.',
            'id_shop.integer' => 'ID Shop harus berupa angka',
            'id_product.required' => 'ID Produk tidak boleh kosong.',
            'id_product.integer' => 'ID Produk harus berupa angka',
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
        $idProd = $request->input('id_product');
        $productName = strtolower($request->input('product_name'));
        $description = $request->input('description');
        $settings = $request->input('settings');
        $unit = $request->input('unit');
        $sellingUnit = $request->input('selling_unit');
        $price = $request->input('price');
        $request->file('photo');

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

        // cek apakah produk exist
        $isExistProd = $this->isExistProduct($tableName, $idProd);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistProd['message']], 400);
        }

        // get nama foto produk yang lama
        $oldData = DB::table($tableName)->select(['product_name', 'photo'])
            ->where('id_product', $idProd)
            ->limit(1)->first();

        // mendapatkan direktori foto
        $oldPhotoDir = '';
        if (app()->environment('local')) {
            $oldPhotoDir = public_path('prods/' . $oldData->photo);
        } else {
            $oldPhotoDir = public_path(base_path('../public_html/public/prods/')) . $oldData->photo;
        }

        // menghapus foto yang lama
        if (File::exists($oldPhotoDir)) {
            File::delete($oldPhotoDir);
        }

        // save foto produk yang baru
        $photoSaveResponse = $this->saveProductPhoto($request);

        // jika foto produk yang baru berhasil disimpan
        if ($photoSaveResponse['status'] == 'success') {

            // put new data
            $newData = [
                'id_cp_prod' => $idCategory,
                'description' => $description,
                'settings' => $settings,
                'unit' => $unit,
                'selling_unit' => $sellingUnit,
                'price' => $price,
                'photo' => $photoSaveResponse['data']['filename'],
                'updated_at' => Carbon::now(),
            ];

            // mendapatkan nama produk yang lama
            $oldProductname = $oldData->product_name;

            // jika user mengedit nama produk
            if ($productName !== $oldProductname) {

                // cek apakah nama produk yang baru sudah terdatar atau belum
                $isExistName = $this->isExistName($tableName, $productName);
                if ($isExistName['status'] === 'success') {
                    return response()->json(['status' => 'error', 'message' => $isExistName['message']], 400);
                }
            }

            // update nama produk
            $newData['product_name'] = $productName;

            // simpan data produk yang baru
            $isUpdate = DB::table($tableName)
                ->where('id_product', $idProd)
                ->update($newData);

            // jika proses update produk berhasil
            if ($isUpdate) {
                return response()->json(['status' => 'success', 'message' => 'Produk berhasil disimpan'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Produk gagal disimpan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => $photoSaveResponse['message']], 500);
        }
    }

    private function updateSettings($tableName, $idProd, $key, $value)
    {
        // validasi data key
        if ($key !== 'is_shown' && $key !== 'is_available' && $key !== 'is_recommended') {
            return ['status' => 'error', 'message' => 'Key tidak valid'];
        }
    
        // cek apakah produk exist
        $isExistProd = $this->isExistProduct($tableName, $idProd);
        if ($isExistProd['status'] === 'success') {
            // get settings
            $settings = DB::table($tableName)
                ->select('settings')
                ->where('id_product', '=', $idProd)
                ->first();
    
            // jika setting tidak kosong
            if ($settings) {
                // echo $settings->settings;
    
                // decode settings
                $settingsData = json_decode($settings->settings, true);
    
                // cek apakah ada perubahan nilai
                if ($settingsData[$key] !== $value) {
                    // edit value dari key
                    $settingsData[$key] = $value;
    
                    // encode settings
                    $updatedSettings = json_encode($settingsData);
    
                    // validasi data setting
                    $validateSetting = $this->validateSettings($updatedSettings);
    
                    // jika data setting tidak valid
                    if ($validateSetting['status'] === 'error') {
                        return ['status' => 'error', 'message' => $validateSetting['message']];
                    } else {
                        // update settings
                        $isUpdate = DB::table($tableName)
                            ->where('id_product', $idProd)
                            ->update(
                                [
                                    'settings' => $updatedSettings,
                                    'updated_at' => Carbon::now(),
                                ]
                            );
    
                        // jika data berhasil diupdate
                        if ($isUpdate) {
                            return ['status' => 'success', 'message' => 'Data berhasil diupdate'];
                        } else {
                            return ['status' => 'error', 'message' => 'Data ' . $key . ' gagal diupdate'];
                        }
                    }
                } else {
                    return ['status' => 'success', 'message' => 'Tidak ada perubahan nilai'];
                }
            } else {
                return ['status' => 'error', 'message' => 'Settings tidak ditemukan'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Product tidak ditemukan'];
        }
    }
    

    public function setStock(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_product');
        $stokStatus = $request->input('stock_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // update data settings
        $update = $this->updateSettings($tableName, $idProduk, 'is_available', $stokStatus);

        // jika setting berhasil diupdate
        if ($update['status'] === 'success') {
            return response()->json($update);
        } else {
            return response()->json($update, 400);
        }
    }

    public function setVisibility(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_product');
        $visibilityStatus = $request->input('visibility_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // update data settings
        $update = $this->updateSettings($tableName, $idProduk, 'is_shown', $visibilityStatus);

        // jika setting berhasil diupdate
        if ($update['status'] === 'success') {
            return response()->json($update);
        } else {
            return response()->json($update, 400);
        }
    }

    public function setRecommended(Request $request)
    {
        // get data
        $idShop = $request->input('id_shop');
        $idProduk = $request->input('id_product');
        $recomendedStatus = $request->input('recommended_status');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // update data settings
        $update = $this->updateSettings($tableName, $idProduk, 'is_recommended', $recomendedStatus);

        // jika setting berhasil diupdate
        if ($update['status'] === 'success') {
            return response()->json($update);
        } else {
            return response()->json($update, 400);
        }
    }

    public function allProducts(Request $request)
    {
        $idShop = $request->input('id_shop');
        $filter = $request->input('id_category', 0);
        $limit = $request->input('limit', 0);

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // cek jika data exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {

            if ($filter === 0) {
                // get all product
                $products = DB::table(DB::raw("$tableName as prod"))
                    ->join(DB::raw("0product_categories as ctg"), 'ctg.id_cp_prod', 'prod.id_cp_prod')
                    ->select("prod.*", "ctg.category_name")
                    ->orderBy('product_name', 'asc')
                    ->when($limit !== 0, function ($query) use ($limit) {
                        $query->limit($limit);
                    })
                    ->get();
            } else {
                // get all product
                $products = DB::table(DB::raw("$tableName as prod"))
                    ->join(DB::raw("0product_categories as ctg"), 'ctg.id_cp_prod', 'prod.id_cp_prod')
                    ->select("prod.*", "ctg.category_name")
                    ->orderBy('product_name', 'asc')
                    ->where('id_cp_prod', $filter)
                    ->when($limit !== 0, function ($query) use ($limit) {
                        $query->limit($limit);
                    })
                    ->get();
            }

            foreach ($products as $prod) {
                $prod->product_name = ucwords($prod->product_name);
                $prod->photo = asset('prods/' . $prod->photo);
            }

            return response()->json(['status' => 'success', 'message' => 'Toko tidak ditemukan', 'data' => $products], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function detailProduct(
        Request $request,
        ProductReviewController $productReview,
        ProductComplainController $productComplain,
        ProductHistoryController $prodcutHistory,
    ) {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // limit menjadi 5
        $request->merge(['limit' => 5]);

        // get review product
        $reviewsResponse = $productReview->getReviews($request);
        // jika terjadi kegagalan saat pengambilan review product
        if ($reviewsResponse->getStatusCode() !== 200) {
            // return error message
            $responseData = json_decode($reviewsResponse->getContent(), true);
            $errorMessage = $responseData['message'];
            return response()->json(['status' => 'error', 'message' => $errorMessage], 400);
        }

        // get complain product
        $complainsResponse = $productComplain->getComplains($request);
        // jika terjadi kegagalan saat pengambilan review product
        if ($complainsResponse->getStatusCode() !== 200) {
            // return error message
            $responseData = json_decode($complainsResponse->getContent(), true);
            $errorMessage = $responseData['message'];
            return response()->json(['status' => 'error', 'message' => $errorMessage], 400);
        }

        // get history product
        $historyResponse = $prodcutHistory->historyProduct($request);
        // jika terjadi kegagalan saat pengambilan review product
        if ($historyResponse->getStatusCode() !== 200) {
            // return error message
            $responseData = json_decode($historyResponse->getContent(), true);
            $errorMessage = $responseData['message'];
            return response()->json(['status' => 'error', 'message' => $errorMessage], 400);
        }

        // get product data
        $prodData = DB::table($tableName)->select()
            ->where('id_product', $idProd)
            ->limit(1)->first();

        // get category name
        $category = ProductCategories::select()
            ->where('id_cp_prod', $prodData->id_cp_prod)
            ->limit(1)->first();

        $prodData->product_name = ucwords($prodData->product_name);
        $prodData->photo = asset('prods/' . $prodData->photo);
        $prodData->rating = doubleval($reviewsResponse->getData()->data->rating);
        $prodData->category_name = $category->category_name;
        $prodData->total_review = $reviewsResponse->getData()->data->total_review;
        $prodData->reviews = $reviewsResponse->getData()->data->reviewers;
        $prodData->complains = $complainsResponse->getData()->data;
        $prodData->histories = $historyResponse->getData()->data;

        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $prodData], 200);
    }

    public function dataProduct(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // cek apakah toko exist atu tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // cek apakah produk exist
        $isExistProd = $this->isExistProduct($tableName, $idProd);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistProd['message']], 400);
        }

        // get data product
        $product = DB::table(DB::raw("$tableName as prod"))
            ->join(DB::raw("0product_categories as ctg"), "ctg.id_cp_prod", "prod.id_cp_prod")
            ->select("prod.*", "ctg.category_name")
            ->get()->first();

        // update product photo
        $product->photo = asset('prods/' . $product->photo);

        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $product], 200);
    }

    private function getSpecified(Request $request, $key, $acceptedValue)
    {
        $idShop = $request->input('id_shop');

        // geneate table name
        $tableName = $this->generateTableName($idShop);

        // get semua data product
        $prodData = DB::table($tableName)->select()->get();

        $prodList = [];

        // ambil data produk
        foreach ($prodData as $prod) {
            $settingsData = json_decode($prod->settings, true);
            $prod->photo = asset('prods/' . $prod->photo);

            if ($settingsData[$key] == $acceptedValue) {
                // Tambahkan $prod ke dalam $prodList
                $prodList[] = $prod;
            }
        }

        //  return response
        return ['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $prodList];
    }

    public function hiddenProducts(Request $request)
    {
        $request->input('id_shop');

        // get data produk yang hidden
        $prodData = $this->getSpecified($request, 'is_shown', false);

        if ($prodData['status'] === 'success') {
            return response()->json($prodData);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data gagal didapatkan'], 400);
        }
    }

    public function recommendedProducts(Request $request)
    {
        $request->input('id_shop');

        // get data produk yang hidden
        $prodData = $this->getSpecified($request, 'is_recommended', true);

        if ($prodData['status'] === 'success') {
            return response()->json($prodData);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data gagal didapatkan'], 400);
        }
    }

    public function unavlProducts(Request $request)
    {
        $request->input('id_shop');

        // get data produk yang hidden
        $prodData = $this->getSpecified($request, 'is_available', false);

        if ($prodData['status'] === 'success') {
            return response()->json($prodData);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data gagal didapatkan'], 400);
        }
    }

    public function bestSelling(Request $request)
    {
        $idShop = $request->input('id_shop');
        $limit = $request->input('limit', 1000);

        // geneate table name
        $tableName = $this->generateTableName($idShop);

        // get best selling
        $best = DB::table($tableName)->select('*')->orderByDesc('total_sold')
            ->limit($limit)->get();

        foreach ($best as $prod) {
            $prod->photo = asset('prods/' . $prod->photo);
        }

        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $best], 200);
    }

    public function deleteProduct(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        $tableProd = $this->generateTableName($idShop);

        // cek apakah toko exist atu tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // delete data
        $deleteData = DB::table($tableProd)
            ->where("id_product", $idProd)
            ->limit(1)
            ->delete();

        // return response
        if ($deleteData) {
            return response()->json(['status' => 'success', 'message' => 'Product berhasil dihapus'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Produk gagal dihapus'], 400);
        }
    }
}
