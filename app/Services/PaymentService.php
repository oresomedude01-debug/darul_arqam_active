<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\BillItem;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

/**
 * PaymentService - Handles all payment processing and billing operations
 * 
 * This service centralizes payment business logic following international standards:
 * - Transaction atomicity and consistency via database transactions
 * - Comprehensive audit trails for all financial operations
 * - Proper error handling and logging
 * - Double-entry bookkeeping principles support
 * 
 * REFACTORED FOR BillItem-based billing:
 * - Payments now directly reference BillItem (student invoice line)
 * - A payment can cover multiple bill items or partial bill items
 * - Automatically updates BillItem.status (unpaid → paid/partial)
 * - No more StudentBill parent table
 */
class PaymentService
{
    /**
     * Record a payment against a specific bill item
     * 
     * @param BillItem $billItem
     * @param array $paymentData
     * @return Payment
     * @throws Exception
     */
    public function recordPayment(BillItem $billItem, array $paymentData): Payment
    {
        return DB::transaction(function () use ($billItem, $paymentData) {
            // Validate amount doesn't exceed outstanding balance
            $outstandingAmount = $billItem->getOutstandingAmount();
            
            if ($paymentData['amount'] > $outstandingAmount) {
                throw new Exception("Payment amount ({$paymentData['amount']}) exceeds outstanding balance ({$outstandingAmount})");
            }

            // Create payment record with transaction ID
            $payment = Payment::create([
                'transaction_id' => $this->generateTransactionId(),
                'student_id' => $billItem->student_id,
                'bill_item_id' => $billItem->id,
                'amount' => $paymentData['amount'],
                'currency' => $paymentData['currency'] ?? 'NGN',
                'payment_method' => $paymentData['payment_method'],
                'status' => $paymentData['status'] ?? 'pending',
                'remarks' => $paymentData['remarks'] ?? null,
                'recorded_by' => auth()->id(),
                'payment_date' => now(),
            ]);

            // Update bill item with payment amount
            $billItem->paid_amount = $billItem->paid_amount + $paymentData['amount'];
            
            // Update bill item status
            if ($billItem->paid_amount >= $billItem->amount) {
                $billItem->status = 'paid';
                $billItem->paid_amount = $billItem->amount; // Cap at full amount
            } elseif ($billItem->paid_amount > 0) {
                $billItem->status = 'partial';
            }
            
            $billItem->save();

            // Mark payment as verified
            $payment->update([
                'status' => 'completed',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Auto-generate receipt
            $this->generateReceipt($payment);

            return $payment;
        });
    }

    /**
     * Record payments for multiple bill items at once
     * 
     * Use this when a student makes one payment that covers multiple fees/bills
     * 
     * @param int $studentId
     * @param array $billItemPayments Array of ['bill_item_id' => id, 'amount' => amount]
     * @param array $paymentMeta Common payment metadata
     * @return array Array of Payment records created
     */
    public function recordMultiplePayments(int $studentId, array $billItemPayments, array $paymentMeta): array
    {
        return DB::transaction(function () use ($studentId, $billItemPayments, $paymentMeta) {
            $payments = [];

            foreach ($billItemPayments as $payment) {
                $billItem = BillItem::find($payment['bill_item_id']);

                if (!$billItem || $billItem->student_id != $studentId) {
                    throw new Exception("Invalid bill item or student mismatch");
                }

                $paymentData = array_merge($paymentMeta, [
                    'amount' => $payment['amount'],
                ]);

                $payments[] = $this->recordPayment($billItem, $paymentData);
            }

            return $payments;
        });
    }

    /**
     * Apply waiver/scholarship to a bill item
     * 
     * @param BillItem $billItem
     * @param array $waiverData
     * @return void
     * @throws Exception
     */
    public function applyWaiver(BillItem $billItem, array $waiverData): void
    {
        DB::transaction(function () use ($billItem, $waiverData) {
            $waiverAmount = $waiverData['amount'] ?? $billItem->getOutstandingAmount();

            if ($waiverAmount > $billItem->getOutstandingAmount()) {
                throw new Exception('Waiver amount exceeds outstanding balance');
            }

            // Create waiver record (essentially a payment with waiver method)
            $payment = Payment::create([
                'transaction_id' => $this->generateTransactionId(),
                'student_id' => $billItem->student_id,
                'bill_item_id' => $billItem->id,
                'amount' => $waiverAmount,
                'currency' => 'NGN',
                'payment_method' => 'waiver',
                'status' => 'completed',
                'remarks' => $waiverData['remarks'] ?? 'Fee waiver applied',
                'recorded_by' => auth()->id(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'payment_date' => now(),
            ]);

            // Update bill item
            $billItem->paid_amount = $billItem->amount;
            $billItem->status = 'paid';
            $billItem->save();

            // Auto-generate receipt
            $this->generateReceipt($payment);
        });
    }

    /**
     * Refund a payment (with audit trail)
     * 
     * @param Payment $payment
     * @param string $reason
     * @return Payment
     * @throws Exception
     */
    public function refundPayment(Payment $payment, string $reason): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {
            if ($payment->status === 'refunded') {
                throw new Exception('Payment has already been refunded');
            }

            if ($payment->status === 'pending') {
                throw new Exception('Cannot refund a pending payment');
            }

            // Create reversal transaction
            $reversal = Payment::create([
                'transaction_id' => $this->generateTransactionId(),
                'student_id' => $payment->student_id,
                'bill_item_id' => $payment->bill_item_id,
                'amount' => -$payment->amount, // Negative indicates refund
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'status' => 'refunded',
                'remarks' => "Refund for transaction {$payment->transaction_id}: {$reason}",
                'recorded_by' => auth()->id(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'payment_date' => now(),
            ]);

            // Update original payment status
            $payment->update(['status' => 'refunded']);

            // Revert bill item amounts
            if ($billItem = $payment->billItem) {
                $billItem->paid_amount -= $payment->amount;
                
                // Recalculate bill item status
                if ($billItem->paid_amount <= 0) {
                    $billItem->status = 'unpaid';
                    $billItem->paid_amount = 0;
                } elseif ($billItem->paid_amount >= $billItem->amount) {
                    $billItem->status = 'paid';
                } else {
                    $billItem->status = 'partial';
                }
                
                $billItem->save();
            }

            return $reversal;
        });
    }

    /**
     * Generate unique transaction ID
     * 
     * @return string
     */
    private function generateTransactionId(): string
    {
        return 'TXN-' . date('YmdHis') . '-' . Str::random(6);
    }

    /**
     * Generate unique receipt number
     * 
     * @return string
     */
    private function generateReceiptNumber(): string
    {
        return 'RCP-' . date('Y') . '-' . Str::random(8);
    }

    /**
     * Generate payment receipt
     * 
     * @param Payment $payment
     * @return PaymentReceipt
     */
    public function generateReceipt(Payment $payment): PaymentReceipt
    {
        return PaymentReceipt::create([
            'receipt_number' => $this->generateReceiptNumber(),
            'payment_id' => $payment->id,
            'issued_by' => auth()->id(),
            'notes' => "Receipt for payment on {$payment->payment_date->format('Y-m-d H:i')}",
        ]);
    }

    /**
     * Get payment history for a student
     * 
     * @param int $studentId
     * @return mixed
     */
    public function getPaymentHistory(int $studentId)
    {
        return Payment::where('student_id', $studentId)
            ->whereIn('status', ['completed', 'refunded'])
            ->with(['billItem', 'receipt'])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Get outstanding balance for a student
     * 
     * @param int $studentId
     * @return float
     */
    public function getOutstandingBalance(int $studentId): float
    {
        return BillItem::where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->sum(DB::raw('amount - paid_amount'));
    }

    /**
     * Get outstanding balance for a student in a specific session
     * 
     * @param int $studentId
     * @param int $sessionId
     * @return float
     */
    public function getOutstandingBalanceForSession(int $studentId, int $sessionId): float
    {
        return BillItem::where('student_id', $studentId)
            ->where('academic_session_id', $sessionId)
            ->where('status', '!=', 'paid')
            ->sum(DB::raw('amount - paid_amount'));
    }
}
