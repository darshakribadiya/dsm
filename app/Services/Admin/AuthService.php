<?php

namespace App\Services\Admin;

use App\Models\Auth\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\Admin\UserInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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


}