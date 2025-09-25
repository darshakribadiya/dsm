<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RolePermissionsController;

//  Role Routes
Route::get('roles', [RolePermissionsController::class, 'index']);
Route::post('roles', [RolePermissionsController::class, 'storeRole']);
Route::get('roles/{id}', [RolePermissionsController::class, 'showRole']);
Route::put('roles/{id}', [RolePermissionsController::class, 'updateRole']);
Route::delete('roles/{id}', [RolePermissionsController::class, 'destroyRole']);

// Permission Routes
Route::get('permissions', [RolePermissionsController::class, 'permissions']);
Route::post('permissions', [RolePermissionsController::class, 'storePermission']);
Route::get('permissions/{id}', [RolePermissionsController::class, 'showPermission']);
Route::put('permissions/{id}', [RolePermissionsController::class, 'updatePermission']);
Route::delete('permissions/{id}', [RolePermissionsController::class, 'destroyPermission']);