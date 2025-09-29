<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

// Role Routes
Route::get('roles', [RoleController::class, 'index']);
Route::post('roles', [RoleController::class, 'store']);
Route::get('roles/{id}', [RoleController::class, 'show']);
Route::put('roles/{id}', [RoleController::class, 'update']);
Route::delete('roles/{id}', [RoleController::class, 'destroy']);

// Role Permission Assignment Routes
Route::put('roles/{id}/permissions', [RoleController::class, 'updatePermissions']);
Route::post('roles/{id}/permissions', [RoleController::class, 'attachPermissions']);
Route::delete('roles/{id}/permissions', [RoleController::class, 'detachPermissions']);
Route::delete('roles/{roleId}/permissions/{permissionId}', [RoleController::class, 'detachPermission']);

// Role Permission Matrix
Route::get('roles/{id}/permission-matrix', [RoleController::class, 'permissionMatrix']);

// Permission Routes
Route::get('permissions', [PermissionController::class, 'index']);
Route::post('permissions', [PermissionController::class, 'store']);
Route::post('permissions/bulk', [PermissionController::class, 'bulkStore']);
Route::get('permissions/{id}', [PermissionController::class, 'show']);
Route::put('permissions/{id}', [PermissionController::class, 'update']);
Route::delete('permissions/{id}', [PermissionController::class, 'destroy']);

// Permission Matrix
Route::get('permissions/matrix', [PermissionController::class, 'matrix']);