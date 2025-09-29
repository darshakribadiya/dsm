<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;


use App\Models\Auth\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Get all roles
     */
    public function index(): JsonResponse
    {
        $roles = Role::select('id', 'role_name', 'label', 'created_at', 'updated_at')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['data' => $roles]);
    }

    /**
     * Get a single role with its permissions
     */
    public function show($id): JsonResponse
    {
        $role = Role::with([
            'permissions' => function ($query) {
                $query->select('permissions.id', 'permission_name', 'action', 'created_at', 'updated_at');
            }
        ])
            ->select('id', 'role_name', 'label', 'created_at', 'updated_at')
            ->findOrFail($id);

        return response()->json(['data' => $role]);
    }

    /**
     * Create a new role
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name',
            'label' => 'required|string|max:255',
        ]);

        $role = Role::create($validated);

        return response()->json(['data' => $role], 201);
    }

    /**
     * Update a role
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => 'sometimes|required|string|max:255|unique:roles,role_name,' . $id,
            'label' => 'sometimes|required|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->update($validated);

        return response()->json(['data' => $role]);
    }

    /**
     * Delete a role
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);

        // Detach all permissions before deleting
        $role->permissions()->detach();
        $role->delete();

        return response()->json(['message' => 'Role deleted']);
    }

    /**
     * Replace all permissions for a role
     */
    public function updatePermissions(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->permissions()->sync($validated['permission_ids']);

        // Return role with permissions
        $role = $role->load([
            'permissions' => function ($query) {
                $query->select('permissions.id', 'permission_name', 'action');
            }
        ]);

        return response()->json(['data' => $role]);
    }

    /**
     * Attach permissions to a role
     */
    public function attachPermissions(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->permissions()->attach($validated['permission_ids']);

        return response()->json(['message' => 'Permissions attached']);
    }

    /**
     * Detach permissions from a role
     */
    public function detachPermissions(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->permissions()->detach($validated['permission_ids']);

        return response()->json(['message' => 'Permissions detached']);
    }

    /**
     * Detach a single permission from a role
     */
    public function detachPermission($roleId, $permissionId): JsonResponse
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->detach($permissionId);

        return response()->json(['message' => 'Permission detached']);
    }

    /**
     * Get permission matrix for a role (grouped by resource)
     */
    public function permissionMatrix($id): JsonResponse
    {
        $role = Role::with([
            'permissions' => function ($query) {
                $query->select('permissions.id', 'permission_name', 'action');
            }
        ])
            ->select('id', 'role_name', 'label')
            ->findOrFail($id);

        // Group permissions by resource name
        $resources = [];
        foreach ($role->permissions as $permission) {
            $resourceName = $permission->permission_name;

            if (!isset($resources[$resourceName])) {
                $resources[$resourceName] = [
                    'name' => $resourceName,
                    'create' => false,
                    'read' => false,
                    'update' => false,
                    'delete' => false,
                ];
            }

            $resources[$resourceName][$permission->action] = true;
        }

        $roleData = [
            'id' => $role->id,
            'role_name' => $role->role_name,
            'label' => $role->label,
            'resources' => array_values($resources),
        ];

        return response()->json(['data' => $roleData]);
    }
}