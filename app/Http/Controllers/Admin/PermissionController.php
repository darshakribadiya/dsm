<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Auth\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Get all permissions (flat list)
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::select('id', 'permission_name', 'action')
            ->orderBy('permission_name', 'asc')
            ->orderBy('action', 'asc')
            ->get();

        return response()->json(['data' => $permissions]);
    }

    /**
     * Get a single permission
     */
    public function show($id): JsonResponse
    {
        $permission = Permission::select('id', 'permission_name', 'action', 'created_at', 'updated_at')
            ->findOrFail($id);

        return response()->json(['data' => $permission]);
    }

    /**
     * Create a new permission
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'permission_name' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        // Check for uniqueness of permission_name + action combination
        $exists = Permission::where('permission_name', $validated['permission_name'])
            ->where('action', $validated['action'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'permission' => ['This permission already exists.']
                ]
            ], 422);
        }

        $permission = Permission::create($validated);

        return response()->json(['data' => $permission], 201);
    }

    /**
     * Update a permission
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'permission_name' => 'sometimes|required|string|max:255',
            'action' => 'sometimes|required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);

        // Check for uniqueness of permission_name + action combination (excluding current record)
        if (isset($validated['permission_name']) || isset($validated['action'])) {
            $permissionName = $validated['permission_name'] ?? $permission->permission_name;
            $action = $validated['action'] ?? $permission->action;

            $exists = Permission::where('permission_name', $permissionName)
                ->where('action', $action)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        'permission' => ['This permission already exists.']
                    ]
                ], 422);
            }
        }

        $permission->update($validated);

        return response()->json(['data' => $permission]);
    }

    /**
     * Delete a permission
     */
    public function destroy($id): JsonResponse
    {
        $permission = Permission::findOrFail($id);

        // Detach from roles and users before deleting
        $permission->roles()->detach();
        $permission->users()->detach();
        $permission->delete();

        return response()->json(['message' => 'Permission deleted']);
    }

    /**
     * Bulk create permissions for a resource
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'permission_name' => 'required|string|max:255',
            'actions' => 'required|array',
            'actions.*' => 'string|max:255',
        ]);

        $permissions = [];

        DB::transaction(function () use ($validated, &$permissions) {
            foreach ($validated['actions'] as $action) {
                // Check if permission already exists
                $exists = Permission::where('permission_name', $validated['permission_name'])
                    ->where('action', $action)
                    ->exists();

                if (!$exists) {
                    $permissions[] = Permission::create([
                        'permission_name' => $validated['permission_name'],
                        'action' => $action,
                    ]);
                }
            }
        });

        return response()->json(['data' => $permissions], 201);
    }

    /**
     * Get permission matrix (grouped by resource)
     */
    public function matrix(): JsonResponse
    {
        $permissions = Permission::select('permission_name', 'action')
            ->orderBy('permission_name', 'asc')
            ->orderBy('action', 'asc')
            ->get();

        // Group by resource name
        $resources = [];
        foreach ($permissions as $permission) {
            $resourceName = $permission->permission_name;

            if (!isset($resources[$resourceName])) {
                $resources[$resourceName] = [
                    'name' => $resourceName,
                    'actions' => [],
                ];
            }

            $resources[$resourceName]['actions'][] = $permission->action;
        }

        return response()->json(['data' => array_values($resources)]);
    }
}