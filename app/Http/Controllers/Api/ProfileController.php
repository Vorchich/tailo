<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddBookRequest;
use App\Http\Requests\Api\ProfileRequest;
use App\Http\Requests\Api\SetFirebaseTokenRequest;
use App\Http\Requests\Api\SwitchRoleRequest;
use App\Http\Resources\Api\ActivitiesResource;
use App\Http\Resources\Api\BookResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\ProfileResource;
use App\Models\Order;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileController extends Controller
{
    public function profile()
    {
        return response([
            'data' => [
                'profile' => ProfileResource::make(auth()->user()),
                'result' => true,
            ],
        ]);
    }

    public function activities()
    {
        $user = auth()->user()->loadMissing('activities');

        return response()->json([
            'data' => [
                'activities' => ActivitiesResource::collection($user->activities->sortByDesc('created_at')),
                'result' => true,
        ]]);
    }

    public function edit(ProfileRequest $request)
    {
        $profile = auth()->user();
        // dd($request->validated());
        $profile->update(
            $request->validated()
        );
        // dd($profile->fresh());
        if($request->has('role_seamstress'))
        {
            $profile->update(['is_seamstress' => $request->role_seamstress]);
        }

        if($request->has('role_customer'))
        {
            $profile->update(['is_customer' => $request->role_customer]);
        }

        if($request->image)
        {
            $profile->clearMediaCollection('image');
            $profile->addMedia($request->image)
                ->toMediaCollection('image', 'profile');
        }
        if($request->has('email')){
            $profile->forceFill([
                'email_verified_at' => null,
            ])->save();
        }

        $profile->activities()->create([
            'name'  => 'Дані профілю оновлено',
        ]);

        $profile->refresh();

        return response([
                'data' => [
                    'profile' => ProfileResource::make($profile->fresh()),
                    'result' => true,
                ]]);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();

        if(!$user)
        {
            return response([
                'data' => ["message" => "User could not be found.",
                'result' => false,
            ]], 404);
        }

        $user->delete();

        return response([
            'data' => [
                "message" => "The user was successfully deleted.",
                'result' => true,
            ]]);
    }

    public function deleteImage()
    {
        auth()->user()->clearMediaCollection('image');

        return response([
            'data' => [
                "message" => "The image was successfully deleted.",
                'result' => true,
            ]]);
    }

    public function switchRole(SwitchRoleRequest $request)
    {
        auth()->user()->update($request->validated());

        return response([
            'data' => [
                'profile' => ProfileResource::make(auth()->user()),
                'result' => true,
            ],
        ]);
    }

    public function orders(Request $request, $user)
    {
        $user = User::where('id', $user)->first();

        if(!$user){
            return response([
                'data' => [
                    "message" => "The user could not be found.",
                    'result' => false,
            ]], 404);
        }

        $user->loadMissing('userOrders');

        if(in_array($request->status, array_keys(Order::getStatuses()))){
            $userOrders = $user->userOrders->where('status', $request->status);
        } else {
            $userOrders = $user->userOrders;
        }

        return response([
            'data' =>[
                'orders' => OrderResource::collection($userOrders),
                'result' => true,
        ]]);
    }

    public function order($user, $order)
    {
        $user = User::where('id', $user)->first();
        $order = Order::where('id', $order)->with('notepad')->first();
        if(!$user){
            return response([
                'data' => ["message" => "The user could not be found.",
                'result' => false,
            ]], 404);
        }

        if(!$order || ($order?->user_id !== $user?->id)){
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

    public function addBook(AddBookRequest $request)
    {
        auth()->user()->books()->syncWithoutDetaching($request->book_id);

        return response([
            'data' =>[
                'profile' => ProfileResource::make(auth()->user()),
                'result' => true,
                ]]);
    }

    public function books()
    {

        return response([
            'data' =>[
                'books' => BookResource::collection(auth()->user()->books()->get()),
                'result' => true,
        ]]);
    }

    public function setFirebaseToken(SetFirebaseTokenRequest $request)
    {
        auth()->user()->update($request->validated());

        return response([
            'data' =>[
                'message' => 'Token was set success!',
                'result' => true,
        ]]);
    }

    public function sendCode()
    {
        $verify_code = sprintf("%06d", mt_rand(1, 999999));

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(array(
                'code'      =>  400,
                'message'   =>  'Email address recently verified'
            ), 400);
        }

        $user->update([
            'email_code' => $verify_code,
            'email_verified_at' => null,
        ]);

        $user->notify(new EmailVerificationNotification ($verify_code));

        return response()->json(['data' => [
            'success' => 'Email sent',
            'result' => true,
        ]]);
    }
}
