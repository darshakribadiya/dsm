<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Roles
    Route::get('/roles', [AuthController::class, 'listRoles']);
    Route::post('/roles', [AuthController::class, 'createRole']);
    Route::put('/roles/{id}', [AuthController::class, 'updateRole']);
    Route::delete('/roles/{id}', [AuthController::class, 'deleteRole']);

    // Permissions
    Route::get('/permissions', [AuthController::class, 'listPermissions']);

    // Users
    Route::get('/users', [AuthController::class, 'listUsers']);
    Route::post('/users', [AuthController::class, 'createUser']);
    Route::put('/users/{id}', [AuthController::class, 'updateUser']);
    Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
});
Route::get('/all-users', [AuthController::class, 'allUsers']);