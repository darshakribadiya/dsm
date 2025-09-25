<?php

namespace App\Http\Controllers;

use App\Services\Admin\{AuthService, UserService};
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, AuthService $authService)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $authService->login($validated);

        return response()->json(
            ['message' => $result['message'], 'data' => $result['data'] ?? null],
            $result['status']
        );
    }

    public function logout(Request $request, AuthService $authService)
    {
        $result = $authService->logout($request);
        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function me(Request $request, AuthService $authService)
    {
        $result = $authService->me($request);
        return response()->json($result['user'], $result['status']);
    }

    public function allUsers(Request $request, UserService $userService)
    {
        $filters = [
            'user_type' => $request->query('user_type'),
            'status' => $request->query('status'),
            'search' => $request->query('search'),
            'per_page' => $request->query('per_page', 10),
        ];
        $users = $userService->getAllUsers($filters);
        return response()->json($users);
    }


}