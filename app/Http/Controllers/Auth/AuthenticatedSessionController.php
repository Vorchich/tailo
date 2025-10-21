<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $data = $request->validate([
            'email'=>'required|string|email',
            'role' => ['sometimes', 'nullable', 'string', 'max:255', 'in:seamstress,customer'],
            'password'=>'required|min:8'
        ]);

        $user = User::where('email',$data['email'])->first();

        if($request->role)
        {
            $user->update(['role' => $request->role]);
        }

        if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json([
                'message' => 'Invalid Credentials',
                'result' => false,
            ],401);
        }

        // $token = $user->createToken('app')->plainTextToken;

        return response()->json(['data' => [
            'profile' => ProfileResource::make($user),
            'accessToken' => $user->createToken('app')->plainTextToken,
            'type' => 'Bearer',
            'result' => true,
        ]]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
