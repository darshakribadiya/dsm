<?php

namespace App\Http\Controllers;

use App\Models\Auth\PasswordResetOtp;
use App\Models\Auth\Permission;
use App\Models\Auth\UserInvitation;
use App\Models\User;
use App\Services\Admin\{AuthService, UserService};
use Illuminate\Http\Request;
use App\Mail\Admin\UserInvitationMail;
use App\Mail\Admin\ResetOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

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
            ['data' => $result['data'] ?? null],
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
            'contact' => 'nullable',
        ]);

        $result = $authService->acceptInvitation($request->only(['token', 'password', 'name', 'contact']));

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

    public function getUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'status' => $user->status,
                'roles' => $user->roles->pluck('role_name'),
                'permissions' => $user->getAllPermissions(),
            ]
        ], 200);
    }

    /**
     * Get user roles and permissions with names and IDs
     */
    public function getUserRolesAndPermissions(Request $request, $id, AuthService $authService)
    {
        $result = $authService->getUserRolesAndPermissions($id);
        return response()->json($result, 200);
    }

    public function update(Request $request, $id, AuthService $authService)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $result = $authService->updateUser($id, $validated);
        return response()->json($result, 200);
    }

    /**
     * Send password reset link via email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($status),
            ], 200);
        } else {
            return response()->json([
                'message' => __($status),
            ], 400);
        }
    }

    /**
     * Handle password reset via link
     */
    public function resetPasswordLinkBase(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status),
            ], 200);
        }

        return response()->json([
            'message' => __($status),
        ], 400);
    }

    /**
     * Send OTP to email
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $otp = rand(100000, 999999);

        $user->passwordResetOtps()
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->delete();

        $user->passwordResetOtps()->create([
            'otp_code' => Hash::make($otp),
            'channel' => 'email',
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'max_attempts' => 3,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Mail::to($user->email)->send(new ResetOtpMail($otp, $user));

        return response()->json([
            'message' => 'OTP has been sent to your registered email address.',
        ], 200);
    }

    /**
     * Verify OTP and Reset Password
     */
    public function resetPasswordOtpBase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid request payload.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $otpRecord = PasswordResetOtp::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP has expired or is invalid.',
            ], 404);
        }

        $otpRecord->increment('attempts');

        if (!Hash::check($request->otp_code, $otpRecord->otp_code)) {
            return response()->json([
                'message' => 'Invalid OTP code.',
                'attempts' => $otpRecord->attempts,
            ], 404);
        }

        $otpRecord->update([
            'verified_at' => now(),
        ]);

        DB::transaction(function () use ($user, $request, $otpRecord) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            PasswordResetOtp::where('user_id', $user->id)
                ->whereNull('verified_at')
                ->delete();
        });

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ], 200);
    }


}
