<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'confirmationCode' => ['required', 'string', 'max:100'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(array(
                'code'      =>  404,
                'message'   =>  'User not found'
            ), 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(array(
                'code'      =>  400,
                'message'   =>  'Email address recently verified'
            ), 400);
        }

        if ($user->email_code !== $data['confirmationCode'] && $user->email_code != null) {
            return response()->json(array(
                'code'      =>  400,
                'message'   =>  'The code is incorrect'
            ), 400);
        }

        $user->markEmailAsVerified();

        $user->update([
            'email_code' => null,
        ]);

        $user->activities()->create([
            'name'  => 'Верифікація електронної пошти',
        ]);

        return response()->json([
            'data' => [
                'profile' => ProfileResource::make($user),
                'result' => true,
                'accessToken' => $user->createToken('app')->plainTextToken,
                'type' => 'Bearer',
        ]]);
    }
}
