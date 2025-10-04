<?php

namespace App\Services\Admin;

use App\Mail\Admin\UserCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Auth\UserInvitation;
use App\Mail\Admin\UserInvitationMail;



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
            'status' => 200,
            'data' => [
                'message' => 'Login successful',
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

    public function sendInvitation(array $data, int $invitedBy): UserInvitation
    {
        $token = Str::random(64);

        $invitation = UserInvitation::create([
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'token' => $token,
            'expires_at' => now()->addDays(7),
            'invited_by' => $invitedBy,
        ]);

        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        return $invitation->load('role');
    }

    public function acceptInvitation(array $data)
    {
        $invitation = UserInvitation::where('token', $data['token'])
            ->where('accepted', false)
            ->where('expires_at', '>', now())
            ->first();

        Log::info($invitation);

        if (!$invitation) {
            return [
                'status' => 400,
                'message' => 'Invalid or expired invitation token',
            ];
        }

        if (User::where('email', $invitation->email)->exists()) {
            return [
                'status' => 409,
                'message' => 'User with this email already exists.',
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            // 'role' => $invitation->role->
            'password' => Hash::make($data['password']),
        ]);

        $user->roles()->attach($invitation->role_id);

        $invitation->update(['accepted' => true]);

        Mail::to($user->email)->send(new UserCreatedMail($user));

        return [
            'status' => 201,
            'message' => 'Account created successfully. You can now login.',
            'data' => $user,
        ];
    }

    public function getInvitation(string $token)
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('accepted', false)
            ->where('expires_at', '>', now())
            ->with('role')
            ->first();

        if (!$invitation) {
            return [
                'status' => 400,
                'message' => 'Invalid or expired invitation token',
            ];
        }

        return [
            'status' => 200,
            'data' => $invitation,
        ];
    }

    public function listInvitations(?string $search = null, ?string $status = null)
    {
        $query = UserInvitation::with(['role', 'inviter'])->latest();

        if ($search) {
            $query->where('email', 'like', "%{$search}%");
        }

        if ($status === 'pending') {
            $query->where('accepted', false);
        } elseif ($status === 'accepted') {
            $query->where('accepted', true);
        }

        return $query->get();
    }

    /**
     * Get user roles and permissions with names and IDs
     */
    public function getUserRolesAndPermissions(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        // Load relationships
        $user->load(['roles', 'permissions']);

        // Get roles with both ID and name
        $roles = $user->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->role_name,
                'label' => $role->label ?? null
            ];
        })->values();

        // Get direct permissions with both ID and name
        $directPermissions = $user->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->permission_name,
                'action' => $permission->action ?? null
            ];
        })->values();

        // Get role permissions with both ID and name
        $rolePermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->map(function ($permission) use ($role) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->permission_name,
                    'action' => $permission->action ?? null,
                    'via_role' => $role->role_name
                ];
            });
        })->unique('id')->values();

        // Get all permissions (direct + role-based) with deduplication
        $allPermissions = collect($directPermissions)->merge(collect($rolePermissions))->unique('id')->values();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'user_type' => $user->user_type,
            ],
            'roles' => $roles,
            'permissions' => [
                'direct' => $directPermissions,
                'via_roles' => $rolePermissions,
                'all' => $allPermissions
            ]
        ];
    }

    /**
     * Update user with roles and permissions
     */
    public function updateUser(int $userId, array $data): array
    {
        $user = User::findOrFail($userId);
        
        // Update user status
        $user->status = $data['status'];
        $user->save();

        // Sync roles if provided
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        // Sync permissions if provided
        if (isset($data['permissions'])) {
            $user->permissions()->sync($data['permissions']);
        }

        // Reload relationships to get updated data
        $user->load(['roles', 'permissions']);

        return [
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->id,
                'status' => $user->status,
                'roles' => $user->roles->pluck('role_name'),
                'permissions' => $user->getAllPermissions(),
            ]
        ];
    }

}