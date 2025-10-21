<?php

namespace App\Http\Controllers\Api;

use App\Actions\PublicNotificationAction;
use App\Actions\PublicNotificationUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Requests\Api\OrderStatusRequest;
use App\Http\Requests\Api\OrederReviewRequest;
use App\Http\Requests\Api\PreOrderConfirmRequest;
use App\Http\Requests\Api\PreOrderRequest;
use App\Http\Requests\Api\SubmitOrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\ReviewResource;
use App\Models\Category;
use App\Models\Notepad;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show($order)
    {
        $order = Order::where('id', $order)->with('notepad')->first();

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
                'order' => OrderResource::make($order),
                'result' => true,
            ]]);
    }


    public function create(OrderRequest $request, $seamstress)
    {
        // $sizes = Category::find($request->category_id)->with('sizes')->first()->sizes->pluck('id');
        $seamstress = User::find($seamstress);

        if(!($seamstress && $seamstress->is_seamstress))
        {
            return response([
                'data' => ["message" => "The seamstress could not be found.",
                'result' => false,
            ]],404);
        }

        $data = $request->validated();
        $order = $seamstress->seamstressOrders()->create([
            'user_id' => auth()->user()->id,
            'category_id' => $data['category_id'],
            'comment' => $data['comment'] ?? null,
        ]);
        if($request->comment)
        {
            $order->messages()->create([
                'user_id' => auth()->user()->id,
                'text' => $request->comment,
                'is_read' => false,
            ]);
        }

        $measurements = $data['measurements'] ?? [];
        foreach($measurements as $key => $measurement)
        {
            $a[$key] = ['value' => $measurement];
        }

        auth()->user()->activities()->create([
            'name'  => 'Замовлення створено',
        ]);
        $notepad = $seamstress->notepads()->create([
            'order_id' => $order->id,
            'name' => 'Робоча область ' . $order->id,
        ]);

        $order->sizes()->sync($a);
        $notepad->sizes()->sync($a);

        $user = auth()->user();

        $user_name = $user->name . ' ' . $user->last_name;
        $action = new PublicNotificationUserAction();

        $action->handle(__('order-title'), $user_name . ' ' . __('order-text'), $user);

        return response([
            'data' => [
                'order' => OrderResource::make($order),
                'result' => true,
            ]]);
    }

    public function statusChange(OrderStatusRequest $request, $order)
    {
        $seamstress = auth()->user();
        $order = Order::where('id', $order)
            // ->where('seamstress_id', auth()->user()->id)
            ->first();

        if(!$order)
        {
            return response([
                'data' => ["message" => "The order could not be found.",
                'result' => false,
            ]],404);
        }

        $customer = User::findOrFail('id', $order->user_id);

        $order->update([
            'status' => $request->status,
        ]);

        $action = new PublicNotificationUserAction();
        $seamstress_name = $seamstress->name . ' ' . $seamstress->last_name;
        if($request->status == 'failed')
        {
            $action->handle('Оновлення співпраці',  $seamstress_name . ' ' .'відхилив вашу співпрацю', $customer);
        }
        if($request->status == 'in_process')
        {
            $action->handle('Оновлення співпраці',  $seamstress_name . ' ' .'прийняв вашу співпрацю', $customer);
        }
        if($request->status == 'success')
        {
            $action->handle('Оновлення співпраці',  $seamstress_name . ' ' .'завершив вашу співпрацю', $customer);
        }

        return response([
            'data' => [
                'order' => OrderResource::make($order),
                'result' => true,
            ]]);
    }

    public function seamstressSubmit(Order $order)
    {
        if(!($order->seamstress_id == auth()->id()))
        {
            return response([
                'data' => ["message" => "The order could not be found.",
                'result' => false,
            ]],404);
        }

        $order->seamstress_comfirm = true;

        if($order->seamstress_comfirm && $order->customer_comfirm)
        {
            $order->status = 'success';
        }

        $order->save();

        auth()->user()->activities()->create([
            'name'  => 'Ви підтвердили успішне виконання замовлення №' . ' ' . $order->id,
        ]);

        return response([
            'data' => [
                'order' => OrderResource::make($order->fresh()),
                'result' => true,
            ]]);
    }

    public function customerSubmit(Order $order)
    {
        if(!($order->user_id == auth()->id()))
        {
            return response([
                'data' => ["message" => "The order could not be found.",
                'result' => false,
            ]],404);
        }

        $order->customer_comfirm = true;

        if($order->seamstress_comfirm && $order->customer_comfirm)
        {
            $order->status = 'success';
        }

        $order->save();

        auth()->user()->activities()->create([
            'name'  => 'Ви підтвердили успішне виконання замовлення №' . ' ' . $order->id,
        ]);

        return response([
            'data' => [
                'order' => OrderResource::make($order->fresh()),
                'result' => true,
            ]]);
    }

    public function orderReview(OrederReviewRequest $request, $order)
    {
        $order = Order::where('id', $order)
            ->where('user_id', auth()->user()->id)
            ->first();

        if(!$order)
        {
            return response([
                'data' => ["message" => "The seamstress or order could not be found.",
                'result' => false,
            ]],404);
        }

        $order->review()->updateOrCreate([
            'order_id' => $order->id,
        ],[
            'rating' => $request->rating,
            'text' => $request->text,
        ]);

        return response([
            'data' => [
                'review' => ReviewResource::make($order->review),
                'result' => true,
            ]]);
    }

    public function preOrder(PreOrderRequest $request, User $user)
    {
        // $action = new PublicNotificationAction();
        // $action->handle(__('pre-order-title'), __('pre-order-text'));
        $seamstress = auth()->user();

        $preOrder = auth()->user()->seamstressOrders()->create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'status' => 'pre-order',
            'comment' => $request->comment ?? null,
        ]);
        if($request->comment)
        {
            $preOrder->messages()->create([
                'user_id' => auth()->user()->id,
                'text' => $request->comment,
                'is_read' => false,
            ]);
        }

        auth()->user()->activities()->create([
            'name'  => 'Передзамовлення створено',
        ]);

        $user->activities()->create([
            'name'  => 'Отримано передзамовлення',
        ]);

        $preOrder->sizes()->sync($request->sizes);

        $seamstress_name = $seamstress->name . ' ' . $seamstress->last_name;
        $action = new PublicNotificationUserAction();

        $action->handle(__('pre-order-title'), $seamstress_name . ' ' . __('pre-order-text'), $user);

        return response([
            'data' => [
                'order' => OrderResource::make($preOrder),
                'result' => true,
            ]]);
    }

    public function confirm(PreOrderConfirmRequest $request, Order $order)
    {
        $a = [];
        $customer = auth()->user();
        if($order->user_id != auth()->id() || $order->status != 'pre-order')
        {
            return response([
                'data' => ["message" => "Order not found!",
                'result' => false,
            ]], 404);
        }
        $measurements = $request->measurements ?? [];
        foreach($measurements as $key => $measurement)
        {
            $a[$key] = ['value' => $measurement];
        }
        $customer->activities()->create([
            'name'  => 'Замовлення підтверджено',
        ]);

        $notepad = $customer->notepads()->create([
            'order_id' => $order->id,
            'name' => 'Робоча область ' . $order->id,
        ]);
        $order->sizes()->sync($a);

        $order->update([
            'status' => 'in_process'
        ]);
        $notepad->sizes()->sync($a);
        $seamstress = User::findOrFail($order->seamstress_id);
        $customer = $customer;
        $customer_name = $customer->name . ' ' . $customer->last_name;
        $action = new PublicNotificationUserAction();

        $action->handle('Оновлення співпраці', $customer_name . ' ' . 'прийняв вашу співпрацю', $seamstress);

        return response([
            'data' => [
                'order' => OrderResource::make($order->fresh()),
                'result' => true,
            ]]);
    }
}
