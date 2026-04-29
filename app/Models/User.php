<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        
        // Student specific fields
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'nationality',
        'religion',
        'place_of_birth',
        'address',
        'photo_path',
        'blood_group',
        'admission_number',
        'admission_date',
        'status',
        'registration_token_id',
        'class_level',
        'section',
        'session_year',
        'roll_number',
        'previous_school_name',
        'previous_school_address',
        'previous_school_grade',
        'previous_school_year',
        'previous_school_reason',
        'previous_result_path',
        'allergies',
        'medical_conditions',
        'medications',
        'emergency_medical_consent',
        'special_needs',
        'notes',
        
        // Parent specific fields
        'parent_id',
        'occupation',
        'relationship_to_student',
        
        // System Fields
        'created_by',
        'updated_by',
        
        // Push notification fields
        'fcm_token',
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

    /**
     * Get user profile
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get parent of student (if this is a student)
     * Access through the user_profiles table where parent_id is stored
     */
    public function parentUser()
    {
        // Get the parent_id from this user's profile, then load that User
        $parentId = $this->profile?->parent_id;
        return $parentId ? self::find($parentId) : null;
    }

    /**
     * Get children (students) of parent (if this is a parent)
     * Goes through user_profiles table where parent_id = this user's id
     */
    public function children()
    {
        return $this->hasManyThrough(User::class, UserProfile::class, 'parent_id', 'id', 'id', 'user_id');
    }

    /**
     * Get all roles for this user
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Get all permissions through roles
     */
    public function permissions()
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn($role) => $role->permissions)
            ->unique('id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->roles()
                ->where(function($q) use ($roles) {
                    $q->where('slug', $roles)
                      ->orWhere('name', $roles);
                })
                ->exists();
        }

        return $this->roles()
            ->where(function($q) use ($roles) {
                $q->whereIn('slug', $roles)
                  ->orWhereIn('name', $roles);
            })
            ->exists();
    }

    /**
     * Check if user has all specified roles
     */
    public function hasAllRoles(array $roles): bool
    {
        return collect($roles)->every(fn($role) => $this->hasRole($role));
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('slug', $permissionSlug))
            ->exists();
    }

    /**
     * Check if user has all specified permissions
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        return collect($permissionSlugs)->every(fn($permission) => $this->hasPermission($permission));
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return collect($permissionSlugs)->some(fn($permission) => $this->hasPermission($permission));
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(Role|string $role): void
    {
        $roleId = $role instanceof Role ? $role->id : Role::where('slug', $role)->firstOrFail()->id;

        if (!$this->roles()->where('role_id', $roleId)->exists()) {
            $this->roles()->attach($roleId);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(Role|string $role): void
    {
        $roleId = $role instanceof Role ? $role->id : Role::where('slug', $role)->firstOrFail()->id;
        $this->roles()->detach($roleId);
    }

    /**
     * Sync user roles
     */
    public function syncRoles(array|string $roles): void
    {
        $roleIds = [];

        foreach ((array)$roles as $role) {
            $roleIds[] = $role instanceof Role ? $role->id : Role::where('slug', $role)->firstOrFail()->id;
        }

        $this->roles()->sync($roleIds);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user is parent
     */
    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }
}
