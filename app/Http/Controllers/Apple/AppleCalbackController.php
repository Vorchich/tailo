<?php

namespace App\Http\Controllers\Apple;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppleCalbackController extends Controller
{
    public function call_back(Request $request){
        list($header, $payload, $signature) = explode('.', $request->signedPayload);

        $decodedHeader = json_decode(base64_decode($header), true);
        $decodedPayload = json_decode(base64_decode($payload), true);

        $decodedHeader = $this->decodeJwtPart($header);
        $decodedPayload = $this->decodeJwtPart($payload);

        $signedTransactionInfo = $decodedPayload['data']['signedTransactionInfo'];
        $signedRenewalInfo = $decodedPayload['data']['signedRenewalInfo'];

        $transactionInfo = $this->decodeJwt($signedTransactionInfo);
        $renewalInfo = $this->decodeJwt($signedRenewalInfo);

        $user = User::where('apple_transaction_id', $transactionInfo['originalTransactionId'])->first();

        $notificationType = $transactionInfo['transactionReason'];
        // dd($transactionInfo,$renewalInfo);
        switch ($notificationType) {
            case 'RENEWAL':
            case 'DID_RENEW': // Підписка подовжена
            case 'RENEWAL_EXTENDED': // Apple продовжив підписку
                if(!$user){
                    \Log::info('New purchase detected: ' , [
                        'transaction_info' => $transactionInfo,
                        'renewal_info' => $renewalInfo
                    ]);

                    return response([
                        'status' => false,
                    ],404);
                }
                $expiresDate = Carbon::createFromTimestampMs($transactionInfo['expiresDate']);
                $user->update([
                    'apple_expires_date' => $expiresDate,
                    'apple_is_subscribe' => true
                ]);
                break;
            case 'SUBSCRIBED': // Нова підписка
                if(!$user){
                    \Log::info('New purchase detected: ' , [
                        'transaction_info' => $transactionInfo,
                        'renewal_info' => $renewalInfo
                    ]);
                    return response([
                        'status' => false,
                    ],404);
                }
                $expiresDate = Carbon::createFromTimestampMs($transactionInfo['expiresDate']);
                $user->update([
                    'apple_expires_date' => $expiresDate,
                    'apple_is_subscribe' => true
                ]);
                break;
            case 'EXPIRED': // Закінчення підписки
            case 'CANCEL': // Користувач скасував підписку
            case 'DID_REVOKE': // Apple скасував підписку
            case 'GRACE_PERIOD_EXPIRED': // Закінчився льготний період
                if(!$user){
                    \Log::info('New purchase detected: ' , [
                        'transaction_info' => $transactionInfo,
                        'renewal_info' => $renewalInfo
                    ]);
                    return response([
                        'status' => false,
                    ],404);
                }
                $expiresDate = Carbon::createFromTimestampMs($transactionInfo['expiresDate']);
                $user->update([
                    'apple_expires_date' => $expiresDate,
                    'apple_is_subscribe' => false
                ]);
                break;
            case 'CONSUMPTION_REQUEST': //Користувач зробив запит на повернення коштів
            case 'REFUND': // Користувачу повернули кошти
                if(!$user){
                    \Log::info('New purchase detected: ' , [
                        'transaction_info' => $transactionInfo,
                        'renewal_info' => $renewalInfo
                    ]);

                    return response([
                        'status' => false,
                    ],404);
                }
                $user->update([
                    'apple_expires_date' => now(),
                    'apple_is_subscribe' => false
                ]);
                break;
            case 'GRACE_PERIOD_EXPIRED':
                break;
            case 'PURCHASED':
                if(!$user){
                    \Log::info('New purchase detected: ' , [
                        'transaction_info' => $transactionInfo,
                        'renewal_info' => $renewalInfo
                    ]);
                }
                break;
        }
        // dd($user);
        if(!$user){
            \Log::info('New purchase detected: ' , [
                'transaction_info' => $transactionInfo,
                'renewal_info' => $renewalInfo
            ]);

            return response([
                'status' => false,
            ],404);
        }
        $user->save();
        // dd($decodedHeader, $decodedPayload, $transactionInfo, $renewalInfo);
        // dd($user);
        return true;

    }

    private function decodeJwtPart($part) {
        $remainder = strlen($part) % 4;
        if ($remainder) {
            $part .= str_repeat('=', 4 - $remainder); // Додаємо "=" для коректного декодування
        }
        return json_decode(base64_decode(strtr($part, '-_', '+/')), true);
    }

    private function decodeJwt($jwt) {
        list($header, $payload, $signature) = explode('.', $jwt);

        // Декодуємо JSON з payload (без перевірки підпису)
        $decodedPayload = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        return $decodedPayload;
    }
}
