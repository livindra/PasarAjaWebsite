<?php

namespace App\Http\Controllers\Mobile\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Firebase\MessagingController;
use App\Http\Controllers\Messenger\MailController;
use App\Http\Controllers\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Mobile\Product\ProductController;
use App\Http\Controllers\Website\ShopController;
use App\Mail\CustomerRejected;
use App\Mail\Finished;
use App\Mail\InTaking;
use App\Mail\MerchantRejected;
use App\Mail\OrderConfirmed;
use App\Mail\OrderRequest;
use App\Mail\Submitted;
use App\Models\RefreshToken;
use App\Models\Shops;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    public function generateTableProd($idShop)
    {
        return 'sp_' . $idShop . '_prod';
    }

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

    public function getNamaHari($date)
    {
        // Mendapatkan nama hari dalam bahasa Indonesia dari objek DateTime
        $hari = $date->format('l'); // 'l' mengembalikan nama hari dalam bahasa Inggris

        // Kamus penggantian nama hari dalam bahasa Indonesia
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        // Mengganti nama hari dalam bahasa Inggris dengan bahasa Indonesia
        return $hariIndonesia[$hari];
    }

    public function isExistTrx($idShop, $orderCode)
    {
        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        $isExistShop = $this->isExistShop($idShop);

        if ($isExistShop['status'] === 'success') {
            $isExist = DB::table($tableTrx)
                ->select()
                ->where('order_code', $orderCode)
                ->exists();

            if ($isExist) {
                return ['status' => 'success', 'message' => 'Trx exist'];
            } else {
                return ['status' => 'error', 'message' => 'Trx not exist'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Shop not exist'];
        }
    }

    public function trxDetail(Request $request)
    {
        $idShop = $request->input('id_shop');
        $code = $request->input('order_code');
        $showUsData = $request->input('user_data', true);
        $showSpData = $request->input('shop_data', false);

        $isExist = $this->isExistTrx($idShop, $code);

        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        if ($isExist['status'] === 'success') {

            // mendapatkan data transaksi
            $trxData = DB::table($tableTrx)
                ->select()
                ->where('order_code', $code)
                ->first();

            if ($trxData) {
                // generate order id
                $orderId = strtoupper('#' . Str::afterLast($code, '-'));
                $trxData->order_id = $orderId;

                // mendapatkan detail produk
                $details = DB::table(DB::raw("$tableDtl as dtl"))
                    ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'dtl.id_product')
                    ->select(
                        [
                            'dtl.id_product',
                            'quantity',
                            'prod.product_name',
                            DB::raw("CONCAT('" . asset('prods') . "/', prod.photo) AS product_photo"),
                            'prod.unit',
                            'prod.selling_unit',
                            'prod.price',
                            DB::raw('(dtl.promo_price * quantity) as promo_price'),
                            'dtl.notes',
                            DB::raw('(prod.price * dtl.quantity) - (dtl.promo_price * dtl.quantity) AS total_price')
                        ]
                    )
                    ->where('dtl.id_trx', $trxData->id_trx)
                    ->get();

                // jika detail produk kosong
                if ($details->isEmpty()) {
                    return response()->json(['status' => 'error', 'message' => 'Detail produk tidak ditemukan'], 400);
                }


                // hitung rician pesaanan
                $totalProd = 0;
                $totalQuantity = 0;
                $subTotal = 0;
                $totalPromo = 0;
                $totalPrice = 0;
                foreach ($details as $detail) {
                    $totalProd++;
                    $totalQuantity += $detail->quantity;
                    $subTotal += ($detail->price * $detail->quantity);
                    $totalPromo += $detail->promo_price;
                    $totalPrice += $detail->total_price;
                }
                $trxData->total_product = $totalProd;
                $trxData->total_quantity = $totalQuantity;
                $trxData->total_promo = $totalPromo;
                $trxData->sub_total = $subTotal;
                $trxData->total_price = $totalPrice;

                // menambahkan detail produk
                $trxData->details = $details;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data transaksi gagal didapatkan'], 400);
            }

            if ($showUsData) {
                // get user data
                $userData = DB::table('0users as ussr')
                    ->where('id_user', $trxData->id_user)
                    ->select(
                        [
                            'ussr.id_user',
                            'ussr.full_name',
                            'ussr.email',
                            'ussr.phone_number',
                            DB::raw("CONCAT('" . asset('users') . "/', ussr.photo) as user_photo"),
                        ]
                    )->first();

                if ($userData) {
                    $trxData->user_data = $userData;
                }
            }


            if ($showSpData) {
                // get shop data
                $shopCont = new ShopController();
                $shopData = $shopCont->getShopData($request, new Shops())->getData();

                if ($shopData) {
                    $trxData->shop_data = $shopData->data;
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $trxData], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => $isExist['message']], 404);
        }
    }

    public function listOfTrx(Request $request)
    {
        $idShop = $request->input('id_shop');
        $status = $request->input('status', '-');

        // cek toko terdaftar atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 404);
        }

        // generate table
        $tableProd = $this->generateTableProd($idShop);
        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // get data
        $trxData = DB::table(DB::raw("$tableTrx as trx"))
            ->join('0users as usr', 'usr.id_user', 'trx.id_user')
            ->select([
                'trx.*',
                'usr.full_name',
                'usr.phone_number',
                DB::raw("CONCAT('" . asset('users') . "/', usr.photo) as user_photo"),
            ])
            ->when($status !== '-', function ($query) use ($status) {
                $query->where('trx.status', $status);
            })
            ->get();

        // get detail prod
        foreach ($trxData as $key => $trx) {
            $details = DB::table(DB::raw("$tableDtl as dtl"))
                ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'dtl.id_product')
                ->select(
                    [
                        'dtl.id_product',
                        'quantity',
                        'prod.product_name',
                        DB::raw("CONCAT('" . asset('prods') . "/', prod.photo) AS product_photo"),
                        'prod.unit',
                        'prod.selling_unit',
                        'prod.price',
                        'dtl.promo_price'
                    ]
                )
                ->where('dtl.id_trx', $trx->id_trx)
                ->get();

            // menambahkan detail trx
            $trxData[$key]->details = $details;
        }

        return response()->json(['status' => 'success', 'message' => 'testing', 'data' => $trxData], 200);
    }

    public function createTrx(
        Request $request,
        Shops $shops,
        User $user,
        ShopController $shopController,
        ProductController $productController,
        MobileAuthController $mobileAuth,
        MailController $mailController,
    ) {
        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'id_user' => 'required|integer',
            'email' => 'required|email',
            'taken_date' => 'required|date',
            'products' => 'required|array',
            'products.*.id_product' => 'required|integer',
            'products.*.quantity' => 'required|integer',
            'products.*.promo_price' => 'nullable|integer',
            'products.*.notes' => 'nullable|max:100'
        ], [
            'id_shop' => 'Id Toko tidak valid',
            'id_user' => 'Id User tidak valid.',
            'email.required' => 'Email harus diisi',
            'email.email' => 'email tidak valid',
            'taken_date.required' => 'taken date harus diisi',
            'taken_date.date' => 'Taken date harus dalam bentuk tanggal',
            'products.required' => 'Detail produk harus diisi',
            'products.json' => 'Detail produk harus dalam format json',
            'products.*.id_product.required' => 'ID Produk harus diisi',
            'products.*.id_product.integer' => 'ID Prduk harus berupa integer',
            'products.*.quantity.required' => 'Quantity harus diisi',
            'products.*.quantity.integer' => 'Quantity harus berupa integer',
            'products.*.promo_price.integer' => 'Harga promo harus diisid',
            'products.*.notes' => 'Panjang catatan tidak boleh lebih dari 100 karakter',
        ]);

        $validator->after(function ($validator) use ($request) {
            $idProducts = array_column($request->input('products'), 'id_product');
            if (count($idProducts) !== count(array_unique($idProducts))) {
                $validator->errors()->add('products', 'ID Product ada yang duplikat');
            }
        });

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $idUser = $request->input('id_user');
        $email = $request->input('email');
        $takenDate = $request->input('taken_date');
        $products = $request->input('products');

        // generate table
        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);
        $tableProd = $this->generateTableProd($idShop);

        // cek toko existance
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 404);
        }

        // cek user existance
        $isExistUser = $user->select()
            ->where('id_user', $idUser)
            ->where('email', $email)
            ->limit(1)->exists();
        if (!$isExistUser) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        // cek apakah user sudah login atau belum
        $isOnLogin = $mobileAuth->isOnLogin($request)->getData();
        if ($isOnLogin->status === 'error') {
            return response()->json(['status' => 'error', 'message' => 'User belum login'], 404);
        }

        // get tanggal saat ini
        $currentDate = new DateTime();
        // batas maximal hari pengambilan barang
        $maxDate = new DateTime();
        $maxDate->modify('+7 days');

        // convert $takenDate to datetime
        $tDate = new DateTime($takenDate);

        // cek tanggal diambil < tanggal saat ini
        if ($tDate < $currentDate) {
            return response()->json(['status' => 'error', 'message' => 'Tanggal diambil tidak boleh kurang dari tanggal saat ini'], 400);
        }

        // cek tanggal diambil lebih dari 7 hari dari tanggal saat ini
        if ($tDate > $maxDate) {
            return response()->json(['status' => 'error', 'message' => 'Tanggal diambil tidak boleh lebih dari 5 hari dari tanggal saat ini'], 400);
        }

        // Mendapatkan data toko
        $shopData = $shops->select()
            ->where('id_shop', $idShop)
            ->first();

        if ($shopData) {
            // Mendapatkan nama hari pengambilan barang
            $hari = $this->getNamaHari($tDate);

            // convert 'operational' menjadi array
            $operationalArray = json_decode($shopData->operational, true);

            // Memastikan $operationalArray adalah array sebelum mengakses
            if (is_array($operationalArray) && isset($operationalArray[$hari])) {
                // cek apakah toko tutup atau tidak pada hari pengambilan barang
                $jamBuka = $operationalArray[$hari];
                if ($jamBuka === 'tutup') {
                    return response()->json(['status' => 'error', 'message' => 'Toko sedang tutup pada tanggal pengambilan'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Ada masalah pada jam operasional Toko'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Ada masalah pada Toko'], 400);
        }

        // validasi data produk yang dibeli
        foreach ($products as $prod) {
            // Mendapatkan data produk
            $newRequest = $request->duplicate();
            $newRequest->merge(['id_product' => $prod['id_product']]);
            $prodData = $productController->dataProduct($newRequest)->getData();

            // Jika produk tidak ditemukan
            if ($prodData->status === 'error') {
                return response()->json(['status' => 'error', 'message' => $prodData->message], 400);
            } else {
                // mendapatkan keterangan produk
                $prodData = DB::table($tableProd)
                    ->select(['product_name', 'settings'])
                    ->where('id_product', $prod['id_product'])
                    ->first();

                if ($prodData) {
                    // decode keterangan produk
                    $settings = json_decode($prodData->settings);

                    // validasi data settings / keterangan produk
                    if (isset($settings->is_shown) && isset($settings->is_available)) {
                        // cek apakah produk ditampilkan atau tidak
                        if ($settings->is_shown === false) {
                            return response()->json(['status' => 'error', 'message' => "Produk '" . ucwords($prodData->product_name) . "' sedang tidak ditampilkan oleh Penjual"], 400);
                        }

                        // cek apakah produk stok nya habis atau tidak
                        if ($settings->is_available === false) {
                            return response()->json(['status' => 'error', 'message' => "Stok dari produk '" . ucwords($prodData->product_name) . "' sedang habis"], 400);
                        }
                    } else {
                        return response()->json(['status' => 'error', 'message' => "Data produk '" . ucwords($prodData->product_name) . "' tidak valid"], 400);
                    }
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Product setting not found'], 404);
                }
            }

            // Cek apakah quantity produk < 0
            if ($prod['quantity'] <= 0) {
                return response()->json(['status' => 'error', 'message' => 'Quantity dari produk minimal harus minimal 1'], 400);
            }

            // Cek apakah promo price produk < -1
            if ($prod['promo_price'] < 0) {
                return response()->json(['status' => 'error', 'message' => 'Promo price tidak boleh minus'], 400);
            }

            // reset request
            $newRequest = null;
        }

        // generate order code
        $orderCode = 'PasarAja-' . Str::uuid()->toString();
        $orderPin = mt_rand(1000, 9999);

        // generate exp time for merchant confirmation
        $currentMillis = round(microtime(true) * 1000);
        $expTime = $currentMillis + (2 * 3600 * 1000);

        $data = [
            'id_user' => $idUser,
            'order_code' => $orderCode,
            'order_pin' => $orderPin,
            'status' => 'Request',
            'taken_date' => $takenDate,
            'expiration_time' => $expTime,
            'confirmed_by' => '0',
            'canceled_message' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $createTrx = DB::table($tableTrx)
            ->insert($data);

        // cek data berhasil disimpan atau tidak
        if ($createTrx) {
            // get id trx from order code
            $idTrx = DB::table($tableTrx)
                ->select('id_trx')
                ->where('order_code', $orderCode)
                ->limit(1)->value('id_trx');

            try {
                // jika id ditemukan
                if ($idTrx !== null) {
                    // add detail product
                    foreach ($products as $prod) {
                        $prod['id_trx'] = $idTrx;
                        $addDtl = DB::table($tableDtl)
                            ->insert($prod);

                        // jika gagal menambahkan detail product
                        if (!$addDtl) {
                            // delete transaksi & return response
                            DB::table($tableTrx)
                                ->where('id_trx', $idTrx)
                                ->delete();
                            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan detail transaksi'], 400);
                        }
                    }

                    // save to user trx
                    $this->addToTrx($idUser, $idShop, $orderCode);

                    // get data detail transaksi
                    $nReq = new Request();
                    $nReq->merge(['id_shop' => $idShop, 'order_code' => $orderCode]);
                    $detailTrx = $this->trxDetail($nReq)->getData();

                    // jika data detail trx berhasil didapatkan
                    if ($detailTrx->status === 'success') {
                        // get shop contact
                        $contactData = $shopController->getContact($request)->getData();
                        if ($contactData->status === 'success') {
                            // send notif and data transaction
                            $detailData = $detailTrx->data;
                            Mail::to($contactData->data->email)->send(new OrderRequest($detailData));
                        }
                    }

                    return response()->json(['status' => 'success', 'message' => 'Berhasil membuat transaksi'], 201);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'ID Transaksi tidak ditemukan'], 404);
                }
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kegagalan saat membuat transaksi'], 400);
        }
    }

    private function addToTrx($idUser, $idShop, $orderCode)
    {
        $collection = "us_" . $idUser . "_trx";
        // create request
        $request = new Request();
        $request->merge([
            'id_shop' => $idShop,
            'order_code' => $orderCode,
            'user_data' => false,
            'shop_data' => true,
        ]);

        // get data transaksi
        $trxData = $this->trxDetail($request)->getData();

        if ($trxData) {
            // register firebase
            $firestore = app('firebase.firestore')
                ->database()
                ->collection($collection);

            // mencari data dengan order_code yang sama
            $query = $firestore->where('order_code', '=', $orderCode);
            $documents = $query->documents();

            // menghapus data order_code yang lama
            foreach ($documents as $document) {
                $document->reference()->delete();
            }

            // membuat dokumen baru
            $newDocument = $firestore->newDocument();

            // convert trx data to array
            $trxDataArray = json_decode(json_encode($trxData->data), true);

            // save trx data to firebase
            $newDocument->set($trxDataArray);

            return true;
        }
        return false;
    }

    public function cancelByCustomer(Request $request, ShopController $shopController)
    {

        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
            'reason' => 'required',
            'message' => 'required'
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
            'reason' => 'Alasan harus diisi',
            'message' => 'Pesan pembatalan harus diisi'
        ]);

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');
        $rejectedReason = $request->input('reason');
        $rejectedMessage = $request->input('message');

        $message = $rejectedReason . '/' . $rejectedMessage;

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data of transaction & add rejected detail
        $trxData = $trx->data;
        $trxData->rejected_reason = $rejectedReason;
        $trxData->rejected_message = $rejectedMessage;
        $trxData->status = 'Cancel_Customer';

        // put new data
        $newData = [
            'status' => 'Cancel_Customer',
            'canceled_message' => $message,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        // cek apakah pembatalan berhasil
        if ($isUpdate) {

            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);


            // get shop contact
            $contactData = $shopController->getContact($request)->getData();
            if ($contactData->status === 'success') {
                Mail::to($contactData->data->email)->send(new CustomerRejected($trxData));
            }

            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal membatalkan pesanan'], 400);
        }
    }

    public function cancelByMerchant(Request $request, ShopController $shopController, Shops $shops)
    {

        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
            'reason' => 'required',
            'message' => 'required'
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
            'reason' => 'Alasan harus diisi',
            'message' => 'Pesan pembatalan harus diisi'
        ]);

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');
        $rejectedReason = $request->input('reason');
        $rejectedMessage = $request->input('message');

        $message = $rejectedReason . '/' . $rejectedMessage;

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data of transaction & add rejected detail
        $trxData = $trx->data;
        $trxData->rejected_reason = $rejectedReason;
        $trxData->rejected_message = $rejectedMessage;
        $trxData->status = 'Cancel_Merchant';

        // get shop data
        $shopData = $shopController->getShopData($request, $shops)->getData();
        if ($shopData->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $shopData['message']], 400);
        }
        $trxData->shop_data = $shopData->data;

        // put new data
        $newData = [
            'status' => 'Cancel_Merchant',
            'canceled_message' => $message,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        // cek apakah pembatalan berhasil
        if ($isUpdate) {
            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);

            // return response & send email to user
            Mail::to($trxData->user_data->email)->send(new MerchantRejected($trxData));
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal membatalkan pesanan'], 400);
        }
    }

    public function confirmTrx(Request $request, ShopController $shopController, Shops $shops)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
        ]);

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data
        $trxData = $trx->data;

        // get expiration confirmed
        $expTime = $trxData->expiration_time;
        $millisNow = Carbon::now()->timestamp * 1000;

        // cek apakah pesanan sudah kadaluarsa atau belum
        if ($millisNow > $expTime) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan telah melewati batas waktu konfirmasi (kadaluarsa)'], 400);
        }

        // get shop data
        $shopData = $shopController->getShopData($request, $shops)->getData();
        if ($shopData->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $shopData['message']], 400);
        }

        // update expiration time
        $expTaken = Carbon::now()->addHours(24)->timestamp * 1000;
        $expDate = Carbon::createFromTimestamp($expTaken / 1000);

        // add shop data and new exp time
        $trxData->shop_data = $shopData->data;
        $trxData->expiration_time = $expTaken;
        $trxData->expiration_date = $expDate;
        $trxData->status = 'Confirmed';

        // put new data
        $newData = [
            'status' => 'Confirmed',
            'expiration_time' => $expTaken,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        // cek apakah konfirmasi berhasil
        if ($isUpdate) {
            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);

            // return response & send email to user
            Mail::to($trxData->user_data->email)->send(new OrderConfirmed($trxData));
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dikonfirmasi', 'data' => $trxData], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengkonfirmasi pesanan'], 400);
        }
    }

    public function inTakingTrx(Request $request, ShopController $shopController, Shops $shops)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
        ]);


        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data
        $trxData = $trx->data;

        // get expiration taking
        $expTime = $trxData->expiration_time;
        $confDate = Carbon::createFromTimestamp($expTime / 1000);
        $millisNow = Carbon::now()->timestamp * 1000;

        // cek apakah pesanan sudah kadaluarsa atau belum
        if ($millisNow > $expTime) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan telah melewati batas waktu pengambilan (kadaluarsa)'], 400);
        }

        // update expiration time
        $expSubmitted = Carbon::now()->addHours(5)->timestamp * 1000;
        $expDate = Carbon::createFromTimestamp($expSubmitted / 1000);

        // add shop data and new exp time
        $trxData->confirmation_date = $confDate;
        $trxData->expiration_time = $expSubmitted;
        $trxData->expiration_date = $expDate;
        $trxData->status = 'InTaking';

        // put new data
        $newData = [
            'status' => 'InTaking',
            'expiration_time' => $expSubmitted,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        // cek apakah update berhasil
        if ($isUpdate) {
            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);

            // get shop contact
            $contactData = $shopController->getContact($request)->getData();
            if ($contactData->status === 'success') {
                // return response & send email to merchant
                Mail::to($contactData->data->email)->send(new InTaking($trxData));
            }
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil diupdate', 'data' => $trxData], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal update pesanan'], 400);
        }
    }

    public function submittedTrx(Request $request, ShopController $shopController, Shops $shops)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
        ]);

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data
        $trxData = $trx->data;

        // get expiration submitted
        $expTime = $trxData->expiration_time;
        $confDate = Carbon::createFromTimestamp($expTime / 1000);
        $millisNow = Carbon::now()->timestamp * 1000;

        // cek apakah pesanan sudah kadaluarsa atau belum
        // if ($millisNow > $expTime) {
        //     return response()->json(['status' => 'error', 'message' => 'Pesanan telah melewati batas waktu penyerahan (kadaluarsa)'], 400);
        // }

        // update expiration time
        $expFinished = Carbon::now()->addHours(1)->timestamp * 1000;
        $expDate = Carbon::createFromTimestamp($expFinished / 1000);

        // get shop data
        $shopData = $shopController->getShopData($request, $shops)->getData();
        if ($shopData->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $shopData['message']], 400);
        }

        // add shop data and new exp time
        $trxData->shop_data = $shopData->data;
        $trxData->confirmation_date = $confDate;
        $trxData->expiration_time = $expFinished;
        $trxData->expiration_finished = $expDate;
        $trxData->status = 'Submitted';

        // put new data
        $newData = [
            'status' => 'Submitted',
            'expiration_time' => $expFinished,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        // cek apakah pembatalan berhasil
        if ($isUpdate) {
            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);

            // return response & send email to merchant
            Mail::to($trxData->user_data->email)->send(new Submitted($trxData));
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil diserahkan', 'data' => $trxData], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyerahkan pesanan'], 400);
        }
    }

    public function finishTrx(Request $request, ShopController $shopController, Shops $shops)
    {

        // validasi data
        $validator = Validator::make($request->all(), [
            'id_shop' => 'required|integer',
            'order_code' => 'required',
        ], [
            'id_shop' => 'Id Toko tidak valid.',
            'order_code' => 'Order code harus diisi.',
        ]);

        // cek validasi
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $idShop = $request->input('id_shop');
        $orderCode = $request->input('order_code');

        $tableTrx = $this->generateTableTrx($idShop);
        $tableDtl = $this->generateTableDtl($idShop);

        // cek toko exist atau tidak
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistShop['message']], 404);
        }

        // cek apakah transaksi exist atau tidak
        $isExistTrx = $this->isExistTrx($idShop, $orderCode);
        if ($isExistTrx['status'] === 'error') {
            return response()->json(['status' => 'success', 'message' => $isExistTrx['message']], 404);
        }

        // get transaction by order code
        $trx = $this->trxDetail($request)->getData();

        // jika transaction gagal didapatkan
        if ($trx->status === 'error') {
            return response()->json(['status' => 'error', 'message' => $trx->message], 400);
        }

        // get data
        $trxData = $trx->data;

        // get expiration finished
        $expTime = $trxData->expiration_time;
        $confDate = Carbon::createFromTimestamp($expTime / 1000);
        $millisNow = Carbon::now()->timestamp * 1000;

        // cek apakah pesanan sudah kadaluarsa atau belum
        // if ($millisNow > $expTime) {
        //     return response()->json(['status' => 'error', 'message' => 'Pesanan telah melewati batas waktu penyerahan (kadaluarsa)'], 400);
        // }

        // add shop data and new exp time
        $trxData->confirmation_date = $confDate;
        $trxData->expiration_time = 0;
        $trxData->status = 'Finished';

        // put new data
        $newData = [
            'status' => 'Finished',
            'expiration_time' => 0,
            'updated_at' => Carbon::now(),
        ];

        // update data transaksi
        $isUpdate = DB::table($tableTrx)
            ->where('order_code', $orderCode)
            ->update($newData);

        if ($isUpdate) {
            // save to user trx
            $this->addToTrx($trxData->id_user, $idShop, $orderCode);

            // get shop contact
            $contactData = $shopController->getContact($request)->getData();
            if ($contactData->status === 'success') {
                // return response & send email to merchant
                Mail::to($contactData->data->email)->send(new Finished($trxData));
            }
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil diselesaikan', 'data' => $trxData], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyelesaikan pesanan'], 400);
        }
    }
}
