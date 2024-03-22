<?php

namespace App\Http\Controllers\Mobile\Auth;


use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class MobileAuthController extends Controller
{
    public function first()
    {
        return response()->json(['status' => 'success', 'message' => 'test response'], 200);
    }

    public function isExistEmail(Request $request)
    {
        $email = $request->input("email");

        $isExist = User::select("email")->where('email', '=', $email)->limit(1)->exists();

        if ($isExist) {
            return response()->json(['status' => 'success', 'message' => 'Email terdaftar'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 400);
        }
    }

    public function isExistPhone(Request $request)
    {
        $phone = $request->input("phone_number");

        $isExist = User::select("phone_number")->where('phone_number', '=', $phone)->limit(1)->exists();

        if ($isExist) {
            return response()->json(['status' => 'success', 'message' => 'Nomor telephone terdaftar'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Nomor telephone tidak terdaftar'], 400);
        }
    }

    public function isOnLogin(Request $request)
    {
        $email = $request->input('email');

        $isExist = RefreshToken::select('email')->where('email', '=', $email, 'and', 'device', '=', 'mobile')->limit(1)->exists();

        if ($isExist) {
            return response()->json(['status' => 'success', 'message' => 'Akun sedang login'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Akun belum login'], 400);
        }
    }

    private function createTableCart($tableName)
    {
        // create table cart
        Schema::dropIfExists($tableName);
        Schema::create($tableName, function (Blueprint $table) {
            $table->id('id_cart');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_shop');
            $table->integer('id_product');
            $table->smallInteger('quantity');
            $table->integer('price');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')
                ->on('0users')->onDelete('cascade');
            $table->foreign('id_shop')->references('id_shop')
                ->on('0shops')->onDelete('cascade');
        });
    }

    private function createTableTransaction($tableName)
    {
        // create table transaction
        Schema::dropIfExists($tableName);
        Schema::create($tableName, function (Blueprint $table) {
            $table->id('id_trx');
            $table->mediumText('transaction');
            $table->timestamps();
        });
    }

    public function createUser(Request $request, User $user)
    {

        // validasi data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:30',
                'regex:/[^\w\s]/',
            ],
            'full_name' => 'required|string|min:4|max:50',
            'pin' => 'required|string|regex:/^\d{6}$/',
            'phone_number' => 'nullable|string|regex:/^\d{9,15}$/',
        ]);

        // custom message
        $customMsg = [
            'email' => 'Email tidak valid',
            'password' => 'Password tidak valid.',
            'nama' => 'Nama tidak valid.',
            'pin' => 'PIN tidak valid',
            'phone_number' => 'Nomor HP tidak valid.',
        ];
        $validator->setCustomMessages($customMsg);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        // mendapatkan data
        $user->phone_number = $request->input('phone_number');
        $user->email = $request->input('email');
        $user->full_name = $request->input('full_name');
        $user->pin = Hash::make($request->input('pin'));
        $user->password = Hash::make($request->input('password'));
        $user->is_verified = false;

        // menyimpan data
        if ($user->save()) {
            return ['status' => 'success', 'message' => 'Akun berhasil dibuat'];
        } else {
            return ['status' => 'error', 'message' => 'Akun gagal dibuat'];
        }
    }

    public function register(Request $request, User $user)
    {
        $request->input("phone_number");
        $email = $request->input('email');
        $request->input('full_name');
        $request->input('password');
        $request->input('pin');

        // cek email & no hp exist atau tidak
        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);
        $isExistPhone = json_decode($this->isExistPhone($request)->getContent(), true);

        if ($isExistEmail['status'] === 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email sudah terdaftar'], 400);
        } else if ($isExistPhone['status'] === 'success') {
            return response()->json(['status' => 'error', 'message' => 'Nomor HP sudah terdaftar'], 400);
        } else {
            // create akun
            $result = $this->createUser($request, $user);
            if ($result['status'] == 'error') {
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            } else {

                // get user data
                $userData = $user->select('id_user')->where('email', '=', $email)
                    ->limit(1)->first();

                // generate table name
                $tableId = 'us_' . $userData->id_user . '_';
                $tableCart = $tableId . 'cart';
                $tableTrasaction = $tableId . 'trx';

                // create table cart
                $this->createTableCart($tableCart);

                // create table transaction
                $this->createTableTransaction($tableTrasaction);

                return response()->json(['status' => 'success', 'message' => 'Register berhasil'], 200);
            }
        }
    }

    public function signinEmail(Request $request, JwtMobileController $jwtController, RefreshToken $refreshToken)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $request->input('device_token');
        $request->input('device_name');

        // cek email exist atau tidak
        $isExist = json_decode($this->isExistEmail($request)->getContent(), true);

        // jika email tidak exist
        if ($isExist['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 400);
        }

        // get password
        $dbPass = User::select("password")->where('email', $email)->limit(1)->get();

        // jika password tidak cocok
        if (!password_verify($password, $dbPass[0]->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password tidak cocok'], 400);
        }

        // $userData = User::select("*")->where('email', $email)->limit(1)->get();
        // return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);

        // create token
        $token = $jwtController->createJWTMobile($request, $refreshToken);
        if (is_null($token)) {
            return response()->json(['status' => 'error', 'message' => 'create token error'], 400);
        } else {
            if ($token['status'] == 'error') {
                return response()->json(['status' => 'error', 'message' => $token['message']], 400);
            } else {
                return $token;
            }
        }
    }

    public function signinPhone(Request $request, JwtMobileController $jwtController, RefreshToken $refreshToken)
    {
        $phone = $request->input('phone_number');
        $pin = $request->input('pin');
        $request->input('device_token');
        $request->input('device_name');

        // cek nomor hp exist atau tidak
        $isExistPhone = json_decode($this->isExistPhone($request)->getContent(), true);

        // jika nomor hp tidak exist
        if ($isExistPhone['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Nomor Hp tidak terdaftar'], 400);
        }

        // mendapatkan pin
        $dbPin = User::select("pin")->where('phone_number', '=', $phone)->limit(1)->get();

        // jika pin tidak cocok
        if (!password_verify($pin, $dbPin[0]->pin)) {
            return response()->json(['status' => 'error', 'message' => 'PIN tidak cocok'], 400);
        }

        // $userData = User::select('*')->where('phone_number', $phone)->limit(1)->get();
        // return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);

        // get email
        $email = User::select('email')->where('phone_number', '=', $phone)->limit(1)->first();
        $request->merge(['email' => $email->email]);

        // create token
        $token = $jwtController->createJWTMobile($request, $refreshToken);
        if (is_null($token)) {
            return response()->json(['status' => 'error', 'message' => 'create token error'], 400);
        } else {
            if ($token['status'] == 'error') {
                return response()->json(['status' => 'error', 'message' => $token['message']], 400);
            } else {
                return $token;
            }
        }
    }

    public function signinGoogle(Request $request, JwtMobileController $jwtController, RefreshToken $refreshToken)
    {
        $email = $request->input('email');
        $request->input('device_name');
        $request->input('device_token');

        // cek email exist atau tidak
        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);

        // jika email exist
        if ($isExistEmail['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email tersebut belum terdaftar'], 400);
        } else {
            // get user data
            // $userData = User::select("*")->where('email', $email)->limit(1)->get();
            // return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);

            $token = $jwtController->createJWTMobile($request, $refreshToken);
            if (is_null($token)) {
                return response()->json(['status' => 'error', 'message' => 'create token error'], 400);
            } else {
                if ($token['status'] == 'error') {
                    return response()->json(['status' => 'error', 'message' => $token['message']], 400);
                } else {
                    return $token;
                }
            }
        }
    }

    public function changePassword(Request $request, JwtMobileController $jwtController)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:30',
                'regex:/[^\w\s]/',
            ],
        ]);

        // custom message
        $customMsg = [
            'email' => 'Email tidak valid',
            'password' => 'Password tidak valid.',
        ];
        $validator->setCustomMessages($customMsg);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        // get data
        $email = $request->input('email');
        $newPass = $request->input('password');
        $passHash = Hash::make($newPass);

        // cek email exist atau tidak
        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);

        // jika email tidak exist
        if ($isExistEmail['status'] !== 'success') {
            return response()->json(['status' => 'success', 'message' => 'Email tidak terdaftar'], 400);
        } else {
            // mengupdate password
            $update = User::select('email')->where('email', '=', $email)->update(['password' => $passHash]);
            if ($update) {
                // return response()->json(['status' => 'success', 'message' => 'Password berhasil diedit'], 200);

                // update token
                $tokenUpdate = $jwtController->updateRefreshMobile($email);
                if (!is_null($tokenUpdate) && $tokenUpdate['status'] == 'success') {
                    return response()->json(['status' => 'success', 'message' => 'change success'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'gagal update token'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Password gagal diedit'], 400);
            }
        }
    }

    public function changePin(Request $request, JwtMobileController $jwtController)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|regex:/^\d{9,15}$/',
            'pin' => 'required|string|regex:/^\d{6}$/',
        ]);

        // custom message
        $customMsg = [
            'phone_number' => 'Nomor HP tidak valid.',
            'pin' => 'PIN tidak valid',
        ];
        $validator->setCustomMessages($customMsg);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        // get data
        $phone = $request->input('phone_number');
        $newPin = $request->input('pin');
        $pinHash = Hash::make($newPin);

        // cek nomor hp exist atau tidak
        $isExistPhone = json_decode($this->isExistPhone($request)->getContent(), true);

        // jika nomor hp tidak exist
        if ($isExistPhone['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Nomor HP tidak terdaftar'], 400);
        } else {
            // menupdate pin
            $update = User::select('phone_number')->where('phone_number', '=', $phone)->update(['pin' => $pinHash]);
            if ($update) {
                // return response()->json(['status' => 'success', 'message' => 'PIN berhasil diedit'], 200);

                // get email
                $email = User::select('email')->where('phone_number', '=', $phone)->limit(1)->get();

                // update token
                $tokenUpdate = $jwtController->updateRefreshMobile($email[0]->email);
                if (!is_null($tokenUpdate) && $tokenUpdate['status'] == 'success') {
                    return response()->json(['status' => 'success', 'message' => 'change success'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'gagal update token'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'PIN gagal diedit'], 400);
            }
        }
    }

    // Method untuk menyimpan foto profile
    private function savePhotoProfile(Request $request)
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
                $photo->move(public_path('users/'), $fotoProduk);
            } else {
                $isMoved = $photo->move(public_path(base_path('../public_html/public/users/')), $fotoProduk);
                if (!$isMoved) {
                    return ['status' => 'error', 'message' => 'Gagal menyimpan foto profile'];
                }
            }
            // Jika foto produk berhasil disimpan
            return ['status' => 'success', 'message' => 'Foto profile berhasil disimpan', 'data' => ['filename' => $fotoProduk]];
        } else {
            // Jika $photo bukan file yang valid
            return ['status' => 'error', 'message' => 'File foto tidak valid atau tidak ditemukan'];
        }
    }

    public function updatePhotoProfile(Request $request, User $user, JwtMobileController $jwtController)
    {

        // validasi data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'photo' => 'required|file|image|max:512',
        ], [
            'email' => 'Email tidak valid.',
            'photo.required' => 'Foto produk tidak boleh kosong.',
            'photo.max' => 'Ukuran foto produk tidak boleh lebih dari 512 kb',
            'photo.image' => 'File harus berupa gambar.',
        ]);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        $email = $request->input('email');
        $request->file('photo');

        // mendapatkan data foto profile yang lama
        $oldUserData = $user->select('photo')
            ->where('email', $email)
            ->limit(1)->first();

        // get old file name
        $oldFilename = $oldUserData->photo;

        // menghapus foto yang lama
        if ($oldFilename !== 'photo-profile.png') {
            // mendapatkan direktori foto
            $oldPhotoDir = '';
            if (app()->environment('local')) {
                $oldPhotoDir = public_path('users/' . $oldFilename);
            } else {
                $oldPhotoDir = public_path(base_path('../public_html/public/users/')) . $oldFilename;
            }

            // menghapus foto yang lama
            if (File::exists($oldPhotoDir)) {
                File::delete($oldPhotoDir);
            }
        }

        // save foto profile
        $photoSaveResponse = $this->savePhotoProfile($request);

        // jika foto profile berhasil disimpan
        if ($photoSaveResponse['status'] == 'success') {
            // update data profile
            $updateData = $user->where('email', $email)
                ->update([
                    'photo' => $photoSaveResponse['data']['filename'],
                    'updated_at' => Carbon::now(),
                ]);

            // cek data berhasil diupdate atau tidak
            if ($updateData) {
                // return response()->json(['status' => 'success', 'message' => 'Foto berhasil disimpan'], 200);

                // update token
                $tokenUpdate = $jwtController->updateRefreshMobile($email);
                if (!is_null($tokenUpdate) && $tokenUpdate['status'] == 'success') {
                    return response()->json(['status' => 'success', 'message' => 'Foto berhasil disimpan'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'gagal update token'], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Foto gagal disimpan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => $photoSaveResponse['message']], 500);
        }
    }

    public function updateDeviceToken(Request $request, RefreshToken $refreshToken)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        // custom message
        $customMsg = [
            'email' => 'Email tidak valid',
        ];
        $validator->setCustomMessages($customMsg);

        // cek validasi
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->errors()->first()];
        }

        $email = $request->input('email');
        $deviceToken = $request->input('device_token');

        // cek email exist atau tidak
        $isExistEmail = $refreshToken::select('email')
            ->where('email', '=', $email)
            ->limit(1)->exists();

        // jika email tidak exist
        if ($isExistEmail) {
            // update device token
            $update = $refreshToken->select('device_token')
                ->where('email', '=', $email)
                ->update(['device_token' => $deviceToken]);

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'Device token berhasil diupdate'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Device token gagal diupdate'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 400);
        }
    }

    public function logout(Request $request, JWTMobileController $jwtController)
    {
        $email = $request->input('email');
        // hapus token
        $deleted = $jwtController->deleteRefreshMobile($email);
        if ($deleted['status'] == 'error') {
            return response()->json(['status' => 'error', 'message' => 'logout gagal'], 400);
        } else {
            return response()->json(['status' => 'success', 'message' => 'logout berhasil'], 200);
        }
    }

    public function deleteAccount(Request $request, User $user)
    {
        $idUser = $request->input('id_user');

        // generate table name
        $tableId = 'us_' . $idUser . '_';
        $tableCart = $tableId . 'cart';
        $tableTrasaction = $tableId . 'trx';

        // delete account
        $deleteData = $user->where('id_user', '=', $idUser)
            ->limit(1)->delete();

        if ($deleteData) {
            // delete table
            Schema::dropIfExists($tableCart);
            Schema::dropIfExists($tableTrasaction);

            return response()->json(['status' => 'success', 'message' => 'Akun berhasil dihapus'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Akun gagal dihapus'], 400);
        }
    }
}
