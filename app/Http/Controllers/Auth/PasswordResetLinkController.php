<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $code = rand(100000, 999999);

        $user->reset_password_code = Hash::make($code);
        $user->save();

        $user->notify(new ResetPasswordNotification ($code));

        return response()->json(['data' => [
                'success' => 'Email sent',
                'result' => true,
            ]]);

    }
}
