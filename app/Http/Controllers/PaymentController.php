<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeItem;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\UserProfile;
use App\Models\User;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\StudentBill;
use App\Models\SchoolSetting;
use App\Services\PaymentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

/**
 * PaymentController
 * 
 * Handles all payment-related operations following international best practices:
 * - Transaction atomicity and consistency
 * - Comprehensive audit trails
 * - Proper separation of concerns with PaymentService
 * - Standardized error handling and validation
 */
class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Payment Dashboard
     */
    public function index(): View
    {
        $settings = SchoolSetting::getInstance();
        
        // Total collected from all payments
        $totalCollected = Payment::where('amount', '>', 0)
            ->sum('amount');
        
        // Pending payments = bills not fully paid
        $pendingPayments = StudentBill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum('balance_due');
        
        // Statistics
        $totalStudentsBilled = StudentBill::count();
        $studentsPaid = StudentBill::where('status', 'paid')->count();

        // Recent payments
        $recentPayments = Payment::with('student', 'studentBill')
            ->orderBy('paid_at', 'desc')
            ->take(10)
            ->get();

        return view('payments.index', compact(
            'totalCollected',
            'pendingPayments',
            'totalStudentsBilled',
            'studentsPaid',
            'recentPayments',
            'settings'
        ));
    }

    // ============ Student Bills Management ============

    /**
     * List all student bills
     */
    public function studentBills(Request $request): View
    {
        $query = StudentBill::with([
            'student.schoolClass',
            'academicSession'
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $status = strtolower($request->status);
            $query->where('status', $status);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }

        // Search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = SchoolClass::orderBy('name')->get();

        return view('payments.bills.index', compact('bills', 'classes'));
    }

    /**
     * View single bill details
     */
    public function viewBill(StudentBill $bill): View
    {
        $bill->load(['student', 'billItems', 'payments', 'academicSession']);
        return view('payments.bills.view', compact('bill'));
    }

    /**
     * Print bill invoice
     */
    public function printInvoice(StudentBill $bill): View
    {
        $bill->load(['student', 'billItems', 'payments', 'academicSession', 'schoolClass']);
        $settings = SchoolSetting::getInstance();
        return view('payments.bills.print-invoice', compact('bill', 'settings'));
    }

    // ============ Payment Processing ============

    /**
     * Record manual payment for a bill
     */
    public function recordPayment(Request $request, StudentBill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bill->balance_due,
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online,waiver',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $bill->recordPayment(
                amount: $validated['amount'],
                method: $validated['payment_method'],
                reference: $request->input('reference_number'),
                recordedBy: auth()->id(),
                notes: $validated['notes'] ?? null
            );

            return redirect()->back()
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Apply waiver/scholarship to bill
     */
    public function applyWaiver(Request $request, StudentBill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bill->outstanding_amount,
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->applyWaiver($bill, [
                'amount' => $validated['amount'],
                'remarks' => $validated['remarks'],
            ]);

            return redirect()->back()
                ->with('success', 'Waiver applied successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error applying waiver: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process online payment (Paystack/Flutterwave)
     */
    public function initiateOnlinePayment(Request $request, StudentBill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $bill->outstanding_amount,
            'gateway' => 'required|in:paystack,flutterwave',
        ]);

        try {
            // For now, redirect to Paystack
            // Implementation details depend on gateway choice
            
            if ($validated['gateway'] === 'paystack') {
                return $this->initializePaystackPayment($request, $bill);
            }
            
            return redirect()->back()->with('error', 'Payment gateway not yet implemented');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error initiating payment: ' . $e->getMessage());
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->refundPayment($payment, $validated['reason']);
            
            return redirect()->back()
                ->with('success', 'Payment refunded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error refunding payment: ' . $e->getMessage());
        }
    }

    // ============ Reports ============

    /**
     * Payment history report
     */
    public function paymentHistory(Request $request): View
    {
        $query = Payment::with(['student', 'billItem'])
            ->where('status', 'completed')
            ->where('amount', '>', 0);

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        $classes = SchoolClass::orderBy('name')->get();

        return view('payments.reports.payment-history', compact('payments', 'classes'));
    }

    /**
     * Outstanding debt report
     */
    public function debtManagement(Request $request): View
    {
        // Get all unpaid and partial bills with related data
        $query = StudentBill::with(['student.schoolClass', 'academicSession'])
            ->where('balance_due', '>', 0);

        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }

        // Paginate the query results
        $debtors = $query->orderBy('balance_due', 'desc')->paginate(20);
        
        $classes = SchoolClass::orderBy('name')->get();
        
        // Calculate total debt amounts using balance_due
        $totalDebt = StudentBill::where('balance_due', '>', 0)->sum('balance_due');
        
        // Calculate debt by status
        $pendingDebt = StudentBill::where('balance_due', '>', 0)
            ->where('paid_amount', 0)
            ->sum('balance_due');
        
        $partialDebt = StudentBill::where('balance_due', '>', 0)
            ->where('paid_amount', '>', 0)
            ->sum('balance_due');
        
        // Count bills by status
        $pendingCount = StudentBill::where('balance_due', '>', 0)
            ->where('paid_amount', 0)
            ->count();
        
        $partialCount = StudentBill::where('balance_due', '>', 0)
            ->where('paid_amount', '>', 0)
            ->count();
        
        // Count overdue bills (bills past due date with outstanding balance)
        $overdueCount = StudentBill::where('balance_due', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
        
        // Sum overdue debt amount
        $overdueDebt = StudentBill::where('balance_due', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->sum('balance_due');
        
        // Get top debtors
        $topDebtors = StudentBill::with('student')
            ->where('balance_due', '>', 0)
            ->orderBy('balance_due', 'desc')
            ->take(5)
            ->get();

        return view('payments.reports.debt-management', [
            'bills' => $debtors,
            'totalOutstanding' => $totalDebt,
            'classes' => $classes,
            'totalDebt' => $totalDebt,
            'pendingDebt' => $pendingDebt,
            'partialDebt' => $partialDebt,
            'pendingCount' => $pendingCount,
            'partialCount' => $partialCount,
            'overdueCount' => $overdueCount,
            'overdueDebt' => $overdueDebt,
            'topDebtors' => $topDebtors
        ]);
    }

    /**
     * Send payment reminders to all parents with outstanding bills
     */
    public function sendPaymentReminders(): RedirectResponse
    {
        // Get all outstanding bills with student data
        $outstandingBills = StudentBill::where('balance_due', '>', 0)
            ->with(['student' => function ($query) {
                $query->with(['user' => function ($q) {
                    $q->with('parent');
                }]);
            }])
            ->get();

        if ($outstandingBills->isEmpty()) {
            return redirect()->back()->with('info', 'No outstanding bills to send reminders for.');
        }

        $parentBills = [];

        foreach ($outstandingBills as $bill) {
            // Get the student's user account
            $student = $bill->student;
            
            if (!$student || !$student->user) {
                continue;
            }

            // Get parent through the relationship
            $parent = $student->user->parent;
            
            if (!$parent) {
                continue;
            }

            $parentId = $parent->id;

            if (!isset($parentBills[$parentId])) {
                $parentBills[$parentId] = [
                    'parent' => $parent,
                    'bills' => collect(),
                ];
            }

            $parentBills[$parentId]['bills']->push($bill);
        }

        if (empty($parentBills)) {
            return redirect()->back()->with('info', 'No parents found with outstanding bills to notify.');
        }

        // Send reminder emails
        $remindersCount = 0;
        foreach ($parentBills as $parentData) {
            Mail::send(new PaymentReminderNotification($parentData['parent'], $parentData['bills']));
            $remindersCount++;
        }

        return redirect()->back()
            ->with('success', "Payment reminders sent to {$remindersCount} parent(s)!");
    }

    // ============ Receipts ============

    /**
     * Generate receipt from payment
     */
    public function generateReceipt(Payment $payment): RedirectResponse
    {
        try {
            // Check if receipt already exists
            if ($payment->receipt) {
                return redirect()->route('payments.receipts.view', $payment->receipt)
                    ->with('info', 'Receipt already exists for this payment');
            }

            $session = $payment->studentBill?->academicSession?->session ?? 'Academic Session';
            
            $receipt = PaymentReceipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'RCP-' . date('Ymd') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                'status' => 'generated',
                'notes' => "Payment for {$session}",
                'generated_at' => now(),
            ]);

            return redirect()->route('payments.receipts.view', $receipt)
                ->with('success', 'Receipt generated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate receipt: ' . $e->getMessage());
        }
    }

    /**
     * View payment receipt
     */
    public function viewReceipt(PaymentReceipt $receipt): View
    {
        $receipt->load(['payment.student', 'payment.studentBill.billItems']);
        return view('payments.receipts.view', compact('receipt'));
    }

    /**
     * Print receipt
     */
    public function printReceipt(PaymentReceipt $receipt): View
    {
        $receipt->load(['payment.student', 'payment.studentBill.billItems']);
        $settings = SchoolSetting::getInstance();
        return view('payments.receipts.print', compact('receipt', 'settings'));
    }

    /**
     * Download receipt as PDF
     */
    public function downloadReceiptPDF(PaymentReceipt $receipt): View
    {
        $receipt->load(['payment.student', 'payment.studentBill.billItems']);
        $settings = SchoolSetting::getInstance();
        return view('payments.receipts.pdf', compact('receipt', 'settings'));
    }

    /**
     * List all receipts
     */
    public function receiptsList(Request $request): View
    {
        $query = PaymentReceipt::with(['payment.student', 'payment.studentBill']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('payment.student', function ($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
        }

        $receipts = $query->orderBy('generated_at', 'desc')->paginate(20);
        
        return view('payments.reports.receipts-list', compact('receipts'));
    }

    // ============ Paystack Integration ============

    /**
     * Initialize Paystack payment
     */
    private function initializePaystackPayment(Request $request, StudentBill $bill): RedirectResponse
    {
        $paystackService = new PaystackService();

        if (!$paystackService->isConfigured()) {
            return redirect()->back()
                ->with('error', 'Paystack is not configured. Please contact administration.');
        }

        $amount = $request->input('amount');
        $student = $bill->student;

        $metadata = [
            'bill_id' => $bill->id,
            'student_id' => $student->id,
            'student_name' => $student->full_name ?? $student->name,
            'amount' => $amount,
        ];

        $result = $paystackService->initializePayment(
            $amount,
            $student->email,
            $metadata
        );

        if ($result['success']) {
            $paymentData = $result['data']['data'];
            return redirect($paymentData['authorization_url']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Handle Paystack callback
     */
    public function handlePaystackCallback(Request $request): RedirectResponse
    {
        $reference = $request->input('reference');
        $paystackService = new PaystackService();
        $result = $paystackService->verifyPayment($reference);

        if ($result['success']) {
            $transactionData = $result['data']['data'];

            if ($transactionData['status'] === 'success') {
                $metadata = $transactionData['metadata'];

                try {
                    $bill = StudentBill::findOrFail($metadata['bill_id']);

                    $this->paymentService->processOnlinePayment($bill, [
                        'amount' => $metadata['amount'],
                        'currency' => 'NGN',
                        'gateway' => 'paystack',
                    ]);

                    return redirect()->route('payments.bills.view', $bill)
                        ->with('success', 'Payment processed successfully!');
                } catch (\Exception $e) {
                    return redirect()->route('payments.bills.index')
                        ->with('error', 'Error processing payment: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('payments.bills.index')
            ->with('error', 'Payment verification failed');
    }

    /**
     * Parent Portal: Initiate Paystack payment - Permission centered
     */
    public function parentInitiatePaystackPayment(Request $request)
    {
        // Check permission for Paystack payments
        if (!auth()->user()->hasPermission('make-paystack-payment')) {
            return response()->json(['error' => 'You do not have permission to use Paystack'], 403);
        }

        $request->validate([
            'bills' => 'required|array|min:1',
            'bills.*' => 'exists:student_bills,id',
        ]);

        $parent = auth()->user()->profile;
        $billIds = $request->bills;

        // Cast bill IDs to integers
        $billIds = array_map(function($id) { return (int)$id; }, $billIds);

        // Get parent's children
        $children = $parent->children()->get();
        $childrenIds = $children->pluck('id');

        // Get selected bills - verify all belong to parent's children and are payable
        // Accept any status except 'paid'
        $bills = StudentBill::whereIn('id', $billIds)
            ->whereIn('student_id', $childrenIds)
            ->where('status', '!=', 'paid')
            ->where('balance_due', '>', 0)
            ->get();

        if ($bills->isEmpty()) {
            // Debug: Check what went wrong
            $allBills = StudentBill::whereIn('id', $billIds)->get();
            $parentChildrenIds = $parent->children()->pluck('id');
            $parentBills = StudentBill::whereIn('student_id', $parentChildrenIds)->get();
            
            \Log::error('Invalid bills for Paystack payment', [
                'requested_bill_ids' => $billIds,
                'parent_id' => $parent->id,
                'parent_user_id' => $parent->user_id,
                'children_ids' => $parentChildrenIds->toArray(),
                'all_bills_requested' => $allBills->map(fn($b) => ['id' => $b->id, 'student_id' => $b->student_id, 'status' => $b->status, 'total_amount' => $b->total_amount])->toArray(),
                'all_parent_bills' => $parentBills->map(fn($b) => ['id' => $b->id, 'student_id' => $b->student_id, 'status' => $b->status, 'total_amount' => $b->total_amount])->toArray(),
            ]);
            
            return response()->json([
                'error' => 'Invalid bills selected. Please select unpaid bills.',
            ], 403);
        }

        // Calculate total amount (convert to kobo for Paystack)
        $totalAmount = (int)($bills->sum('total_amount') * 100);

        // Get Paystack key from settings
        $settings = SchoolSetting::getInstance();
        $paystackSecretKey = $settings->paystack_secret_key;

        if (!$paystackSecretKey) {
            return response()->json(['error' => 'Paystack is not configured'], 400);
        }

        // Prepare Paystack request
        $email = auth()->user()->email;
        $billReference = 'PBILL-' . implode('-', array_slice($billIds, 0, 2)) . '-' . time();

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $totalAmount,
                'reference' => $billReference,
                'metadata' => [
                    'bill_ids' => $billIds,
                    'parent_id' => $parent->id,
                    'bills_amount' => $bills->sum('total_amount'),
                    'type' => 'parent_payment',
                ],
            ]);

            $data = $response->json();

            if ($response->status() === 200 && $data['status']) {
                return response()->json([
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $billReference,
                ]);
            }

            return response()->json(['error' => 'Failed to initialize payment: ' . ($data['message'] ?? 'Unknown error')], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Parent Portal: Handle Paystack callback - Permission centered
     */
    public function parentPaystackCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            \Log::error('Paystack callback: No reference provided');
            return redirect()->route('parent-portal.bills')
                ->with('error', 'No payment reference provided');
        }

        \Log::info('Paystack callback received', ['reference' => $reference]);

        // Get Paystack secret key
        $settings = SchoolSetting::getInstance();
        $paystackSecretKey = $settings->paystack_secret_key;

        if (!$paystackSecretKey) {
            \Log::error('Paystack callback: Secret key not configured');
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Payment gateway not configured');
        }

        try {
            // Verify transaction with Paystack
            \Log::info('Verifying Paystack transaction', ['reference' => $reference]);
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
            ])->get('https://api.paystack.co/transaction/verify/' . $reference);

            \Log::info('Paystack response received', ['status_code' => $response->status()]);

            $data = $response->json();
            \Log::info('Paystack response data', ['data' => $data]);

            if ($response->status() === 200 && $data['status'] && $data['data']['status'] === 'success') {
                // Payment successful
                $transactionData = $data['data'];
                
                // Extract bill IDs from metadata
                $billIds = [];
                if (isset($transactionData['metadata']) && is_array($transactionData['metadata'])) {
                    $billIds = $transactionData['metadata']['bill_ids'] ?? [];
                }
                
                \Log::info('Payment verified successfully', ['billIds' => $billIds, 'amount' => $transactionData['amount']]);
                
                if (empty($billIds)) {
                    \Log::warning('Paystack callback: No bill IDs in metadata', ['reference' => $reference]);
                    return redirect()->route('parent-portal.payment-history')
                        ->with('warning', 'Payment verified but no bills found. Please contact support.');
                }

                $amountPaid = $transactionData['amount'] / 100; // Convert from kobo

                // Record payment for each bill
                foreach ($billIds as $billId) {
                    $bill = StudentBill::find($billId);
                    if ($bill) {
                        // Calculate payment amount (distribute equally if multiple bills)
                        $paymentAmount = $amountPaid / count($billIds);
                        
                        \Log::info('Creating payment record', [
                            'bill_id' => $billId,
                            'student_id' => $bill->student_id,
                            'amount' => $paymentAmount
                        ]);
                        
                        $payment = Payment::create([
                            'student_id' => $bill->student_id,
                            'student_bill_id' => $billId,
                            'amount' => $paymentAmount,
                            'payment_method' => 'paystack',
                            'paid_at' => now(),
                            'payment_date' => now(),
                            'reference_number' => $reference,
                            'status' => 'success',
                            'notes' => 'Paystack payment - ' . $transactionData['reference'],
                        ]);

                        // Update bill: add to paid_amount and calculate balance_due
                        $newPaidAmount = ($bill->paid_amount ?? 0) + $paymentAmount;
                        $newBalanceDue = $bill->total_amount - $newPaidAmount;
                        
                        $bill->update([
                            'paid_amount' => $newPaidAmount,
                            'balance_due' => max(0, $newBalanceDue),
                            'status' => $newBalanceDue <= 0 ? 'paid' : 'pending'
                        ]);
                        
                        \Log::info('Bill updated', [
                            'bill_id' => $billId,
                            'paid_amount' => $newPaidAmount,
                            'balance_due' => $newBalanceDue
                        ]);
                    } else {
                        \Log::warning('Bill not found', ['bill_id' => $billId]);
                    }
                }

                \Log::info('Paystack payment processed successfully', ['reference' => $reference]);
                
                return redirect()->route('parent-portal.payment-history')
                    ->with('success', 'Payment successful! Receipt will be sent to your email.');
            } else {
                // Payment failed
                \Log::warning('Paystack payment failed', ['reference' => $reference, 'status' => $data['data']['status'] ?? 'unknown']);

                $errorMessage = $data['message'] ?? 'Payment verification failed';
                return redirect()->route('parent-portal.bills')
                    ->with('error', 'Payment failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    /**
     * Parent Portal: Record manual payment (bank transfer, cash, cheque) - Permission centered
     */
    public function parentRecordManualPayment(Request $request)
    {
        $paymentMethod = $request->input('payment_method');
        
        // Check permission for specific payment method
        $permissionMap = [
            'bank_transfer' => 'make-bank-transfer-payment',
            'cash' => 'make-cash-payment',
            'cheque' => 'make-cheque-payment',
        ];

        if (isset($permissionMap[$paymentMethod])) {
            if (!auth()->user()->hasPermission($permissionMap[$paymentMethod])) {
                return redirect()->route('parent-portal.bills')
                    ->with('error', "You do not have permission to make {$paymentMethod} payments");
            }
        } else {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Invalid payment method');
        }

        $request->validate([
            'bills' => 'required|array|min:1',
            'bills.*' => 'exists:bills,id',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $parent = auth()->user()->profile;
        $billIds = $request->bills;

        // Get parent's children
        $children = \App\Models\Student::where('parent_id', $parent->id)->get();
        $childrenIds = $children->pluck('id');

        // Get selected bills - verify all belong to parent's children
        $bills = StudentBill::whereIn('id', $billIds)
            ->whereIn('student_id', $childrenIds)
            ->get();

        if ($bills->isEmpty()) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Invalid bills selected');
        }

        // Record payment for each bill
        foreach ($bills as $bill) {
            Payment::create([
                'student_id' => $bill->student_id,
                'bill_id' => $bill->id,
                'amount' => $bill->amount,
                'payment_method' => $paymentMethod,
                'payment_date' => now(),
                'reference' => $request->input('reference'),
                'status' => 'pending', // Manual payments pending verification
                'notes' => $request->input('notes'),
            ]);

            // Update bill status to pending verification
            $bill->update(['status' => 'pending_verification']);
        }

        return redirect()->route('parent-portal.payment-history')
            ->with('success', 'Payment recorded. Please check your email for payment instructions and confirmation.');
    }
}

