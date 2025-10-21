<?php

namespace App\Http\Controllers\Api;

use App\Actions\PublicNotificationUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MessageRequest;
use App\Http\Requests\Api\MessageUpdateRequest;
use App\Http\Resources\Api\MessageResource;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($order)
    {
        $order = Order::where('id', $order)->first();

        if(!$order){
            return response()->json(array(
                'data' => [
                'code'      =>  404,
                'message'   =>  'Order not found',
                ]), 404);
        }

        $order->loadMissing('messages.user');

        return response([
            'data' => [
                'chat' => MessageResource::collection($order->messages),
                'result' => true,
            ]]);
    }

    public function create(MessageRequest $request, $order)
    {
        $order = Order::where('id', $order)->with('user','seamstress')->first();

        if(!$order){
            return response()->json(array(
                'data' => [
                'code'      =>  404,
                'message'   =>  'Order not found',
                ]), 404);
        }

        $message = $order->messages()->create([
            'user_id' => auth()->user()->id,
            'text' => $request->text,
            'is_read' => false,
        ]);

        if($request->images){
            foreach($request->images as $image)
            {
                $message->addMedia($image)->toMediaCollection('images', 'messages');
            }
        }
        $action = new PublicNotificationUserAction();
        $seamstress = $order->seamstress;
        $user = $order->user;
        if(auth()->user()->id == $order->user_id){
            $user_name = $order->user->name . ' ' . $order->user->last_name;
            $action->handle('Нове повідомлення', $user_name . ' ' . 'надіслав вам приватне повідомлення. Перегляньте його в застосунку', $seamstress);
        } else {
            $seamstress_name = $order->seamstress->name . ' ' . $order->seamstress->last_name;
            $action->handle('Нове повідомлення', $seamstress_name . ' ' . 'надіслав вам приватне повідомлення. Перегляньте його в застосунку', $user);
        }

        return response([
            'data' => [
                'message' => MessageResource::make($message),
                'result' => true,
            ]]);
    }

    public function update($message)
    {
        $message = Message::where('id', $message)->first();

        if(!$message){
            return response()->json(array(
                'data' => [
                'result' => false,
                'code'      =>  404,
                'message'   =>  'Order not found',
                ]), 404);
        }
        $message = $message->markAsRead();

        return response([
            'data' => [
                'message' => MessageResource::make($message),
                'result' => true,
            ]]);
    }
}
