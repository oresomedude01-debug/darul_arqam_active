<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_session_id',
        'name',
        'session',
        'term',
        'start_date',
        'end_date',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Boot the model with event listeners
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * When an academic term is deleted, delete all attached events and results
         */
        static::deleting(function ($term) {
            $term->events()->delete();
            $term->results()->delete();
        });
    }

    // Relationships
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'academic_term_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')->orderBy('start_date');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')->orderBy('start_date', 'desc');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'upcoming' => 'badge-info',
            'ongoing' => 'badge-success',
            'completed' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    public function getDurationDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Calculate the number of school opening days within the term
     * based on school_days setting (e.g., Monday-Friday)
     */
    public function getSchoolOpeningDaysAttribute()
    {
        $schoolSettings = SchoolSetting::getInstance();
        $schoolDays = $schoolSettings->getOperatingDays();
        
        $count = 0;
        $current = $this->start_date->copy();
        
        while ($current <= $this->end_date) {
            $dayName = $current->format('l'); // Get day name (Monday, Tuesday, etc.)
            
            if (in_array($dayName, $schoolDays)) {
                $count++;
            }
            
            $current->addDay();
        }
        
        return $count;
    }
}
