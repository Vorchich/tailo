<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AppleLoginRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AppleAuthController extends Controller
{

    public function login(AppleLoginRequest $request){
        $result = $this->verifyAppleToken($request->token);
        $user = User::where('email', $result['user']['email'])->first();
        if($user){
            $user->apple_id = $result['user']['sub'];
            $user->save();
        }
        else{
            $user = User::create([
                'name' => $request->name ?? $result['user']['email'],
                'email' => $result['user']['email'],
                'apple_id' => $result['user']['sub'],
                'password' => Hash::make(Str::random(16)),
            ]);
        }
        if($user->apple_id != $result['user']['sub']){
            return response()->json(['message' => 'Неправильний id'], 400);
        }
        $user->markEmailAsVerified();
        $user = User::where('email', $result['user']['email'])->where('apple_id', $result['user']['sub'])->first();
        $user->tokens()->delete();


        $user->save();

        return response()->json(['data' => [
            'profile' => ProfileResource::make($user),
            'accessToken' => $user->createToken('app')->plainTextToken,
            'type' => 'Bearer',
            'result' => true,
        ]]);
    }

    private function getApplePublicKeys() {
        $url = "https://appleid.apple.com/auth/keys";
        $keys = file_get_contents('https://appleid.apple.com/auth/keys');
        return json_decode($keys, true);
    }

    private function verifyAppleToken($idToken) {
        $publicKeys = $this->getApplePublicKeys();
        $header = explode('.', $idToken)[0];
        $header = json_decode(base64_decode($header), true);

        if (!isset($header['kid'])) {
            return ['success' => false, 'message' => 'Invalid token header'];
        }

        $kid = $header['kid'];
        $alg = $header['alg'];

        foreach ($publicKeys['keys'] as $key) {
            if ($key['kid'] === $kid) {
                $pem = $this->buildPemFromAppleKey($key);
                return $this->decodeAndVerifyToken($idToken, $pem);
            }
        }

        return ['success' => false, 'message' => 'No matching public key found'];
    }

    private function buildPemFromAppleKey($key) {
        $modulus = base64_decode(strtr($key['n'], '-_', '+/'));
        $exponent = base64_decode(strtr($key['e'], '-_', '+/'));

        $publicKey = "-----BEGIN PUBLIC KEY-----\n";
        $publicKey .= chunk_split(base64_encode("\x30\x81\x89\x02\x81\x81" . $modulus . "\x02\x03" . $exponent), 64, "\n");
        $publicKey .= "-----END PUBLIC KEY-----\n";

        return $publicKey;
    }

    private function decodeAndVerifyToken($idToken, $pem) {
        $jwtParts = explode('.', $idToken);
        if (count($jwtParts) !== 3) {
            return ['success' => false, 'message' => 'Invalid token format'];
        }

        $payload = json_decode(base64_decode($jwtParts[1]), true);

        if (!$payload) {
            return ['success' => false, 'message' => 'Invalid token payload'];
        }

        if (!isset($payload['aud']) || $payload['aud'] !== env("APPLE_BUNDLE_ID")) {
            return ['success' => false, 'message' => 'Invalid audience'];
        }

        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return ['success' => false, 'message' => 'Token expired'];
        }

        return ['success' => true, 'user' => $payload];
    }
}
