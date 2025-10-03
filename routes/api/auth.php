<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Invitation routes (public)
Route::get('/invitation', [AuthController::class, 'getInvitation']);
Route::post('/accept-invitation', [AuthController::class, 'acceptInvitation']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/users/{id}/status', [AuthController::class, 'updateStatus']);

    Route::get('/all-users', [AuthController::class, 'allUsers']);

    // Send invitation (protected)
    Route::post('/send-invitation', [AuthController::class, 'inviteUser']);
    Route::get('/invitations', [AuthController::class, 'listInvitations']);
    Route::delete('/invitations/{id}', [AuthController::class, 'cancelInvitation']);


});