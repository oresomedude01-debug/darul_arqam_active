<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model with event listeners
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * When an academic session is deleted, delete all attached terms
         */
        static::deleting(function ($session) {
            $session->terms()->delete();
        });
    }

    /**
     * Relationship: An academic session has many terms
     */
    public function terms()
    {
        return $this->hasMany(AcademicTerm::class);
    }

    /**
     * Relationship: An academic session has many results
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'academic_session_id');
    }

    /**
     * Scope: Get active session
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->first();
    }

    /**
     * Get the active term within this session
     */
    public function getActiveTermAttribute()
    {
        return $this->terms()->where('is_active', true)->first();
    }

    /**
     * Get all terms ordered by position
     */
    public function getOrderedTermsAttribute()
    {
        return $this->terms()->orderBy('term')->get();
    }
}
