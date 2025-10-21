<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', Rules\Password::defaults()],
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user && !$user->reset_password_code === null) {
            return response()->json(array(
                'data' => [
                'code'      =>  404,
                'message'   =>  'User not found',
                ]), 404);
        }

        if (!($user->reset_password_code != null && Hash::check($request->code ,$user->reset_password_code))) {
            return response()->json(array(
                'data' => [
                'code'      =>  400,
                'message'   =>  'The code is incorrect',
            ]), 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_code' => null,
            'result' => true,
        ]);

        $user->activities()->create([
            'name'  => 'Зміна паролю',
        ]);

        return response()->json([
            'data' => [
            'status' => 'Password changed successfully'
            ]]);
    }
}
