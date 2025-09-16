<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = $request->validate([
            'first_name' => 'required|string|min:1',
            'last_name' => 'required|string|min:1',
            'email' => 'required|string|email|min:4|max:255|unique:users',
            'password' => 'required|string|min:4',
            'confirm_password' => 'required|same:password',

        ]);

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $validator['last_name'],
            'email' => $validator['email'],
            'password' => bcrypt($validator['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully!.',
            'user' => $user
        ], status: 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email and Password doe not match',
                'errors' => ['Unauthorized']
            ], 401);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('access_token', ['user'])->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully!',
            'user_info'     => [
                'id'    => $user->id,
                'first_name'  => $user->first_name,
                'last_name'  => $user->last_name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type'  => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
            'errors' => ['Unauthorized']
        ], 401);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'Profile Information',
                'user' => $user
            ],200);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
            'errors' => 'Unauthorized'
        ],401);
    }
}
