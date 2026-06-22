<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $user->toArray();
        $userData['token'] = $token;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => $userData
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'status'=>'error',
                'message'=>'Username or password incorrect'
            ]);
        }
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $user->toArray();
        $userData['token'] = $token;

        return response()->json([
            'status'=>'success',
            'message'=>'Login successful',
            'data'=>$userData
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Logout successful'
        ], 200);
    }
}
