<?php

namespace App\Http\Controllers;

use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use App\Models\UserProfile;
use App\Mail\ParentBillNotification;
use App\Mail\PaymentReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BillingController extends Controller
{
    // ============ FEE STRUCTURES (TEMPLATES) ============

    /**
     * List all fee structure templates
     */
    public function feeStructures(): View
    {
        $structures = FeeStructure::with('items.feeItem', 'academicSession')
            ->orderBy('name')
            ->get();
        return view('billing.fee-structures.index', compact('structures'));
    }

    /**
     * Create new fee structure template
     */
    public function createFeeStructure(): View
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        $terms = AcademicTerm::all();
        $feeItems = FeeItem::where('status', 'active')->get();
        return view('billing.fee-structures.create', compact('sessions', 'terms', 'feeItems'));
    }

    /**
     * Store new fee structure template
     */
    public function storeFeeStructure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fee_structures,name',
            'description' => 'nullable|string',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'fee_items' => 'required|array|min:1',
            'fee_items.*' => 'exists:fee_items,id',
            'amounts.*' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Create structure
            $structure = FeeStructure::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'academic_session_id' => $validated['academic_session_id'],
                'academic_term_id' => $validated['academic_term_id'],
                'is_active' => true,
            ]);

            // Add fee items to structure
            $totalAmount = 0;
            foreach ($validated['fee_items'] as $index => $feeItemId) {
                $amount = (float) ($request->input('amounts')[$index] ?? 0);
                
                FeeStructureItem::create([
                    'fee_structure_id' => $structure->id,
                    'fee_item_id' => $feeItemId,
                    'amount' => $amount,
                    'display_order' => $index,
                ]);
                
                $totalAmount += $amount;
            }

            // Update total
            $structure->update(['total_amount' => $totalAmount]);

            DB::commit();
            return redirect()->route('billing.fee-structures.index')
                ->with('success', 'Fee structure template created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating fee structure: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============ BILLING - GENERATE BILLS ============

    /**
     * Show form to generate bills for a class
     */
    public function generateBillsForm(): View
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        $terms = AcademicTerm::all();
        $classes = SchoolClass::orderBy('name')->get();
        $feeStructures = FeeStructure::where('is_active', true)->get();
        
        // Fetch only students (users with 'student' role)
        $students = UserProfile::whereHas('user.roles', function ($query) {
            $query->where('roles.name', 'student');
        })
        ->with('schoolClass')
        ->orderBy('first_name')
        ->get();
        
        return view('billing.generate-bills', compact('sessions', 'terms', 'classes', 'feeStructures', 'students'));
    }

    /**
     * Generate bills for a class using a template
     */
    public function generateBills(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $structure = FeeStructure::find($validated['fee_structure_id']);
            $class = SchoolClass::find($validated['school_class_id']);

            // Get all students in class
            $students = UserProfile::where('school_class_id', $class->id)->get();

            $billsCreated = 0;
            foreach ($students as $student) {
                // Check if bill already exists
                $existing = StudentBill::where('student_id', $student->id)
                    ->where('academic_session_id', $validated['academic_session_id'])
                    ->where('academic_term_id', $validated['academic_term_id'])
                    ->where('school_class_id', $class->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                // Create bill
                StudentBill::create([
                    'student_id' => $student->id,
                    'academic_session_id' => $validated['academic_session_id'],
                    'academic_term_id' => $validated['academic_term_id'],
                    'school_class_id' => $class->id,
                    'fee_structure_id' => $structure->id,
                    'total_amount' => $structure->total_amount,
                    'paid_amount' => 0,
                    'balance_due' => $structure->total_amount,
                    'status' => 'pending',
                    'due_date' => $validated['due_date'],
                ]);

                $billsCreated++;
            }

            DB::commit();

            // Send emails to parents and get feedback
            $emailResult = $this->sendBillNotificationsToParents($class->id, $validated['academic_session_id']);

            // Build feedback message
            $message = "Generated {$billsCreated} bill(s) for {$class->name}. ";
            $message .= "Bill notifications queued for {$emailResult['sent']} parent(s).";
            if ($emailResult['failed'] > 0) {
                $message .= " ({$emailResult['failed']} notifications failed to queue)";
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('failedParents', $emailResult['failed'] > 0 ? $emailResult['failedParents'] : null);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate individual bill for a single student with selected fee structure
     */
    public function generateIndividualBill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:user_profiles,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'nullable|exists:academic_terms,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'description' => 'nullable|string|max:500',
            'due_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $student = UserProfile::find($validated['student_id']);
            if (!$student) {
                throw new \Exception('Student not found');
            }

            // Check if student is assigned to a class
            if (!$student->school_class_id) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', "{$student->first_name} {$student->last_name} is not assigned to a class. Please assign the student to a class before creating a bill.")
                    ->withInput();
            }

            $feeStructure = FeeStructure::find($validated['fee_structure_id']);
            if (!$feeStructure) {
                throw new \Exception('Fee structure not found');
            }

            // Create bill using selected fee structure
            $bill = StudentBill::create([
                'student_id' => $student->id,
                'academic_session_id' => $validated['session_id'],
                'academic_term_id' => $validated['term_id'],
                'school_class_id' => $student->school_class_id,
                'fee_structure_id' => $feeStructure->id,
                'total_amount' => $feeStructure->total_amount,
                'paid_amount' => 0,
                'balance_due' => $feeStructure->total_amount,
                'status' => 'pending',
                'due_date' => $validated['due_date'] ?? null,
                'description' => $validated['description'] ?? $feeStructure->name,
            ]);

            DB::commit();
            
            \Log::info('Individual bill created', [
                'bill_id' => $bill->id,
                'student_id' => $student->id,
                'amount' => $feeStructure->total_amount
            ]);

            // Send email to parent and get feedback
            $emailResult = $this->sendBillNotificationToParent($student);

            // Build feedback message
            $message = "Individual bill of ₦" . number_format($feeStructure->total_amount, 2) . " created for {$student->first_name} {$student->last_name}!";
            if ($emailResult['sent'] > 0) {
                $message .= " Bill notification queued to parent.";
            }
            if ($emailResult['failed'] > 0) {
                $message .= " (Warning: Failed to queue notification to parent)";
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('failedParents', $emailResult['failed'] > 0 ? $emailResult['failedParents'] : null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating individual bill', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error creating bill: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============ BILLS - VIEW & MANAGE ============

    /**
     * View student bills
     */
    public function studentBills(UserProfile $student): View
    {
        $bills = StudentBill::where('student_id', $student->id)
            ->with('academicSession', 'academicTerm', 'feeStructure')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('billing.student-bills', compact('student', 'bills'));
    }

    /**
     * View single bill with payments
     */
    public function viewBill(StudentBill $bill): View
    {
        $bill->load('student', 'academicSession', 'feeStructure.items.feeItem', 'payments');
        return view('billing.view-bill', compact('bill'));
    }

    // ============ PAYMENTS ============

    /**
     * Record a payment for a bill
     */
    public function recordPayment(Request $request, StudentBill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bill->balance_due,
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online,waiver',
            'reference_number' => 'nullable|string|unique:payments,reference_number',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Record payment
            $bill->recordPayment(
                $validated['amount'],
                $validated['payment_method'],
                $validated['reference_number'],
                auth()->id(),
                $validated['notes']
            );

            DB::commit();
            return redirect()->back()
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    // ============ REPORTS ============

    /**
     * View payment history/reports
     */
    public function paymentHistory(Request $request): View
    {
        $query = Payment::with('studentBill.student', 'studentBill.schoolClass');

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->where('paid_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('paid_at', '<=', $request->to_date . ' 23:59:59');
        }

        $payments = $query->orderBy('paid_at', 'desc')->paginate(50);
        
        return view('billing.payment-history', compact('payments'));
    }

    /**
     * Debt management report
     */
    public function debtManagement(Request $request): View
    {
        $query = StudentBill::with('student', 'schoolClass', 'academicSession')
            ->whereIn('status', ['pending', 'partial', 'overdue']);

        if ($request->filled('school_class_id')) {
            $query->where('school_class_id', $request->school_class_id);
        }

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        $bills = $query->orderBy('balance_due', 'desc')->paginate(50);
        $totalOutstanding = StudentBill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum('balance_due');

        return view('billing.debt-management', compact('bills', 'totalOutstanding'));
    }

    // ============ EMAIL NOTIFICATIONS ============

    /**
     * Send bill notification to a single student's parent
     * Returns array with success count and failed parents list
     */
    private function sendBillNotificationToParent(UserProfile $student): array
    {
        $result = [
            'sent' => 0,
            'failed' => 0,
            'failedParents' => []
        ];

        if (!$student->user || !$student->parent) {
            return $result; // No parent user or no parent assigned
        }

        $parentUser = $student->parent;
        
        // Get all recent bills for this parent's children
        $parentChildren = UserProfile::where('parent_id', $parentUser->id)->pluck('id');

        $recentBills = StudentBill::whereIn('student_id', $parentChildren)
            ->where('created_at', '>=', now()->subHours(1))
            ->with('student', 'student.schoolClass')
            ->get();

        if ($recentBills->isEmpty()) {
            return $result;
        }

        try {
            Mail::send(new ParentBillNotification($parentUser, $recentBills));
            $result['sent'] = 1;
            \Log::info('Successfully sent bill notification to parent: ' . $parentUser->email);
        } catch (\Exception $e) {
            $result['failed'] = 1;
            $result['failedParents'][] = $parentUser->email;
            \Log::error('Failed to queue bill notification to parent ' . $parentUser->email . ': ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Send bill notifications to all parents of a class
     * Returns array with success count and failed parents list
     */
    private function sendBillNotificationsToParents(int $classId, int $sessionId): array
    {
        $result = [
            'sent' => 0,
            'failed' => 0,
            'failedParents' => []
        ];

        // Get all students in the class with their user and parent information
        $students = UserProfile::where('school_class_id', $classId)
            ->with(['user', 'parent'])  // Load user and parent relationships
            ->get();

        // Group by parent and send one email per parent
        $parentBills = [];
        
        foreach ($students as $student) {
            if (!$student->user || !$student->parent) {
                continue;
            }

            $parentUser = $student->parent;  // Get parent from the profile relationship
            $parentId = $parentUser->id;

            // Get all bills for this student in this session
            $bills = StudentBill::where('student_id', $student->id)
                ->where('academic_session_id', $sessionId)
                ->with('student', 'student.schoolClass', 'feeStructure')
                ->get();

            if ($bills->isNotEmpty()) {
                if (!isset($parentBills[$parentId])) {
                    $parentBills[$parentId] = [
                        'parent' => $parentUser,
                        'bills' => collect(),
                    ];
                }

                $parentBills[$parentId]['bills'] = $parentBills[$parentId]['bills']->merge($bills);
            }
        }

        // Queue one email to each parent and track results
        foreach ($parentBills as $parentData) {
            try {
                $parentEmail = $parentData['parent']->email;
                
                if (!$parentEmail) {
                    throw new \Exception('Parent has no email address');
                }

                Mail::send(new ParentBillNotification($parentData['parent'], $parentData['bills']));
                $result['sent']++;
                \Log::info('Successfully queued bill notification to parent: ' . $parentEmail . ' with ' . $parentData['bills']->count() . ' bills');
            } catch (\Exception $e) {
                $result['failed']++;
                $parentEmail = $parentData['parent']->email ?? 'Unknown';
                $result['failedParents'][] = $parentEmail;
                \Log::error('Failed to queue bill notification to parent ' . $parentEmail . ': ' . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Send payment reminders to all parents with outstanding bills
     */
    public function sendPaymentReminders(): RedirectResponse
    {
        // Get all outstanding bills with student and parent data
        $outstandingBills = StudentBill::where('balance_due', '>', 0)
            ->with(['student' => function ($query) {
                $query->with(['user', 'parent', 'schoolClass']);  // Load user, parent, and schoolClass
            }])
            ->get();

        \Log::info('Payment reminders: Found ' . $outstandingBills->count() . ' outstanding bills');

        if ($outstandingBills->isEmpty()) {
            return redirect()->back()->with('info', 'No outstanding bills to send reminders for.');
        }

        $parentBills = [];

        foreach ($outstandingBills as $bill) {
            // Get the student profile
            $student = $bill->student;
            
            \Log::debug('Processing bill for student: ' . ($student ? $student->id : 'NULL'));
            
            if (!$student) {
                \Log::warning('Bill ' . $bill->id . ' has no student profile');
                continue;
            }
            
            if (!$student->parent) {
                \Log::warning('Student ' . $student->id . ' has no parent assigned');
                continue;
            }

            // Get parent directly from student profile
            $parent = $student->parent;

            \Log::debug('Found parent: ' . $parent->email . ' for student: ' . $student->id);

            $parentId = $parent->id;

            if (!isset($parentBills[$parentId])) {
                $parentBills[$parentId] = [
                    'parent' => $parent,
                    'bills' => collect(),
                ];
            }

            $parentBills[$parentId]['bills']->push($bill);
        }

        \Log::info('Payment reminders: Grouped ' . count($parentBills) . ' parents with outstanding bills');

        if (empty($parentBills)) {
            return redirect()->back()->with('info', 'No parents found with outstanding bills to notify.');
        }

        // Send reminder emails
        $remindersCount = 0;
        $failureCount = 0;
        $failedParents = [];
        
        foreach ($parentBills as $parentData) {
            try {
                $parentEmail = $parentData['parent']->email;
                $billsCount = $parentData['bills']->count();
                
                \Log::info('Sending payment reminder to: ' . $parentEmail . ' with ' . $billsCount . ' bills');
                
                // Validate parent has email
                if (!$parentEmail) {
                    throw new \Exception('Parent has no email address');
                }
                
                // Validate bills are not empty
                if ($billsCount === 0) {
                    throw new \Exception('No bills to send in reminder');
                }
                
                Mail::send(new PaymentReminderNotification($parentData['parent'], $parentData['bills']));
                $remindersCount++;
                
                \Log::info('Successfully sent payment reminder to: ' . $parentEmail);
            } catch (\Exception $e) {
                $failureCount++;
                $parentEmail = $parentData['parent']->email ?? 'Unknown';
                $failedParents[] = $parentEmail;
                \Log::error('Failed to send payment reminder to parent ' . $parentEmail . ': ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            }
        }

        // Build feedback message
        $message = "Payment reminders queued for {$remindersCount} parent(s). Emails will be sent shortly.";
        if ($failureCount > 0) {
            $message .= " ({$failureCount} failed to queue)";
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('failedParents', $failureCount > 0 ? $failedParents : null);
    }
}

