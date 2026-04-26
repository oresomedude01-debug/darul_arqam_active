<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegistrationToken extends Model
{
    protected $fillable = [
        'code',
        'status',
        'session_year',
        'class_level',
        'note',
        'expires_at',
        'student_id',
        'consumed_at',
        'consumed_by_ip',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    /**
     * Generate a unique token code
     */
    public static function generateTokenCode(): string
    {
        do {
            // Format: DAREG-YYYYMM-XXXXX (e.g., DAREG-202511-A1B2C)
            $token = 'DAREG-' . date('Ym') . '-' . strtoupper(Str::random(5));
        } while (self::where('code', $token)->exists());

        return $token;
    }

    /**
     * Check if token is valid for use
     */
    public function isValid(): bool
    {
        // Check status
        if ($this->status !== 'active') {
            return false;
        }

        // Check if already consumed
        if ($this->student_id !== null) {
            return false;
        }

        // Check expiry
        if ($this->expires_at && Carbon::now()->isAfter($this->expires_at)) {
            $this->update(['status' => 'expired']);
            return false;
        }

        return true;
    }

    /**
     * Mark token as consumed
     */
    public function markAsConsumed(int $studentId, string $ip = null): void
    {
        $this->update([
            'status' => 'consumed',
            'student_id' => $studentId,
            'consumed_at' => now(),
            'consumed_by_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function student()
    {
        return $this->user->profile ?? null;
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_level');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeConsumed($query)
    {
        return $query->where('status', 'consumed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeForSession($query, string $session)
    {
        return $query->where('session_year', $session);
    }
}
