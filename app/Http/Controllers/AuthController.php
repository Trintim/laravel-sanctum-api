<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
        ]);

        $response = [
            'user' => $user,
        ];
        return response($response, 201);
    }

    public function login(Request $request){

        $fields = $request->validate([
            'email' => 'email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Bad Creds'
            ], 401);
        }

        if($user && $user->role === 1){
            $token = $user->createToken('auth_token', ['admin'])->plainTextToken;
        }
        else{
            return response()->json('not admin', 401);
        }

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request){

        if(Auth::user()->role ===1){
            auth()->user()->tokens()->delete();
        }
        return [
            'message' => 'Logged Out'
        ];
    }
}
