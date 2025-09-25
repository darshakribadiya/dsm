<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\RolePermissionService;

class RolePermissionsController extends Controller
{
    public function index(Request $request, RolePermissionService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->getAllRoles(),
        ]);
    }

    public function storeRole(Request $request, RolePermissionService $service)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = $service->createRole($validated);
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);
    }

    public function showRole($id, RolePermissionService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->getRoleById($id),
        ]);
    }

    public function updateRole(Request $request, $id, RolePermissionService $service)
    {
        $validated = $request->validate([
            'role_name' => 'sometimes|required|string|max:255|unique:roles,role_name,' . $id,
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = $service->updateRole($id, $validated);
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role,
        ]);
    }

    public function destroyRole($id, RolePermissionService $service)
    {
        $service->deleteRole($id);
        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    public function permissions(RolePermissionService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->getAllPermissions(),
        ]);
    }

    public function storePermission(Request $request, RolePermissionService $service)
    {
        $validated = $request->validate([
            'permission_name' => 'required|string|max:255|unique:permissions,permission_name',
        ]);

        $permission = $service->createPermission($validated);
        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => $permission,
        ], 201);
    }

    public function showPermission($id, RolePermissionService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->getPermissionById($id),
        ]);
    }

    public function updatePermission(Request $request, $id, RolePermissionService $service)
    {
        $validated = $request->validate([
            'permission_name' => 'required|string|max:255|unique:permissions,permission_name,' . $id,
        ]);

        $permission = $service->updatePermission($id, $validated);
        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => $permission,
        ]);
    }

    public function destroyPermission($id, RolePermissionService $service)
    {
        $service->deletePermission($id);
        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully',
        ]);
    }
}