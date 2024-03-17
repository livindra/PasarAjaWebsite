<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Mail\ForgotVerify;
use App\Mail\RegisterVerify;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerifyController extends Controller
{

    public function sendOTP($verification)
    {
        if ($verification->type === 'Register') {
            // send otp untuk register
            Mail::to($verification->email)->send(new RegisterVerify($verification));
            return ['status' => 'success', 'message' => 'OTP terkirim'];
        } else if ($verification->type === 'Forgot') {
            // send otp untuk lupa password
            Mail::to($verification->email)->send(new ForgotVerify($verification));
            return ['status' => 'success', 'message' => 'OTP terkirim'];
        } else {
            return ['status' => 'error', 'message' => 'Unknown OTP Type'];
        }
    }

    public function verify(Request $request, Verification $verification)
    {

        // validasi data
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'type' => 'required|in:Register,Forgot'
            ],
            [
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'type.required' => 'Tipe harus diisi.',
                'type.in' => "Tipe harus berupa 'register' atau 'forgot'."
            ],
        );

        // jika data tidak valid
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $email = $request->input('email');
        $type = $request->input('type');

        // get date now
        $dateNow = Carbon::now()->toDateString();

        // cek email exist atau tidak
        $isExistOtp = Verification::select('email')
            ->where('email', '=', $email)
            ->where(DB::raw('DATE(created_at)'), '=', $dateNow)
            ->where('type', '=', $type)
            ->limit(1)->exists();

        // jika user sudah mengirim otp sebelumnya pada hari ini
        if ($isExistOtp) {

            // get old otp data
            $oldOtpData = $verification
                ->select(['expiration_time', DB::raw('DATE(created_at) as tanggal'), 'number', 'type'])
                ->where('email', '=', $email)
                ->where(DB::raw('DATE(created_at)'), '=', $dateNow)
                ->where('type', '=', $type)
                ->limit(1)->first();

            // jika user sudah terlalu sering mengirim otp dalam 1 hari
            if ($oldOtpData && $oldOtpData->number >= 10) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah terlalu sering menggirim OTP pada hari ini'], 400);
            } else {
                // random otp code, endmillis & update number
                $otpCode = strval(rand(1000, 9999));
                $expTime = (round(microtime(true) * 1000) + 900000);
                $number = ($oldOtpData) ? (++$oldOtpData->number) : 0;

                // put data
                $newData = [
                    'otp' => $otpCode,
                    'expiration_time' => $expTime,
                    'number' => $number,
                ];

                // update data
                $isUpdate = $verification
                    ->where('email', '=', $email)
                    ->where(DB::raw('DATE(created_at)'), '=', $dateNow)
                    ->where('type', '=', $type)
                    ->update($newData);

                // jika proses update berhasil
                if (!is_null($isUpdate)) {

                    // get data otp
                    $data = Verification::select('*')
                        ->where('email', '=', $email)
                        ->where(DB::raw('DATE(created_at)'), '=', $dateNow)
                        ->where('type', '=', $type)
                        ->limit(1)->first();

                    // kirim otp
                    $this->sendOTP($data);

                    return response()->json(['status' => 'success', 'message' => 'Kode OTP berhasil diupdate', 'data' => $data], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Kode OTP gagal diupdate'], 400);
                }
            }
        } else {
            // random otp code, endmillis & update number
            $otpCode = strval(rand(1000, 9999));
            $expTime = (round(microtime(true) * 1000) + 900000);
            $number = 1;

            // put data to model
            $verification->email = $email;
            $verification->type = $type;
            $verification->otp = $otpCode;
            $verification->expiration_time = $expTime;
            $verification->number = $number;

            // menyimpan data
            if ($verification->save()) {
                // get data otp
                $data = Verification::select('*')
                    ->where('email', '=', $email)
                    ->where(DB::raw('DATE(created_at)'), '=', $dateNow)
                    ->where('type', '=', $type)
                    ->limit(1)->first();

                // kirim otp
                $this->sendOTP($data);

                return response()->json(['status' => 'success', 'message' => 'Kode OTP berhasil dibuat', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Kode OTP gagal dibuat'], 400);
            }
        }
    }

    public function verifyByPhone(Request $request, Verification $verification)
    {
        // validasi data
        $validator = Validator::make(
            $request->all(),
            [
                'phone_number' => 'required',
                'type' => 'required|in:Register,Forgot'
            ],
            [
                'phone_number.required' => 'Nomor HP harus diisi.',
                'type.required' => 'Tipe harus diisi.',
                'type.in' => "Tipe harus berupa 'register' atau 'forgot'."
            ],
        );

        // jika data tidak valid
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $phone = $request->input('phone_number');
        $request->input('type');

        // get email by phone number
        $email = User::select('email')->where('phone_number', '=', $phone)->limit(1)->first();
        $request->merge(['email' => $email->email]);
        // echo $email;

        if (is_null($email)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak dapat menemukan alamat email Anda'], 400);
        } else {
            // send otp
            return $this->verify($request, $verification);
        }
    }
}
