<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function createUser(Request $request, User $user)
    {
        $user->phone_number = $request->input('phone_number');
        $user->email = $request->input('email');
        $user->full_name = $request->input('full_name');
        $user->pin = Hash::make($request->input('pin'));
        $user->password = Hash::make($request->input('password'));
        $user->level = "Pembeli";
        $user->is_verified = false;

        if ($user->save()) {
            return ['status' => 'success', 'message' => 'Akun berhasil dibuat'];
        } else {
            return ['status' => 'error', 'message' => 'Akun gagal dibuat'];
        }
    }

    public function register(Request $request, User $user)
    {
        $request->input("phone_number");
        $request->input('email');
        $request->input('full_name');
        $request->input('password');
        $request->input('pin');

        // cek phone registered
        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);

        if ($isExistEmail['status'] === 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email sudah terdaftar'], 400);
        } else {
            $result = $this->createUser($request, $user);
            if ($request['status'] == 'error') {
                return response()->json(['status' => 'error', 'message' => $request['message']], 400);
            } else {
                return $result;
            }
        }
    }

    public function signinEmail(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // cek email 
        $isExist = json_decode($this->isExistEmail($request)->getContent(), true);
        if ($isExist['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 400);
        }

        // cek password
        $dbPass = User::select("password")->where('email', $email)->limit(1)->get();
        if (!password_verify($password, $dbPass[0]->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password tidak cocok'], 400);
        }

        // login success
        $userData = User::select("*")->where('email', $email)->limit(1)->get();
        return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);
    }

    public function signinPhone(Request $request)
    {
        $phone = $request->input('phone_number');
        $pin = $request->input('pin');

        // cek nomor hp
        $isExistPhone = json_decode($this->isExistPhone($request)->getContent(), true);
        if ($isExistPhone['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Nomor Hp tidak terdaftar'], 400);
        }

        // cek pin
        $dbPin = User::select("pin")->where('phone_number', $phone)->limit(1)->get();
        if (!password_verify($pin, $dbPin[0]->pin)) {
            return response()->json(['status' => 'error', 'message' => 'PIN tidak cocok'], 400);
        }

        $userData = User::select('*')->where('phone_number', $phone)->limit(1)->get();
        return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);
    }

    public function signinGoogle(Request $request)
    {
        $email = $request->input('email');

        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);
        if ($isExistEmail['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Email tersebut belum terdaftar'], 400);
        } else {
            $userData = User::select("*")->where('email', $email)->limit(1)->get();
            return response()->json(['status' => 'success', 'message' => 'Login Berhasil', 'data' => $userData[0]], 200);
        }
    }

    public function changePassword(Request $request)
    {
        $email = $request->input('email');
        $newPass = $request->input('password');
        $passHash = Hash::make($newPass);

        $isExistEmail = json_decode($this->isExistEmail($request)->getContent(), true);

        if ($isExistEmail['status'] !== 'success') {
            return response()->json(['status' => 'success', 'message' => 'Email tidak terdaftar'], 400);
        } else {
            $update = User::select('email')->where('email', '=', $email)->update(['password' => $passHash]);
            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'Password berhasil diedit'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Password gagal diedit'], 400);
            }
        }
    }

    public function changePin(Request $request)
    {
        $phone = $request->input('phone_number');
        $newPin = $request->input('pin');
        $pinHash = Hash::make($newPin);

        $isExistPhone = json_decode($this->isExistPhone($request)->getContent(), true);

        if ($isExistPhone['status'] !== 'success') {
            return response()->json(['status' => 'success', 'message' => 'Nomor HP tidak terdaftar'], 400);
        } else {
            $update = User::select('phone_number')->where('phone_number', '=', $phone)->update(['pin' => $pinHash]);
            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'PIN berhasil diedit'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'PIN gagal diedit'], 400);
            }
        }
    }
}
