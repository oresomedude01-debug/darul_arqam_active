<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * StudentBill Model
 * 
 * Simple bill tracking:
 * - One bill per student per class/session/term
 * - Total amount from fee structure
 * - Paid amount updates with each payment
 * - Balance automatically calculated
 * - Status: pending → partial (if paid > 0) → paid (if balance ≤ 0)
 */
class StudentBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'academic_term_id',
        'school_class_id',
        'fee_structure_id',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'due_date',
        'notes',
        'description',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'due_date' => 'date',
    ];

    /**
     * Relationship: Bill belongs to a student profile
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'student_id');
    }

    /**
     * Relationship: Bill belongs to academic session
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Relationship: Bill belongs to academic term
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Relationship: Bill belongs to school class
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Relationship: Bill belongs to fee structure template
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Relationship: Bill has many payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship: Bill has many fee items (via fee structure) - for legacy compatibility
     * Returns fee structure items through the fee structure relationship
     */
    public function billItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            FeeStructureItem::class,
            FeeStructure::class,
            'id',                   // Foreign key on fee_structures table
            'fee_structure_id',     // Foreign key on fee_structure_items table
            'fee_structure_id',     // Local key on student_bills table
            'id'                    // Local key on fee_structures table
        );
    }

    /**
     * Record a payment and update bill status
     */
    public function recordPayment(float $amount, string $method = 'cash', ?string $reference = null, ?int $recordedBy = null, ?string $notes = null): Payment
    {
        $payment = Payment::create([
            'student_bill_id' => $this->id,
            'student_id' => $this->student_id,
            'amount' => $amount,
            'payment_method' => $method,
            'reference_number' => $reference,
            'recorded_by' => $recordedBy ?? auth()->id(),
            'notes' => $notes,
            'paid_at' => now(),
        ]);

        // Update bill totals
        $this->paid_amount += $amount;
        $this->balance_due = $this->total_amount - $this->paid_amount;

        // Update status
        if ($this->balance_due <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        $this->save();

        return $payment;
    }

    /**
     * Get fee structure items for display
     */
    public function getFeeItems()
    {
        return $this->feeStructure->items()->with('feeItem')->get();
    }

    /**
     * Scope: Get bills for a student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Get bills for a class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    /**
     * Scope: Get bills for a session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope: Get pending/partial bills
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    /**
     * Scope: Get paid bills only
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Get overdue bills
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->where('due_date', '<', now());
    }
}
