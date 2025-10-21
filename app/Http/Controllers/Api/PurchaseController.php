<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PurchaseRequest;
use App\Models\Purchase;
use App\Services\PortmoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PurchaseController extends Controller
{
    private $portmoneService;

    public function __construct(PortmoneService $portmoneService)
    {
        $this->portmoneService = $portmoneService;
    }

    public function checkPayment(PurchaseRequest $request)
    {
        $purchase = Purchase::create($request->validated());

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://www.portmone.com.ua/r3/api/check', [
            'payee_id' => env('PORTMONE_MERCHANT_ID'),
            'login' => env('PORTMONE_LOGIN'),
            'password' => env('PORTMONE_PASSWORD'),
            'order_id' => $purchase->payment_id,
        ]);

        $data = $response->json();
        $status = $data['orders'][0]['status'] ?? 'unknown';
        if ($status === 'PAYED') {
            auth()->user()->books()->syncWithoutDetaching($purchase->item_id);

            return response([
                'data' =>[
                    'message' => 'Book added succesfuly!',
                    'result' => true,
            ]]);
        }
        return response([
            'data' =>[
                'message' => 'Payment failed',
                'result' => false,
        ]]);
    }
}
