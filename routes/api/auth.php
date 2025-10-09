<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Password reset (public)
Route::post('forgot-password', [AuthController::class, 'sendResetLink']);
Route::post('send-reset-otp', [AuthController::class, 'sendResetOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Invitation routes (public)
Route::get('/invitation', [AuthController::class, 'getInvitation']);
Route::post('/accept-invitation', [AuthController::class, 'acceptInvitation']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/all-users', [AuthController::class, 'allUsers']);
    Route::get('users/{user}', [AuthController::class, 'getUser']);
    Route::get('users/{user}/roles-permissions', [AuthController::class, 'getUserRolesAndPermissions']);
    Route::patch('users/{user}/update', [AuthController::class, 'update']);

    // Send invitation (protected)
    Route::post('/send-invitation', [AuthController::class, 'inviteUser']);
    Route::get('/invitations', [AuthController::class, 'listInvitations']);
    Route::delete('/invitations/{id}', [AuthController::class, 'cancelInvitation']);
});
