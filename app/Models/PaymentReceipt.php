<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'receipt_number',
        'status',
        'notes',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
