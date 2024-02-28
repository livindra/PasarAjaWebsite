<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MobileAuthController extends Controller
{
    public function first(Request $request)
    {
        return response()->json(['status' => 'success', 'message' => 'response pertama saya'], 200);
    }

    public function isExistEmail(Request $request){
        $email = $request->input("email");

        $isExist = User::select("email")->where('email','=',$email)->limit(1)->exists();

        if($isExist){
            return response()->json(['status'=>'success', 'message'=>'Email terdaftar'], 200);
        }else{
            return response()->json(['status'=>'error', 'message'=>'Email tidak terdaftar'], 200);
        }
    }

    public function isExistPhone(Request $request){
        $phone = $request->input("phone");

        $isExist = User::select("phone_number")->where('phone_number','=',$phone)->limit(1)->exists();

        if($isExist){
            return response()->json(['status'=>'success', 'message'=>'Nomor telephone terdaftar'], 200);
        }else{
            return response()->json(['status'=>'error', 'message'=>'Nomor telephone tidak terdaftar'], 200);
        }
    }

}
