<?php

namespace App\Traits;

/**
 * Trait for controllers that need to check roles and permissions
 */
trait Authorizable
{
    /**
     * Check if authenticated user has a specific role
     */
    protected function authorize($ability, $model = null): void
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();

        if (is_string($ability)) {
            // Check if it's a role or permission
            if ($user->hasRole($ability)) {
                return;
            }

            if ($user->hasPermission($ability)) {
                return;
            }

            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Check if authenticated user can perform an action
     */
    protected function can($ability, $model = null): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if (is_string($ability)) {
            return $user->hasRole($ability) || $user->hasPermission($ability);
        }

        return false;
    }

    /**
     * Check if authenticated user cannot perform an action
     */
    protected function cannot($ability, $model = null): bool
    {
        return !$this->can($ability, $model);
    }
}
