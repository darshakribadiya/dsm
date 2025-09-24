<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\{Role, Permission};
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Create a new role
     */
    public function createRole(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,role_name',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    /**
     * Get all roles with their permissions
     */
    public function getRolesWithPermissions(): JsonResponse
    {
        $roles = Role::with('permissions')->get();
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Get all permissions (standalone)
     */
    public function getAllPermissions(): JsonResponse
    {
        $permissions = Permission::all();
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }


}