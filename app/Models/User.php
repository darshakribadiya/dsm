<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Auth\{Role, Permission};
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'contact',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔹 Roles relationship
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    // 🔹 Direct permissions relationship
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    // 🔹 Helper: check if user has a role
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role_name', $role)->exists();
    }

    // 🔹 Helper: check if user has a permission (direct or via role)
    public function hasPermission(string $permission): bool
    {
        // Direct permission
        if ($this->permissions()->where('permission_name', $permission)->exists()) {
            return true;
        }

        // Via roles
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('permission_name', $permission))
            ->exists();
    }

    public function getAllPermissions(): array
    {
        $directPermissions = $this->permissions->pluck('permission_name');

        $rolePermissions = $this->roles
            ->flatMap(fn($role) => $role->permissions->pluck('permission_name'))
            ->unique();

        return $directPermissions
            ->merge($rolePermissions)
            ->unique()
            ->values()
            ->toArray();
    }


}