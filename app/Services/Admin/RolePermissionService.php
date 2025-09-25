<?php

namespace App\Services\Admin;

use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    /*
    |--------------------------------------------------------------------------
    | Role Methods
    |--------------------------------------------------------------------------
    */

    // Get all roles with permissions
    public function getAllRoles()
    {
        return Role::with('permissions')->orderBy('id', 'asc')->get();
    }

    // Create a new role with permissions
    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'role_name' => $data['role_name'],
            ]);

            if (!empty($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            return $role->load('permissions');
        });
    }

    // Get a single role with permissions
    public function getRoleById($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    // Update role and its permissions
    public function updateRole($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $role = Role::findOrFail($id);

            if (isset($data['role_name'])) {
                $role->update(['role_name' => $data['role_name']]);
            }

            if (isset($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            return $role->load('permissions');
        });
    }

    // Delete role
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();
        return $role->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Methods
    |--------------------------------------------------------------------------
    */

    // Get all permissions
    public function getAllPermissions()
    {
        return Permission::orderBy('id', 'asc')->get();
    }

    // Create new permission
    public function createPermission(array $data)
    {
        return Permission::create([
            'permission_name' => $data['permission_name'],
        ]);
    }

    // Get single permission
    public function getPermissionById($id)
    {
        return Permission::findOrFail($id);
    }

    // Update permission
    public function updatePermission($id, array $data)
    {
        $permission = Permission::findOrFail($id);
        $permission->update([
            'permission_name' => $data['permission_name'],
        ]);
        return $permission;
    }

    // Delete permission
    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->roles()->detach();
        $permission->users()->detach();
        return $permission->delete();
    }
}