<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * FeeStructure Model
 * 
 * A reusable template for defining fees. Can be applied to any class or individual student.
 * 
 * Unlike class-based pricing, this is a flexible template that can be:
 * - Used for a specific academic session/term
 * - Cloned and modified for different classes
 * - Applied dynamically when generating bills
 * - Reused across multiple classes with the same fee structure
 * 
 * Example:
 * Name: "Standard Tuition 2024/2025"
 * Session: 2024/2025 (optional - can be generic template)
 * Items: Tuition (45,000), ICT (3,000), Exam (2,000)
 * Total: 50,000
 * 
 * When billing a class or student, this template is applied to generate individual bills.
 */
class FeeStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',                     // Template name (e.g., "Standard Tuition 2024/2025")
        'description',              // What this template is for
        'academic_session_id',      // Optional: if tied to specific session
        'academic_term_id',         // Optional: if tied to specific term
        'total_amount',             // Auto-calculated from items
        'is_active',                // Can be disabled
        'notes',                    // Additional notes
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Fee structure has many fee items through structure items
     */
    public function items(): HasMany
    {
        return $this->hasMany(FeeStructureItem::class);
    }

    /**
     * Relationship: Fee structure belongs to academic session (optional)
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Relationship: Fee structure belongs to academic term (optional)
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Relationship: Student bills that use this template
     */
    public function studentBills(): HasMany
    {
        return $this->hasMany(StudentBill::class);
    }

    /**
     * Get all fee items in this structure
     */
    public function getFeeItems()
    {
        return $this->items()->with('feeItem')->get();
    }

    /**
     * Recalculate total amount from items
     */
    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()
            ->sum('amount');
        $this->save();
    }

    /**
     * Scope: Get active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get templates for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId)
                     ->orWhereNull('academic_session_id'); // Include session-agnostic templates
    }

    /**
     * Scope: Get templates for a specific term
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('academic_term_id', $termId)
                     ->orWhereNull('academic_term_id'); // Include term-agnostic templates
    }
}
