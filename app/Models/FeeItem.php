<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FeeItem Model
 * 
 * Master list of all fee categories the school charges.
 * Examples: Tuition, ICT Fee, Exam Fee, Uniform, Books, etc.
 * 
 * This table defines WHAT fees exist (not how much or which class).
 * It is NOT attached to any class or session.
 */
class FeeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',              // e.g., "Tuition", "ICT Fee", "Exam Fee"
        'description',       // Optional description
        'is_optional',       // Whether this fee is optional or mandatory
        'default_amount',    // Default amount (can be overridden per class/session)
        'status',            // active or inactive
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'default_amount' => 'decimal:2',
    ];

    /**
     * Relationship: A fee item can appear in many fee structures
     */
    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Relationship: A fee item can appear in many bill items
     */
    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Scope: Get only active fee items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get only optional fee items
     */
    public function scopeOptional($query)
    {
        return $query->where('is_optional', true);
    }

    /**
     * Scope: Get only mandatory fee items
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_optional', false);
    }

    /**
     * Check if fee item is mandatory
     */
    public function isMandatory(): bool
    {
        return !$this->is_optional;
    }

    /**
     * Get display name with amount if available
     */
    public function getDisplayName(?float $amount = null): string
    {
        if ($amount !== null) {
            return "{$this->name} (₦" . number_format($amount, 2) . ")";
        }
        return $this->name;
    }
}
