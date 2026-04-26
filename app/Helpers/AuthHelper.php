<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Check if authenticated user has a specific permission
     */
    public static function hasPermission(string $permission): bool
    {
        return Auth::check() && Auth::user()->hasPermission($permission);
    }

    /**
     * Check if authenticated user has any of the specified permissions
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        return Auth::check() && Auth::user()->hasAnyPermission($permissions);
    }

    /**
     * Check if authenticated user has all of the specified permissions
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        return Auth::check() && Auth::user()->hasAllPermissions($permissions);
    }

    /**
     * Check if authenticated user has a specific role
     */
    public static function hasRole(string $role): bool
    {
        return Auth::check() && Auth::user()->hasRole($role);
    }

    /**
     * Check if authenticated user is admin
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Check if authenticated user is teacher
     */
    public static function isTeacher(): bool
    {
        return Auth::check() && Auth::user()->isTeacher();
    }

    /**
     * Check if authenticated user is student
     */
    public static function isStudent(): bool
    {
        return Auth::check() && Auth::user()->isStudent();
    }

    /**
     * Check if authenticated user is parent
     */
    public static function isParent(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
}
