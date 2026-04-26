<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all users that have this role
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Get all permissions for this role
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }

    /**
     * Grant permission to role
     */
    public function grantPermission(Permission|string $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : Permission::where('slug', $permission)->firstOrFail()->id;

        if (!$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Permission|string $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : Permission::where('slug', $permission)->firstOrFail()->id;
        $this->permissions()->detach($permissionId);
    }

    /**
     * Revoke all permissions
     */
    public function revokeAllPermissions(): void
    {
        $this->permissions()->detach();
    }

    /**
     * Scope: Get active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
