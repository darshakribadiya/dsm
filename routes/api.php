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

    Route::get('/roles', [AuthController::class, 'getRolesWithPermissions']);
    Route::post('/roles', [AuthController::class, 'createRole']);
    Route::put('/roles/{id}/permissions', [AuthController::class, 'updateRolePermissions']);
    Route::get('/permissions', [AuthController::class, 'getAllPermissions']);

});
Route::get('/all-users', [AuthController::class, 'allUsers']);