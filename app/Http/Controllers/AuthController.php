<?php

namespace App\Http\Controllers;

use App\Models\Auth\UserInvitation;
use App\Services\Admin\{AuthService, UserService};
use Illuminate\Http\Request;
use App\Mail\Admin\UserInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    /**
     * Send an invitation
     */
    public function inviteUser(Request $request, AuthService $authService)
    {
        $request->validate(
            [
                'email' => 'required|email|unique:users,email|unique:invitations,email',
                'role_id' => 'required|exists:roles,id',
            ],
            [
                'email.required' => 'Please enter an email address.',
                'email.email' => 'That doesn\'t look like a valid email.',
                'email.unique' => 'This email is already invited or registered.',
                'role_id.required' => 'Role is required.',
                'role_id.exists' => 'Selected role does not exist in our system.',
            ]
        );


        $invitation = $authService->sendInvitation(
            $request->only(['email', 'role_id']),
            auth()->id()
        );

        return response()->json([
            'message' => 'Invitation sent successfully',
            'data' => $invitation,
        ], 201);
    }

    /**
     * Accept an invitation
     */
    public function acceptInvitation(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
        ]);

        $invitation = UserInvitation::where('token', $request->token)
            ->where('accepted', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation token'
            ], 400);
        }

        // Create user account
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign role to user
        $user->roles()->attach($invitation->role_id);

        // Mark invitation as accepted
        $invitation->update(['accepted' => true]);

        return response()->json([
            'message' => 'Account created successfully. You can now login.',
            'data' => $user
        ], 201);
    }

    /**
     * Get invitation details by token
     */
    public function getInvitation(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $invitation = UserInvitation::where('token', $request->token)
            ->where('accepted', false)
            ->where('expires_at', '>', now())
            ->with('role')
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation token'
            ], 400);
        }

        return response()->json([
            'data' => $invitation
        ], 200);
    }


}