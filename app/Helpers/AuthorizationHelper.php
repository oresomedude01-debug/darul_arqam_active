<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Blade;

class AuthorizationHelper
{
    /**
     * Register Blade directives for authorization
     */
    public static function registerDirectives(): void
    {
        // @hasRole('admin')
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // @hasAnyRole('admin', 'teacher')
        Blade::if('hasAnyRole', function (...$roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        // @hasAllRoles('admin', 'teacher')
        Blade::if('hasAllRoles', function (...$roles) {
            return auth()->check() && auth()->user()->hasAllRoles($roles);
        });

        // @hasPermission('view-students')
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // @hasAnyPermission('view-students', 'edit-students')
        Blade::if('hasAnyPermission', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        // @hasAllPermissions('view-students', 'edit-students')
        Blade::if('hasAllPermissions', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAllPermissions($permissions);
        });

        // @isAdmin
        Blade::if('isAdmin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });

        // @isTeacher
        Blade::if('isTeacher', function () {
            return auth()->check() && auth()->user()->isTeacher();
        });

        // @isStudent
        Blade::if('isStudent', function () {
            return auth()->check() && auth()->user()->isStudent();
        });

        // @isParent
        Blade::if('isParent', function () {
            return auth()->check() && auth()->user()->isParent();
        });
    }
}
