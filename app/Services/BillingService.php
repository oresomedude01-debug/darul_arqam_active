<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\BillItem;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

/**
 * BillingService
 * 
 * Handles automatic bill generation from fee structures.
 * When a fee structure is saved, this service automatically creates bill items
 * for all students in that class.
 */
class BillingService
{
    /**
     * Generate bills for a specific fee structure
     * 
     * This is called automatically when a FeeStructure is created/updated.
     * It creates a BillItem for each student currently enrolled in that class.
     * 
     * @param FeeStructure $feeStructure
     * @return int Number of bills created
     */
    public function generateBillsForFeeStructure(FeeStructure $feeStructure): int
    {
        $billsCreated = 0;

        // Get all students enrolled in this class for this session
        $students = $this->getStudentsForClass(
            $feeStructure->school_class_id,
            $feeStructure->academic_session_id
        );

        foreach ($students as $student) {
            // Check if a bill item already exists for this student/session/class/fee combination
            $existingBill = BillItem::where('student_id', $student->id)
                ->where('academic_session_id', $feeStructure->academic_session_id)
                ->where('school_class_id', $feeStructure->school_class_id)
                ->where('fee_item_id', $feeStructure->fee_item_id)
                ->first();

            if (!$existingBill) {
                // Create new bill item
                BillItem::create([
                    'student_id' => $student->id,
                    'academic_session_id' => $feeStructure->academic_session_id,
                    'school_class_id' => $feeStructure->school_class_id,
                    'fee_item_id' => $feeStructure->fee_item_id,
                    'fee_structure_id' => $feeStructure->id,
                    'amount' => $feeStructure->amount,
                    'status' => 'unpaid',
                ]);

                $billsCreated++;
            } else {
                // Update existing bill item with new amount if price changed
                if ($existingBill->amount != $feeStructure->amount) {
                    $existingBill->update(['amount' => $feeStructure->amount]);
                }
            }
        }

        return $billsCreated;
    }

    /**
     * Generate bills for all fee structures in a session/class
     * 
     * Called when all fee structures for a session/class are set.
     * Creates bill items for the given class in the given session.
     * 
     * @param int $sessionId
     * @param int $classId
     * @return int Total number of bills created
     */
    public function generateBillsForClass(int $sessionId, int $classId): int
    {
        $billsCreated = 0;

        // Get all active fee structures for this session/class combination
        $feeStructures = FeeStructure::where('academic_session_id', $sessionId)
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->get();

        foreach ($feeStructures as $feeStructure) {
            $billsCreated += $this->generateBillsForFeeStructure($feeStructure);
        }

        return $billsCreated;
    }

    /**
     * Get all students enrolled in a class for a specific session
     * 
     * @param int $classId
     * @param int $sessionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getStudentsForClass(int $classId, int $sessionId)
    {
        // Get students assigned to this class
        // UserProfile has school_class_id and active enrollment
        return UserProfile::where('school_class_id', $classId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Calculate total billed amount for a student in a session/class
     * 
     * @param int $studentId
     * @param int $sessionId
     * @param int|null $classId
     * @return float
     */
    public function getTotalBilledAmount(int $studentId, int $sessionId, ?int $classId = null): float
    {
        $query = BillItem::where('student_id', $studentId)
            ->where('academic_session_id', $sessionId);

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Calculate total paid amount for a student in a session/class
     * 
     * @param int $studentId
     * @param int $sessionId
     * @param int|null $classId
     * @return float
     */
    public function getTotalPaidAmount(int $studentId, int $sessionId, ?int $classId = null): float
    {
        $query = BillItem::where('student_id', $studentId)
            ->where('academic_session_id', $sessionId);

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        return (float) $query->sum('paid_amount');
    }

    /**
     * Calculate outstanding amount for a student in a session/class
     * 
     * @param int $studentId
     * @param int $sessionId
     * @param int|null $classId
     * @return float
     */
    public function getOutstandingAmount(int $studentId, int $sessionId, ?int $classId = null): float
    {
        $total = $this->getTotalBilledAmount($studentId, $sessionId, $classId);
        $paid = $this->getTotalPaidAmount($studentId, $sessionId, $classId);

        return $total - $paid;
    }

    /**
     * Waive a bill item (mark as not applicable for this student)
     * 
     * @param BillItem $billItem
     * @param string|null $reason
     * @return bool
     */
    public function waiveBill(BillItem $billItem, ?string $reason = null): bool
    {
        return $billItem->update([
            'status' => 'waived',
            'paid_amount' => $billItem->amount, // Mark as fully paid/waived
        ]);
    }

    /**
     * Get summary statistics for a session
     * 
     * @param int $sessionId
     * @return array
     */
    public function getSessionSummary(int $sessionId): array
    {
        $billItems = BillItem::where('academic_session_id', $sessionId)
            ->select(
                DB::raw('SUM(amount) as total_billed'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(amount - paid_amount) as total_outstanding')
            )
            ->first();

        return [
            'total_billed' => $billItems->total_billed ?? 0,
            'total_paid' => $billItems->total_paid ?? 0,
            'total_outstanding' => $billItems->total_outstanding ?? 0,
            'payment_rate' => $billItems->total_billed > 0
                ? round(($billItems->total_paid / $billItems->total_billed) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get summary statistics for a class in a session
     * 
     * @param int $sessionId
     * @param int $classId
     * @return array
     */
    public function getClassSummary(int $sessionId, int $classId): array
    {
        $billItems = BillItem::where('academic_session_id', $sessionId)
            ->where('school_class_id', $classId)
            ->select(
                DB::raw('SUM(amount) as total_billed'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(amount - paid_amount) as total_outstanding'),
                DB::raw('COUNT(DISTINCT student_id) as total_students')
            )
            ->first();

        return [
            'total_billed' => $billItems->total_billed ?? 0,
            'total_paid' => $billItems->total_paid ?? 0,
            'total_outstanding' => $billItems->total_outstanding ?? 0,
            'total_students' => $billItems->total_students ?? 0,
            'payment_rate' => $billItems->total_billed > 0
                ? round(($billItems->total_paid / $billItems->total_billed) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get debtors list (students with outstanding balances)
     * 
     * @param int $sessionId
     * @param int|null $classId
     * @return array
     */
    public function getDebtors(int $sessionId, ?int $classId = null): array
    {
        $query = BillItem::where('academic_session_id', $sessionId)
            ->where('status', '!=', 'waived');

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        $debtors = [];
        $students = $query->select('student_id')
            ->distinct()
            ->pluck('student_id');

        foreach ($students as $studentId) {
            $outstanding = $this->getOutstandingAmount($studentId, $sessionId, $classId);

            if ($outstanding > 0) {
                $debtors[] = [
                    'student_id' => $studentId,
                    'total_billed' => $this->getTotalBilledAmount($studentId, $sessionId, $classId),
                    'total_paid' => $this->getTotalPaidAmount($studentId, $sessionId, $classId),
                    'outstanding' => $outstanding,
                ];
            }
        }

        // Sort by outstanding amount (highest first)
        usort($debtors, function ($a, $b) {
            return $b['outstanding'] <=> $a['outstanding'];
        });

        return $debtors;
    }
}