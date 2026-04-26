<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'description',
        'color',
        'affected_classes',
        'academic_term_id',
        'created_by',
        'is_auto_acknowledged',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'affected_classes' => 'array',
        'is_auto_acknowledged' => 'boolean',
    ];

    // Relationships
    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function creator()
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())->orderBy('start_date');
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'holiday' => 'Holiday',
            'exam' => 'Exam',
            'break' => 'Break',
            'meeting' => 'Meeting',
            'celebration' => 'Celebration',
            'term_begin' => 'Term Begins',
            'term_end' => 'Term Ends',
            'other' => 'Other',
            default => ucfirst($this->type)
        };
    }

    public function getTypeColorAttribute()
    {
        if ($this->color) {
            return $this->color;
        }

        return match($this->type) {
            'holiday' => '#f59e0b', // orange
            'exam' => '#8b5cf6', // purple
            'break' => '#ef4444', // red
            'meeting' => '#3b82f6', // blue
            'celebration' => '#06b6d4', // cyan
            'term_begin' => '#10b981', // green
            'term_end' => '#dc2626', // dark red
            'other' => '#6b7280', // gray
            default => '#6b7280' // gray
        };
    }

    public function getIsMultiDayAttribute()
    {
        return $this->end_date && $this->start_date->ne($this->end_date);
    }
}
