<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['nullable', 'sometimes', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255', 'in:seamstress,customer'],
            'role_seamstress' => ['nullable', 'sometimes', 'boolean'],
            // 'role_customer' => ['nullable', 'sometimes', 'boolean'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required',  Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->hasVerifiedEmail()) {
            return response(['data' => [
                'message'   =>  'User alredy exists'
            ]],400);
        }

        $verify_code = sprintf("%06d", mt_rand(1, 999999));

        $user = User::updateOrCreate([
            'email' => $request->email,
        ],[
            'name' => $request->firstName,
            'last_name' => $request->lastName ?? null,

            'role' => $request->role,
            'is_seamstress' => $request->role_seamstress ?? false,
            // 'is_customer' => $request->role_customer ?? true,
            'email_code' => $verify_code,
            'password' => Hash::make($request->password),
        ]);

        $user->notify(new EmailVerificationNotification ($verify_code));

        event(new Registered($user));

        $user->activities()->create([
            'name'  => 'Реєстрація',
        ]);

        Auth::login($user);

        return response(['data' => [
            'profile' => ProfileResource::make($user),
            'result' => true,
        ]]);
    }
}
