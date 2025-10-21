<?php
namespace App\Actions;

use App\Models\Achivment\Achivment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Achivment\CompleteAchivment;
use App\Models\Notification\NotificationOn;
use Illuminate\Support\Facades\File;

class PublicNotificationUserAction
{
    public function handle($title,$body,$user)
    {
        $this->sendNotification($title,$body,$user);
    }

    protected function sendNotification($title,$body,$user)
    {
        if(!$user->firebase_token){
            return;
        }

        $path = storage_path('firebase/auth_credetelians.json');
        $credentialsJson = File::get($path);

        $credentialsArray = json_decode($credentialsJson, true);
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsArray);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $apiurl = 'https://fcm.googleapis.com/v1/projects/tailor-8f5cd/messages:send';
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];
        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];
        $notification = [
            "title" => $title,
            "body" => $body,
        ];
        $data = [
            "token" => $user->firebase_token,
            "notification" => $notification,
        ];

        $payload['message'] = $data;

        $payload = json_encode($payload);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_exec($ch);
        $res = curl_close($ch);

    }
}
