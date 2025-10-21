<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(Request $request){
        $data = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

        $user = User::where('email',$data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }

        return response()->json(UserResource::make($user));
    }
}
