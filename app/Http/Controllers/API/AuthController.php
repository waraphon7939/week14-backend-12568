<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = $request->validate([
            'first_name' => 'required|string|min:1',
            'last_name' => 'required|string|min:1',
            'email' => 'required|string|email|min:4|max:255|unique:users',
            'password' => 'required|string|min:8',
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
}
