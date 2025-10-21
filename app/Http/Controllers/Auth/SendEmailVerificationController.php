<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;

class SendEmailVerificationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $verify_code = sprintf("%06d", mt_rand(1, 999999));

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(array(
                'data' => [
                'code'      =>  404,
                'message'   =>  'User not found',
                ]), 404);
        }

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
