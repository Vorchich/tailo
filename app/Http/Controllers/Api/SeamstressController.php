<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddFilesRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class SeamstressController extends Controller
{
    public function index()
    {
        $seamstress = User::where('role', 'seamstress')->where('permission', '!=', 'admin')->with('reviews')->get();

        return response([
            'data' => [
                'seamstress' => ProfileResource::collection($seamstress),
                'result' => true,
            ]]);
    }

    public function customers()
    {
        $customers = User::where('role', 'customer')->where('permission', '!=', 'admin')->get();

        return response([
            'data' => [
                'customers' => ProfileResource::collection($customers),
                'result' => true,
            ]]);
    }

    public function addImages(AddFilesRequest $request)
    {
        $seamstress = auth()->user();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $seamstress->addMedia($file)->toMediaCollection('portfolio', 'portfolio');
            }
        }

        return response([
            'data' => [
                'profile' => ProfileResource::make($seamstress),
                'result' => true,
            ]]);
    }

    public function deletePortfolio($portfolio)
    {
        $file = auth()->user()->getMedia('portfolio')->firstWhere('id', $portfolio);

        if (!$file) {
            abort(404, 'File not found!');
        }
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
            'result' => true,
        ]);
    }

    public function statuses()
    {
        return response([
            'data' => [
                'statuses' => Order::getStatuses(),
                'result' => true,
            ]]);
    }

    public function orders(Request $request, $seamstress)
    {
        $seamstress = User::where('id', $seamstress)->first();

        if(!$seamstress && !$seamstress?->is_seamstress){
            return response([
                'data' => ["message" => "The seamstress could not be found.",
                'result' => false,
            ]], 404);
        }

        $seamstress->loadMissing('seamstressOrders');
        if(in_array($request->status, array_keys(Order::getStatuses()))){
            $seamstressOrders = $seamstress->seamstressOrders->where('status', $request->status);
        } else {
            $seamstressOrders = $seamstress->seamstressOrders;
        }

        return response([
            'data' =>[
                'orders' => OrderResource::collection($seamstressOrders),
                'result' => true,
        ]]);
    }

    public function order($seamstress, $order)
    {
        $seamstress = User::where('id', $seamstress)->first();
        $order = Order::where('id', $order)->with('notepad')->first();
        if(!$seamstress && !$seamstress?->is_seamstress){
            return response([
                'data' => ["message" => "The seamstress could not be found.",
                'result' => false,
            ]], 404);
        }

        if(!$order || ($order?->seamstress_id !== $seamstress?->id)){
            return response([
                'data' => ["message" => "The order could not be found.",
                'result' => false,
            ]], 404);
        }

        return response([
            'data' =>[
                'orders' => OrderResource::make($order),
                'result' => true,
                ]]);
    }

}
