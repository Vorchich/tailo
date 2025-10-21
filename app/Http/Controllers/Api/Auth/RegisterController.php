<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $verify_code = sprintf("%06d", mt_rand(1, 999999));

        $data = $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'role_seamstress' => ['nullable', 'sometimes', 'boolean'],
            'role_customer' => ['nullable', 'sometimes', 'boolean'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'is_seamstress' => $data['role_seamstress'] ?? false,
            'is_customer' => $data['role_customer'] ?? false,
            'email_code' => $verify_code,
            'password' => Hash::make($data['password']),
        ]);

        $user->notify(new EmailVerificationNotification($verify_code));

        return response()->json(['message:' => 'Check your email!']);
    }

    public function confirm(Request $request)
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

        return response()->json(UserResource::make($user));
    }
}
