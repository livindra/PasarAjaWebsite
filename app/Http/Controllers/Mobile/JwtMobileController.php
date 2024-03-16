<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use UnexpectedValueException;

class JwtMobileController extends Controller
{

    public function checkTotalLoginMobile($data)
    {
        $email = $data['email'];
        if (empty($email) || is_null($email)) {
            return ['status' => 'error', 'message' => 'email empty'];
        } else {
            // cek apakah email exist atau tidak
            $isExist = RefreshToken::select('email')->where('email', '=', $email, 'and', 'device', '=', 'mobile')->limit(1)->exists();

            // jika sudah login
            if ($isExist) {
                // count total login
                $Iresult = RefreshToken::where('email', '=', $email, 'and', 'device', '=', 'mobile')->count();
                $result = json_decode(json_encode($Iresult));
                if (is_null($result) || empty($result) || $result <= 0) {
                    return ['status' => 'success', 'data' => 0];
                } else {
                    return ['status' => 'success', 'data' => $result];
                }
            } else {
                return ['status' => 'error', 'message' => 'belum login', 'data' => 0];
            }
        }
    }

    public function generateToken($email, RefreshToken $refreshToken)
    {
        $isExist = User::select('email')->where('email', '=', $email)->limit(1)->exists();
        if ($isExist) {
            // mendapatkan data data
            $dataDb = User::select()->where('email', '=', $email)->limit(1)->get();
            $data = json_decode(json_encode($dataDb));
            // kalkulasi expiration time
            $exp = time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED'));
            $expRefresh = time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED'));
            // prepare payload
            $payload = [$data, 'number' => 1, 'exp' => $exp];
            $payloadRefresh = ['data' => $data, 'exp' => $expRefresh];
            // get secret key
            $secretKey = env('JWT_SECRET_MOBILE');
            $secretRefreshKey = env('JWT_SECRET_REFRESH_TOKEN_MOBILE');
            // generate jwt
            $token = JWT::encode($payload, $secretKey, 'HS512');
            $rToken = JWT::encode($payloadRefresh, $secretRefreshKey, 'HS512');
            // add properties
            $refreshToken->email = $email;
            $refreshToken->token = $rToken;
            $refreshToken->device = 'mobile';
            $refreshToken->number = 1;

            return ['status' => 'success', 'access_token' => $token, 'refresh_token' => $rToken];
        } else {
            return ['status' => 'error', 'message' => 'email e ga onok'];
        }
    }

    public function createJWTMobile($email, RefreshToken $refreshToken)
    {
        try {
            if (empty($email) || is_null($email)) {
                return ['status' => 'error', 'message' => 'email empty'];
            } else {
                // get total login
                $number = $this->checkTotalLoginMobile(['email' => $email]);
                // jika user sudah login
                if ($number['data'] >= 1) {
                    echo 'user on logged';

                    // generate new token
                    $tokens = $this->generateToken($email, $refreshToken);
                    if ($tokens['status'] === 'success') {
                        $rToken = $tokens['refresh_token'];
                    } else {
                        return ['status' => 'error', 'message' => $tokens['message']];
                    }

                    // update token
                    if ($refreshToken->where('email', '=', $email, 'and', 'device', '=', 'mobile')->update(['token' => $rToken])) {
                        return ['status' => 'success', 'data' => json_decode(json_encode($rToken), true), 'number' => 1];
                    } else {
                        return ['status' => 'error', 'message' => 'error saving token'];
                    }
                }
                // jika user belum login
                else {
                    echo 'user new logged';

                    // generate new token
                    $tokens = $this->generateToken($email, $refreshToken);
                    if ($tokens['status'] === 'success') {
                        $accessToken = $tokens['access_token'];
                    } else {
                        return ['status' => 'error', 'message' => $tokens['message']];
                    }

                    // save data
                    if ($refreshToken->save()) {
                        return ['status' => 'success', 'data' => json_decode(json_encode($accessToken), true)];
                    } else {
                        return ['status' => 'error', 'message' => 'error saving token'];
                    }
                }
            }
        } catch (UnexpectedValueException  $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateRefreshMobile(Request $request)
    {
        try {
            $email = $request->input('email');

            $isExist = User::select('email')->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->exists();
            if ($isExist) {
                $dataDb = User::select()->whereRaw("BINARY email LIKE '%" . $email . "%'")->limit(1)->get();
                $data = json_decode(json_encode($dataDb));
                $expRefresh = time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED'));
                $payloadRefresh = [$data, 'exp' => $expRefresh];
                $secretRefreshKey = env('JWT_SECRET_REFRESH_TOKEN_MOBILE');
                $token = JWT::encode($payloadRefresh, $secretRefreshKey, 'HS512');

                $update = DB::table('refresh_token')->whereRaw("BINARY email LIKE '%" . $email . "%' AND device = 'mobile'")
                    ->update(['token' => $token]);
                if (is_null($update)) {
                    return ['status' => 'error', 'message' => 'error update refresh token'];
                } else {
                    return ['status' => 'success', 'message' => 'success update refresh token'];
                }
            } else {
                return ['status' => 'error', 'message' => 'email tersebut tidak terdaftar'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteRefreshMobile($email)
    {
        try {
            if (empty($email) || is_null($email)) {
                return ['status' => 'error', 'message' => 'email empty', 'code' => 404];
            } else {
                $deleted = DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND device = 'mobile'")
                    ->delete();
                if ($deleted) {
                    return ['status' => 'success', 'message' => 'success delete refresh token', 'code' => 200];
                } else {
                    return ['status' => 'error', 'message' => 'failed delete refresh token', 'code' => 500];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
