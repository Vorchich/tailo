<?php

namespace App\Http\Controllers\Apple;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Apple\AppleSubscribeValedateRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;

class AppleController extends Controller
{
    public function subscribe_validate(AppleSubscribeValedateRequest $request){

        $user = Auth::user();
        if($user->skip_subscription_check_until > now()) {
            return response()->json(['status' => true, 'expiry_date' => $user->apple_expires_date, 'user' => ProfileResource::make($user),]);
        }
        $result = $this->validateReceipt($request->receipt, env('APPLE_PRODUCTION_URL'));
        // Якщо у production-перевірці статус 21007 — це тестовий purchase, перенаправляємо запит у sandbox
        if (isset($result['status']) && $result['status'] == 21007) {
            $result = $this->validateReceipt($request->receipt, env('APPLE_SANDBOX_URL'));
        }
        if (!isset($result['latest_receipt_info'])) {
            return response()->json(['status' => false,'expiry_date' => null, 'user' => ProfileResource::make($user),],403);
        }
        $latest_receipt_info = $result['latest_receipt_info'] ?? [];
        $is_active = false;
        $expiry_date = null;
        foreach ($latest_receipt_info as $receipt) {
            if (isset($receipt['purchase_date'])) {
                $expiry_date = date("Y-m-d H:i:s", $receipt['expires_date_ms'] / 1000);
                if ($receipt['expires_date_ms'] > time() * 1000) {
                    $is_active = true;
                    break;
                }
            }
        }
        $subscripe = User::where('apple_transaction_id', $receipt['transaction_id'])->first();
        if ($subscripe) {
            if ($user->id !== $subscripe->id){
                return response()->json(['status' => $is_active,'expiry_date' => $expiry_date, 'user' => ProfileResource::make($user),],403);
            }
        }
        $user->apple_expires_date = $expiry_date;
        $user->apple_is_subscribe = $is_active;
        $user->apple_transaction_id = $receipt['transaction_id'];
        $user->save();
        return response()->json(['status' => $is_active,'expiry_date' => $expiry_date, 'user' => ProfileResource::make($user)]);

    }


    private function validateReceipt($receipt_data, $url) {
        $post_data = json_encode([
            "receipt-data" => $receipt_data,
            "password" => env('APPLE_SHARED_SECRET'),
            "exclude-old-transactions" => true
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function is_subscribe(){
        $user = Auth::user();
        if ($user->apple_expires_date < now()) {
            $user->apple_is_subscribe = false;
            $user->save();
        }
        return response()->json([
            'status' => $user->apple_is_subscribe,
            'expiry_date' => \Carbon\Carbon::parse($user->apple_expires_date)->format('Y-m-d H:i'),
            'user' => ProfileResource::make($user),
        ]);
    }
}
