<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    // public function sendNotificationrToUser(Request $request)
    // {
    //     $token = $request->input('token');
    //     // get a user to get the fcm_token that already sent.               from mobile apps 
    //     // $user = User::findOrFail($id);

    //     FCMService::send(
    //         $token,
    //         [
    //             'title' => 'anjay',
    //             'body' => 'vang',
    //         ]
    //     );
    // }

    public function sendNotification(Request $request)
    {
        // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        $registrationIds = explode(',', $request->input('token'));
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $registrationIds,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        echo $response;
        // return back()->with('success', 'Notification send successfully.');
    }
}
