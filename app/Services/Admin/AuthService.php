<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthService
{
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
                'status' => 401,
            ];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful',
            'status' => 200,
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ];
    }

    public function logout(Request $request): array
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Logged out',
            'status' => 200,
        ];
    }

    public function me(Request $request): array
    {
        return [
            'success' => true,
            'user' => $request->user(),
            'status' => 200,
        ];
    }
}