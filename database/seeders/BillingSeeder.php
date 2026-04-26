<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\SchoolClass;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n🔄 Seeding Billing System...\n";

        // 1. Create Academic Session
        $session = AcademicSession::firstOrCreate(
            ['session' => '2024/2025'],
            ['is_active' => true, 'description' => 'Academic Year 2024/2025']
        );
        echo "✓ Academic Session: {$session->session}\n";

        // 2. Create Academic Terms
        $term1 = AcademicTerm::firstOrCreate(
            [
                'academic_session_id' => $session->id,
                'term' => 'First Term',
            ],
            [
                'name' => 'First Term',
                'session' => '2024/2025',
                'start_date' => now()->startOfYear(),
                'end_date' => now()->startOfYear()->addMonths(3),
                'status' => 'ongoing',
                'is_active' => true,
            ]
        );
        echo "✓ Academic Term: {$term1->name}\n";

        // 3. Create Fee Items
        $feeItems = [
            ['name' => 'Tuition Fee', 'description' => 'Monthly tuition fee', 'is_optional' => false, 'default_amount' => 50000, 'status' => 'active'],
            ['name' => 'ICT Fee', 'description' => 'Information and Communication Technology', 'is_optional' => false, 'default_amount' => 5000, 'status' => 'active'],
            ['name' => 'Sports Fee', 'description' => 'Sports and extracurricular activities', 'is_optional' => true, 'default_amount' => 3000, 'status' => 'active'],
            ['name' => 'Laboratory Fee', 'description' => 'Science laboratory fee', 'is_optional' => false, 'default_amount' => 7000, 'status' => 'active'],
            ['name' => 'Library Fee', 'description' => 'Library and learning resources', 'is_optional' => true, 'default_amount' => 2000, 'status' => 'active'],
        ];

        $createdFeeItems = [];
        foreach ($feeItems as $item) {
            $feeItem = FeeItem::firstOrCreate(['name' => $item['name']], $item);
            $createdFeeItems[] = $feeItem;
            echo "✓ Fee Item: {$feeItem->name} (₦{$feeItem->default_amount})\n";
        }

        // 4. Create Fee Structure (Template)
        $feeStructure = FeeStructure::firstOrCreate(
            [
                'academic_session_id' => $session->id,
                'academic_term_id' => $term1->id,
                'name' => 'JSS1 Fee Structure - Term 1',
            ],
            [
                'description' => 'Fee structure for Junior Secondary School 1',
                'total_amount' => 0,
                'is_active' => true,
            ]
        );
        echo "✓ Fee Structure: {$feeStructure->name}\n";

        // 5. Attach Fee Items to Structure
        $totalAmount = 0;
        $order = 0;
        foreach ($createdFeeItems as $feeItem) {
            FeeStructureItem::firstOrCreate(
                [
                    'fee_structure_id' => $feeStructure->id,
                    'fee_item_id' => $feeItem->id,
                ],
                [
                    'amount' => $feeItem->default_amount,
                    'display_order' => $order++,
                ]
            );
            $totalAmount += $feeItem->default_amount;
        }

        // Update total amount
        $feeStructure->update(['total_amount' => $totalAmount]);
        echo "✓ Fee Structure Items attached (Total: ₦{$totalAmount})\n";

        // 6. Get or create a school class
        $schoolClass = SchoolClass::first();
        if (!$schoolClass) {
            $schoolClass = SchoolClass::firstOrCreate(
                ['name' => 'JSS1A'],
                ['section' => 'A', 'class_code' => 'JSS1A', 'capacity' => 40, 'status' => 'active']
            );
            echo "✓ School Class: {$schoolClass->name}\n";
        }

        // 7. Get or create user profiles for students
        $students = User::whereHas('roles', function ($query) {
            $query->where('name', 'Student');
        })->get();

        if ($students->isEmpty()) {
            echo "⚠️  No students found in the system.\n";
            echo "   Run: php artisan db:seed --class=UserSeeder first\n\n";
            return;
        }

        // Create profiles for students if they don't have one
        foreach ($students as $student) {
            \App\Models\UserProfile::firstOrCreate(
                ['user_id' => $student->id],
                [
                    'first_name' => explode(' ', $student->name)[0],
                    'last_name' => isset(explode(' ', $student->name)[1]) ? explode(' ', $student->name)[1] : 'Student',
                    'status' => 'active',
                ]
            );
        }

        // Get profiles that have been created
        $studentProfiles = \App\Models\UserProfile::whereIn('user_id', $students->pluck('id'))->limit(3)->get();

        if ($studentProfiles->isEmpty()) {
            echo "⚠️  Could not create student profiles.\n\n";
            return;
        }

        echo "\n✓ Creating Student Bills:\n";

        // 8. Create Student Bills
        foreach ($studentProfiles as $profile) {
            $bill = StudentBill::firstOrCreate(
                [
                    'student_id' => $profile->id,
                    'academic_session_id' => $session->id,
                    'academic_term_id' => $term1->id,
                    'fee_structure_id' => $feeStructure->id,
                ],
                [
                    'school_class_id' => $schoolClass->id,
                    'total_amount' => $feeStructure->total_amount,
                    'paid_amount' => 0,
                    'balance_due' => $feeStructure->total_amount,
                    'status' => 'pending',
                    'due_date' => now()->addMonths(1),
                    'notes' => 'First term fees',
                ]
            );

            echo "  - Bill ID #{$bill->id} for {$profile->user->name} (₦{$bill->total_amount})\n";

            // 9. Create sample payment for first bill
            if ($studentProfiles->first()->id === $profile->id) {
                $payment = Payment::firstOrCreate(
                    [
                        'student_bill_id' => $bill->id,
                        'student_id' => $profile->id,
                        'reference_number' => 'TRF-' . strtoupper(substr(uniqid(), -6)),
                    ],
                    [
                        'amount' => 25000,
                        'payment_method' => 'bank_transfer',
                        'notes' => 'Partial payment for first term fees',
                        'recorded_by' => 1,
                        'paid_at' => now(),
                        'payment_date' => now(),
                        'status' => 'completed',
                    ]
                );

                // Update bill status
                $bill->update([
                    'paid_amount' => $payment->amount,
                    'balance_due' => $bill->total_amount - $payment->amount,
                    'status' => 'partial',
                ]);

                echo "    ✓ Payment: ₦{$payment->amount} recorded\n";
            }
        }

        echo "\n✅ Billing system seeded successfully!\n\n";
    }
}
