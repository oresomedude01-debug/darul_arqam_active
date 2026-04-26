<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceComplaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attendance_id',
        'user_profile_id',
        'complaint_date',
        'reason',
        'evidence',
        'status', // pending, resolved, rejected
        'admin_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'complaint_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the attendance record this complaint is about
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Get the student who made this complaint
     */
    public function student()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    /**
     * Get the admin who resolved this complaint
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
