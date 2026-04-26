<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Payment Model
 * 
 * Simple payment tracking:
 * - Records cash, transfer, or online payments against a bill
 * - Updates bill's paid_amount and balance_due
 * - Maintains payment history for auditing
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_bill_id',
        'student_id',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by',
        'paid_at',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_date' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to a bill
     */
    public function studentBill(): BelongsTo
    {
        return $this->belongsTo(StudentBill::class);
    }

    /**
     * Relationship: Payment belongs to a student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'student_id');
    }

    /**
     * Relationship: Payment was recorded by a user
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Relationship: Payment has a receipt
     */
    public function receipt()
    {
        return $this->hasOne(PaymentReceipt::class);
    }

    /**
     * Relationship: Payment's bill item (legacy compatibility)
     * Alias for studentBill relationship
     */
    public function billItem(): BelongsTo
    {
        return $this->belongsTo(StudentBill::class, 'student_bill_id');
    }

    /**
     * Scope: Get payments by method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Get payments in a date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('paid_at', [$from, $to]);
    }
}
