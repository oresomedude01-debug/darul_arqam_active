<?php

namespace Database\Seeders;

use App\Models\FeeStructure;
use App\Models\FeeItem;
use App\Models\StudentBill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Get or create academic session
        $session = AcademicSession::firstOrCreate(
            ['session' => '2024/2025'],
            ['is_active' => true, 'description' => 'Academic Year 2024/2025']
        );

        // Get school classes
        $classes = SchoolClass::all();

        if ($classes->isEmpty()) {
            $this->command->warn('No school classes found. Skipping payment seeding.');
            return;
        }

        $this->command->info('Seeding fee structures...');
        $this->seedFeeStructures($classes, $session);

        $this->command->info('Seeding student bills...');
        $this->seedStudentBills($session);

        $this->command->info('Seeding sample payments...');
        $this->seedPayments();

        $this->command->info('✅ Payment seeding completed!');
    }

    /**
     * Seed fee structures
     */
    private function seedFeeStructures($classes, $session): void
    {
        $feeTypes = [
            [
                'name' => 'Tuition Fee',
                'type' => 'tuition',
                'description' => 'Regular tuition fee for academic session',
                'amount' => 150000,
            ],
            [
                'name' => 'Registration Fee',
                'type' => 'registration',
                'description' => 'One-time registration fee',
                'amount' => 10000,
            ],
            [
                'name' => 'Uniform & Books',
                'type' => 'uniform',
                'description' => 'School uniform and textbooks',
                'amount' => 50000,
            ],
            [
                'name' => 'Technology Fee',
                'type' => 'technology',
                'description' => 'Computer lab and IT resources',
                'amount' => 25000,
            ],
        ];

        foreach ($classes as $class) {
            foreach ($feeTypes as $fee) {
                FeeStructure::firstOrCreate(
                    [
                        'school_class_id' => $class->id,
                        'academic_session_id' => $session->id,
                        'name' => "{$fee['name']} - {$class->name}",
                    ],
                    [
                        'type' => $fee['type'],
                        'description' => $fee['description'],
                        'amount' => $fee['amount'],
                        'currency' => 'NGN',
                        'due_date' => now()->addMonths(2),
                        'installments' => 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info("Created fee structures for " . $classes->count() . " classes");
    }

    /**
     * Seed student bills
     */
    private function seedStudentBills($session): void
    {
        // Get students from user_profiles where school_class_id is not null
        $students = UserProfile::whereNotNull('school_class_id')
            ->limit(20)
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Skipping bill seeding.');
            return;
        }

        foreach ($students as $student) {
            // Check if bill already exists
            $existingBill = StudentBill::where('student_id', $student->id)
                ->where('academic_session_id', $session->id)
                ->first();

            if ($existingBill) {
                continue;
            }

            // Get fee structures for this class
            $feeStructures = FeeStructure::where('school_class_id', $student->school_class_id)
                ->where('academic_session_id', $session->id)
                ->get();

            if ($feeStructures->isEmpty()) {
                continue;
            }

            // Calculate total
            $totalAmount = $feeStructures->sum('amount');

            // Create bill
            $studentName = $student->full_name ?? $student->name;
            $bill = StudentBill::create([
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
                'academic_term_id' => null,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'outstanding_amount' => $totalAmount,
                'status' => 'pending',
                'due_date' => now()->addMonths(2),
                'remarks' => "Bill for {$studentName}",
            ]);

            // Create bill items
            foreach ($feeStructures as $fee) {
                BillItem::create([
                    'student_bill_id' => $bill->id,
                    'fee_structure_id' => $fee->id,
                    'description' => $fee->name,
                    'amount' => $fee->amount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]);
            }
        }

        $this->command->info("Created bills for " . $students->count() . " students");
    }

    /**
     * Seed sample payments
     */
    private function seedPayments(): void
    {
        $bills = StudentBill::where('status', 'pending')
            ->limit(10)
            ->get();

        if ($bills->isEmpty()) {
            $this->command->warn('No bills found. Skipping payment seeding.');
            return;
        }

        $paymentMethods = ['cash', 'bank_transfer', 'cheque', 'card'];
        $paymentCount = 0;

        foreach ($bills as $bill) {
            // Simulate different payment scenarios
            $scenario = rand(1, 4);

            match ($scenario) {
                1 => $this->createFullPayment($bill, $paymentMethods),      // Fully paid
                2 => $this->createPartialPayment($bill, $paymentMethods),   // Partially paid
                3 => $this->createWaiverPayment($bill),                     // Waived
                default => null,                                             // No payment
            };

            $paymentCount++;
        }

        $this->command->info("Created sample payments for $paymentCount bills");
    }

    /**
     * Create full payment
     */
    private function createFullPayment($bill, $paymentMethods): void
    {
        $method = $paymentMethods[array_rand($paymentMethods)];
        $amount = $bill->outstanding_amount;

        Payment::create([
            'transaction_id' => $this->generateTransactionId(),
            'student_id' => $bill->student_id,
            'student_bill_id' => $bill->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'payment_method' => $method,
            'status' => 'completed',
            'remarks' => "Full payment via $method",
            'recorded_by' => 1,
            'payment_date' => now()->subDays(rand(1, 30)),
            'verified_at' => now()->subDays(rand(0, 30)),
            'verified_by' => 1,
        ]);

        // Update bill
        $bill->update([
            'paid_amount' => $amount,
            'outstanding_amount' => 0,
            'status' => 'paid',
        ]);

        // Update bill items
        BillItem::where('student_bill_id', $bill->id)->update([
            'status' => 'paid',
            'paid_amount' => DB::raw('amount'),
        ]);
    }

    /**
     * Create partial payment
     */
    private function createPartialPayment($bill, $paymentMethods): void
    {
        $method = $paymentMethods[array_rand($paymentMethods)];
        $amount = $bill->outstanding_amount * 0.6; // 60% paid

        Payment::create([
            'transaction_id' => $this->generateTransactionId(),
            'student_id' => $bill->student_id,
            'student_bill_id' => $bill->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'payment_method' => $method,
            'status' => 'completed',
            'remarks' => "Partial payment via $method",
            'recorded_by' => 1,
            'payment_date' => now()->subDays(rand(1, 30)),
            'verified_at' => now()->subDays(rand(0, 30)),
            'verified_by' => 1,
        ]);

        // Update bill
        $bill->update([
            'paid_amount' => $amount,
            'outstanding_amount' => $bill->total_amount - $amount,
            'status' => 'partial',
        ]);

        // Update bill items (proportional)
        $paidPercentage = $amount / $bill->total_amount;
        BillItem::where('student_bill_id', $bill->id)->each(function ($item) use ($paidPercentage) {
            $item->update([
                'paid_amount' => $item->amount * $paidPercentage,
                'status' => $paidPercentage >= 1 ? 'paid' : 'partial',
            ]);
        });
    }

    /**
     * Create waiver payment
     */
    private function createWaiverPayment($bill): void
    {
        $waiverAmount = $bill->outstanding_amount * 0.5; // 50% waived

        Payment::create([
            'transaction_id' => $this->generateTransactionId(),
            'student_id' => $bill->student_id,
            'student_bill_id' => $bill->id,
            'amount' => -$waiverAmount, // Negative for waiver
            'currency' => 'NGN',
            'payment_method' => 'waiver',
            'status' => 'completed',
            'remarks' => 'Merit scholarship - 50% fee reduction',
            'recorded_by' => 1,
            'payment_date' => now()->subDays(rand(1, 30)),
            'verified_at' => now()->subDays(rand(0, 30)),
            'verified_by' => 1,
        ]);

        // Update bill
        $bill->update([
            'outstanding_amount' => $bill->outstanding_amount - $waiverAmount,
            'status' => 'partial',
        ]);
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId(): string
    {
        return 'TXN-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    }
}
