<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation failed',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $token = $user->createToken('auth_token', ['*'], now()->addWeek())->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],419);
        }
    }

    public function login(Request $request){
        try {
            $validateUser = $request->validator([
                'email' => 'required|string|email|max:255|exists:users',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation failed',
                    'errors' => $validateUser->errors()
                ],422);
            }
            
            if (!Auth::attempt($request->only(['email','password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & password does not match with our record',
                ],401);
            }
    
            $user = User::where('email', $validateUser['email'])->first();
    
            // if (!$user || !Hash::check($validateUser['password'], $user->password)) {
            //     return response([
            //         'message' => 'Bad Credentials',
            //     ], 401);
            // }
    
            $token = $user->createToken('auth_token', ['*'], now()->addWeek())->plainTextToken;
    
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 419);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'User logged out successfully',
                'data' => [],
            ], 200
        );
    }
}
