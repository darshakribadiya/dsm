<?php

namespace App\Http\Controllers;

use App\Models\Auth\UserInvitation;
use App\Models\User;
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

        $user = $result['user'];

        $user->load(['roles.permissions', 'permissions']);

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'roles' => $user->roles->pluck('role_name'),
            'permissions' => $user->getAllPermissions(),
        ];

        return response()->json($response, $result['status']);
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
    public function acceptInvitation(Request $request, AuthService $authService)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
        ]);

        $result = $authService->acceptInvitation($request->only(['token', 'password', 'name']));

        return response()->json(
            isset($result['data']) ? ['message' => $result['message'], 'data' => $result['data']] : ['message' => $result['message']],
            $result['status']
        );
    }

    /**
     * Get invitation details by token
     */
    public function getInvitation(Request $request, AuthService $authService)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $authService->getInvitation($request->token);

        return response()->json(
            isset($result['data']) ? ['data' => $result['data']] : ['message' => $result['message']],
            $result['status']
        );
    }

    public function listInvitations(Request $request, AuthService $authService)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $invitations = $authService->listInvitations($search, $status);

        return response()->json([
            'data' => $invitations
        ], 200);
    }

    /**
     * Cancel (revoke) an invitation
     */
    public function cancelInvitation(string $id)
    {
        $invitation = UserInvitation::where('id', $id)
            ->where('accepted', false) // Cannot revoke if already accepted
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invitation not found or already accepted.'
            ], 404);
        }

        $invitation->delete();

        return response()->json([
            'message' => 'Invitation has been revoked successfully.'
        ], 200);
    }





}