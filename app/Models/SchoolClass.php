<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'section',
        'class_code',
        'teacher_id',
        'subject_teachers',
        'capacity',
        'room_number',
        'academic_year',
        'status',
        'description',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'subject_teachers' => 'array',
        'capacity' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the full class name with section
     */
    public function getFullNameAttribute(): string
    {
        return $this->section
            ? "{$this->name} - {$this->section}"
            : $this->name;
    }

    /**
     * Get the current enrollment (count of students in this class)
     */
    public function getCurrentEnrollmentAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Get the enrollment percentage
     */
    public function getEnrollmentPercentageAttribute(): float
    {
        if ($this->capacity == 0) {
            return 0;
        }
        $enrollment = $this->students()->count();
        return round(($enrollment / $this->capacity) * 100, 1);
    }

    /**
     * Get available seats
     */
    public function getAvailableSeatsAttribute(): int
    {
        $enrollment = $this->students()->count();
        return max(0, $this->capacity - $enrollment);
    }

    /**
     * Check if class is full
     */
    public function getIsFullAttribute(): bool
    {
        $enrollment = $this->students()->count();
        return $enrollment >= $this->capacity;
    }

    /**
     * Relationship: Class Teacher (Primary class teacher)
     * This is the primary teacher assigned to manage the class
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relationship: Students (UserProfiles assigned to this class)
     */
    public function students()
    {
        return $this->hasMany(UserProfile::class, 'school_class_id');
    }

    /**
     * Relationship: Subjects (many-to-many)
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'school_class_id', 'subject_id')
            ->withPivot('teacher_id', 'periods_per_week')
            ->withTimestamps();
    }

    /**
     * Relationship: Timetable entries
     */
    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'school_class_id');
    }

    /**
     * Get the primary class teacher as a UserProfile
     * Shorthand method for accessing teacher profile information
     */
    public function getClassTeacherAttribute()
    {
        return $this->teacher?->profile;
    }

    /**
     * Check if a teacher is assigned to this class
     */
    public function hasTeacher(User $user): bool
    {
        return $this->teacher_id === $user->id;
    }

    /**
     * Scope: Active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('section', 'like', "%{$search}%")
                ->orWhere('class_code', 'like', "%{$search}%")
                ->orWhere('room_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by teacher
     */
    public function scopeByTeacher($query, int $teacherId)
    {
        // Filter classes by teacher_id
        return $query->where('teacher_id', $teacherId);
    }
}