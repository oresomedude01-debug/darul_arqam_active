<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * BillItem Model
 * 
 * Represents an individual line item on a student's bill.
 * 
 * This table stores the actual student invoices generated from fee_structure.
 * When a fee structure is saved/updated, the system automatically creates bill_items
 * for all students in that class.
 * 
 * A student's complete bill = all bill_items for that student + that session + that class
 * 
 * Example:
 * StudentID: 1, Session: 2024/2025, Class: JSS1
 * Line 1: Tuition = 45,000 (unpaid)
 * Line 2: ICT Fee = 3,000 (paid)
 * Line 3: Exam Fee = 2,000 (unpaid)
 * Total: 50,000 | Paid: 3,000 | Outstanding: 47,000
 */
class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',              // FK to user_profiles (the student being billed)
        'academic_session_id',     // Which session
        'school_class_id',         // Which class
        'fee_item_id',             // Which fee (FK to FeeItem)
        'fee_structure_id',        // Reference to the fee structure that created this
        'amount',                  // Amount owed for this line
        'paid_amount',             // Amount paid for this line (default 0)
        'status',                  // unpaid, paid, partial
        'due_date',                // When payment is due
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Relationship: Bill item belongs to a student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'student_id');
    }

    /**
     * Relationship: Bill item belongs to an academic session
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Relationship: Bill item belongs to a school class
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Relationship: Bill item references a fee item master record
     */
    public function feeItem(): BelongsTo
    {
        return $this->belongsTo(FeeItem::class);
    }

    /**
     * Relationship: Bill item references the fee structure that created it
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Relationship: A bill item can have many payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope: Get unpaid bill items
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope: Get paid bill items
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Get partially paid bill items
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope: Get bill items for a specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Get bill items for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope: Get bill items for a specific class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    /**
     * Get outstanding amount for this line
     */
    public function getOutstandingAmount(): float
    {
        return (float) ($this->amount - $this->paid_amount);
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentage(): float
    {
        if ($this->amount == 0) {
            return 0;
        }
        return ($this->paid_amount / $this->amount) * 100;
    }

    /**
     * Check if fully paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->getOutstandingAmount() <= 0;
    }

    /**
     * Check if partially paid
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial' && $this->paid_amount > 0;
    }

    /**
     * Get display status
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            default => $this->status,
        };
    }
}
