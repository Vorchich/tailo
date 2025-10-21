<?php

namespace App\Http\Controllers\Auth;

use Google\Client;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class GoogleAuthController extends Controller
{
    public function login(GoogleLoginRequest $request){
        if($request->platform == "iOS"){
            $client_id = env("GOOGLE_IOS_CLIENT_ID");
        }
        else{
            $client_id = env("GOOGLE_ANDROID_CLIENT_ID");
        }
        $client = new Client(['client_id' => $client_id]);
        $payload = $client->verifyIdToken($request->token);

        try {
            $email = $payload['email'];
            $name = $payload['given_name'];
            $googleId = $payload['sub'];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Неправильний формат токену'], 400);
        }
        $user = User::where('email', $email)->first();
        if(!$user){
            $user = User::create([
                'name' => $name  ?? 'user',
                'email' => $email,
                'google_id' => $googleId,
                'password' => Hash::make(Str::random(16)),
            ]);
        }
        if(!$user->google_id){
            $user->google_id = $googleId;
            $user->save();
        }
        $user->markEmailAsVerified();
        $user = User::where('email', $email)->where('google_id', $googleId)->first();
        $user->tokens()->delete();
        $user->save();

        return response()->json(['data' => [
            'profile' => ProfileResource::make($user),
            'accessToken' => $user->createToken('app')->plainTextToken,
            'type' => 'Bearer',
            'result' => true,
        ]]);
    }

}
