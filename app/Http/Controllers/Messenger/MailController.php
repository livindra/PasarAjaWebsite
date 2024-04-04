<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerify;
use App\Mail\MailMessage;
use App\Mail\OrderRequest;
use App\Models\User;
use App\Models\Verification;

class MailController extends Controller
{
    public function sendEmail()
    {
        $data = [
            'subject' => 'Kode Verifikasi Anda',
            'body' => 'Ini adalah kode verifikasi Anda'
        ];

        try {
            Mail::to('arenafinder.app@gmail.com')->send(new MailMessage($data));
            return response()->json(['status' => 'success', 'message' => 'email terkirim']);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'message' => "email gagal terkirim" . $data]);
        }
    }

    public function sendOtpCode(Request $request, Verification $ver)
    {
        $email = $request->input('email');
        $type = $request->input('type');

        // random code
        $random_number = rand(1000, 9999);

        // Mengatur properti objek $ver
        $ver->email = $request->input('email');
        $ver->otp = $random_number; // Memasukkan kode OTP yang dihasilkan
        $ver->type = "Forgot";
        $ver->expiration_time = "1";

        // Menyimpan data pada basis data
        if ($ver->save()) {
            // Data berhasil disimpan
            $data = [
                'subject' => $random_number . ' adalah kode otp anda',
                'body' => 'kode otp anda ' . $random_number,
            ];

            // Mengirim email
            Mail::to($email)->send(new MailMessage($data));

            $dataOtp = [
                'id_verification' => '2',
                'email' => $email,
                'otp' => "$random_number",
                'type' => $type,
                'expiration_time' => 1709492,
                "created_at" => "2024-03-02T18:26:12.000000Z",
                "updated_at" => "2024-03-03T18:39:41.000000Z"
            ];

            return response()->json(['status' => 'success', 'message' => 'Kode otp berhasil terkirim', 'data' => $dataOtp], 200);
        } else {
            // Gagal menyimpan data
            return response()->json(['status' => 'error', 'message' => 'Akun gagal dibuat'], 400);
        }
    }

    public function sendOtpCodeByPhone(Request $request, Verification $ver, User $user)
    {
        $phone = $request->input('phone');
        $type = $request->input('type');

        // random code
        $random_number = rand(1000, 9999);

        $isExist = User::select("phone_number")->where('phone_number', '=', $phone)->limit(1)->exists();

        if ($isExist) {
            $email = User::select("email")->where('phone_number', $phone)->limit(1)->get();

            if (!empty($email)) {
                $emailValue = $email[0]['email'];
            }

            // Mengatur properti objek $ver
            $ver->email = $emailValue;
            $ver->otp = $random_number; // Memasukkan kode OTP yang dihasilkan
            $ver->type = "Forgot";
            $ver->expiration_time = "1";

            // Menyimpan data pada basis data
            if ($ver->save()) {
                // Data berhasil disimpan
                $data = [
                    'subject' => $random_number . ' adalah kode otp anda',
                    'body' => 'kode otp anda ' . $random_number,
                ];

                // Mengirim email
                Mail::to($emailValue)->send(new MailMessage($data));

                $dataOtp = [
                    'id_verification' => '2',
                    'email' => $emailValue,
                    'otp' => "$random_number",
                    'type' => $type,
                    'expiration_time' => 1709492,
                    "created_at" => "2024-03-02T18:26:12.000000Z",
                    "updated_at" => "2024-03-03T18:39:41.000000Z"
                ];

                return response()->json(['status' => 'success', 'message' => 'Kode otp berhasil terkirim', 'data' => $dataOtp], 200);
            } else {
                // Gagal menyimpan data
                return response()->json(['status' => 'error', 'message' => 'Akun gagal dibuat'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Nomor HP tidak terdaftar'], 400);
        }
    }


    public function sendOrderRequest($email, $orderData)
    {
        // Mengirim email
        Mail::to($email)->send(new OrderRequest($orderData));

        return response()->json(['status' => 'success', 'message' => 'Email berhasil terkirim'], 200);
    }
}
