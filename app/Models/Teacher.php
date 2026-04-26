<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'state',
        'country',
        'qualification',
        'subjects',
        'classes',
        'date_joined',
        'profile_picture',
        'status',
        'salary',
        'notes',
    ];

    protected $casts = [
        'subjects' => 'array',
        'classes' => 'array',
        'date_of_birth' => 'date',
        'date_joined' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the teacher's full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the subjects as a comma-separated string
     */
    public function getSubjectsListAttribute()
    {
        return is_array($this->subjects) ? implode(', ', $this->subjects) : '';
    }

    /**
     * Get the classes as a comma-separated string
     */
    public function getClassesListAttribute()
    {
        return is_array($this->classes) ? implode(', ', $this->classes) : '';
    }

    /**
     * Scope a query to only include active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by subject
     */
    public function scopeBySubject($query, $subject)
    {
        return $query->whereJsonContains('subjects', $subject);
    }

    /**
     * Scope a query to filter by class
     */
    public function scopeByClass($query, $class)
    {
        return $query->whereJsonContains('classes', $class);
    }
}
