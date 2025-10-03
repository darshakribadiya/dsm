<?php

namespace App\Services\Admin;

use App\Mail\Admin\UserCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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



}