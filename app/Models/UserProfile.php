<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'photo',
        'profile_picture',
        'admission_number',
        'registration_token_id',
        'date_of_birth',
        'blood_group',
        'nationality',
        'state_of_origin',
        'school_class_id',
        'admission_date',
        'previous_school',
        'medical_conditions',
        'allergies',
        'medications',
        'emergency_medical_consent',
        'special_needs',
        'status',
        'parent_id',
        'relationship',
        'occupation',
        'qualification',
        'specialization',
        'employment_date',
        'date_joined',
        'subjects',
        'classes',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'employment_date' => 'date',
        'date_joined' => 'date',
        'allergies' => 'json',
        'subjects' => 'json',
        'classes' => 'json',
        'emergency_medical_consent' => 'boolean',
    ];

    /**
     * Get the user that owns this profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school class for student profiles
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the parent user if this is a student/ward
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the children (students) of this parent
     */
    public function children()
    {
        return $this->hasMany(UserProfile::class, 'parent_id', 'user_id');
    }

    /**
     * Get the registration token
     */
    public function registrationToken()
    {
        return $this->belongsTo(RegistrationToken::class);
    }

    /**
     * Get attendance records for this student
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_profile_id');
    }

    /**
     * Get students enrolled in classes taught by this teacher
     */
    public function students()
    {
        return $this->schoolClass?->students() ?? collect();
    }

    /**
     * Get classes assigned to this teacher
     */
    public function teacher_classes()
    {
        $assignedClasses = $this->getAssignedClasses();
        
        if (empty($assignedClasses)) {
            return SchoolClass::whereRaw('1 = 0'); // Return empty query
        }
        
        // Extract class names from "Name - Code" format
        $classNames = array_map(function($class) {
            return explode(' - ', $class)[0] ?? $class;
        }, $assignedClasses);
        
        return SchoolClass::whereIn('name', $classNames);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Check if user is a student
     */
    public function isStudent()
    {
        return !is_null($this->school_class_id) && !is_null($this->admission_number);
    }

    /**
     * Check if user is staff
     */
    public function isStaff()
    {
        return !is_null($this->occupation) && !is_null($this->employment_date);
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }
        return null;
    }

    /**
     * Scope: Filter by status
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope: Filter by school class
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    /**
     * Scope: Filter by occupation
     */
    public function scopeByOccupation($query, $occupation)
    {
        return $query->where('occupation', $occupation);
    }

    /**
     * Get assigned subjects - returns array or empty array
     */
    public function getAssignedSubjects()
    {
        if (is_null($this->subjects)) {
            return [];
        }
        return is_array($this->subjects) ? $this->subjects : [];
    }

    /**
     * Get assigned classes - returns array or empty array
     */
    public function getAssignedClasses()
    {
        if (is_null($this->classes)) {
            return [];
        }
        return is_array($this->classes) ? $this->classes : [];
    }
}
