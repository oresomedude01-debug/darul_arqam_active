<?php

namespace App\Http\Controllers;

use App\Models\StudentBill as Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\GradeScale;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use App\Services\ResultComputationService;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;

class ParentPortalController extends Controller
{
    /**
     * Parent Portal Dashboard - Permission centered
     */
    public function dashboard()
    {
        // Check if parent has any portal permissions
        $this->requireAnyPermission([
            'view-own-children',
            'view-own-bills',
            'view-own-payment-history'
        ]);
        
        $parentUser = auth()->user();
        
        // Get all children associated with this parent (User models)
        $childUsers = $parentUser->children()
            ->with('profile.schoolClass')
            ->get();
        
        // Map to Student/UserProfile models for display
        $children = $childUsers->map(function ($childUser) {
            return $childUser->profile;
        });

        // Get summary statistics using profile IDs
        $childrenIds = $children->pluck('id');
        $totalOutstanding = Bill::whereIn('student_id', $childrenIds)
            ->where('status', '!=', 'paid')
            ->sum('total_amount');

        $pendingBills = Bill::whereIn('student_id', $childrenIds)
            ->where('status', '!=', 'paid')
            ->count();

        $totalPaid = Payment::whereIn('student_id', $childrenIds)->sum('amount');

        // Get school contact information from settings
        $schoolSettings = SchoolSetting::first();

        return view('parent-portal.dashboard', compact(
            'parentUser',
            'children',
            'totalOutstanding',
            'pendingBills',
            'totalPaid',
            'schoolSettings'
        ));
    }

    /**
     * View all children - Permission centered
     */
    public function viewChildren()
    {
        $this->requirePermission('view-own-children');
        
        $parentUser = auth()->user();
        
        $childUsers = $parentUser->children()
            ->with('profile.schoolClass')
            ->get();

        // Map User records to Student records (profiles)
        $children = $childUsers->map(function ($childUser) {
            $student = $childUser->profile;
            $outstanding = Bill::where('student_id', $student->id)
                ->where('status', '!=', 'paid')
                ->sum('total_amount');

            return [
                'student' => $student,
                'outstanding' => $outstanding,
            ];
        });

        return view('parent-portal.children', compact('children'));
    }

    /**
     * View bills for a specific student or all children - Permission centered
     */
    public function viewBills(Request $request, $studentId = null)
    {
        $this->requirePermission('view-own-bills');
        
        $parentUser = auth()->user();
        
        // Get parent's children (User models)
        $childUsers = $parentUser->children()->get();
        
        // Get their profiles to access student IDs
        $children = $childUsers->map(function ($u) { return $u->profile; });
        $childrenIds = $children->pluck('id');

        // If specific student requested, verify it belongs to parent
        if ($studentId) {
            if (!$childrenIds->contains($studentId)) {
                abort(403, 'Unauthorized - This student is not your child');
            }
        }

        // Get bills
        $query = Bill::whereIn('student_id', $childrenIds)
            ->with(['student', 'academicSession', 'academicTerm']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        // Filter by status
        if ($request->status && in_array($request->status, ['paid', 'pending', 'overdue'])) {
            $query->where('status', $request->status);
        }

        $bills = $query->orderBy('due_date', 'desc')->paginate(15);

        // Get selected student for breadcrumb
        $selectedStudent = $studentId ? Student::find($studentId) : null;

        return view('parent-portal.bills', compact(
            'bills',
            'children',
            'selectedStudent',
            'childrenIds'
        ));
    }

    /**
     * View payment history - Permission centered
     */
    public function paymentHistory(Request $request, $studentId = null)
    {
        $this->requirePermission('view-own-payment-history');
        
        $parentUser = auth()->user();
        
        // Get parent's children
        $childUsers = $parentUser->children()->get();
        $children = $childUsers->map(function ($u) { return $u->profile; });
        $childrenIds = $children->pluck('id');

        // If specific student requested, verify it belongs to parent
        if ($studentId) {
            if (!$childrenIds->contains($studentId)) {
                abort(403, 'Unauthorized - This student is not your child');
            }
        }

        // Get payments
        $query = Payment::whereIn('student_id', $childrenIds)
            ->with(['student', 'studentBill']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        // Get selected student
        $selectedStudent = $studentId ? Student::find($studentId) : null;

        return view('parent-portal.payment-history', compact(
            'payments',
            'children',
            'selectedStudent',
            'childrenIds'
        ));
    }

    /**
     * Select payment method for bills - Permission centered
     */
    public function selectPaymentMethod(Request $request)
    {
        // Check at least one payment method permission
        $this->requireAnyPermission([
            'make-paystack-payment',
            'make-bank-transfer-payment',
            'make-cash-payment',
            'make-cheque-payment'
        ]);
        
        $parentUser = auth()->user();
        $billIds = $request->input('bills', []);
        
        if (empty($billIds)) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Please select bills to pay');
        }

        // Get parent's children
        $childUsers = $parentUser->children()->get();
        $children = $childUsers->map(function ($u) { return $u->profile; });
        $childrenIds = $children->pluck('id');

        // Get selected bills - verify all belong to parent's children
        $bills = Bill::whereIn('id', $billIds)
            ->whereIn('student_id', $childrenIds)
            ->where('status', '!=', 'paid')
            ->get();

        if ($bills->isEmpty()) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Invalid bills selected');
        }

        // Calculate total amount
        $totalAmount = $bills->sum('total_amount');

        // Get available payment methods based on permissions
        $paymentMethods = [];
        
        if (auth()->user()->hasPermission('make-paystack-payment')) {
            $paymentMethods['paystack'] = [
                'label' => 'Paystack (Card/Bank)',
                'description' => 'Pay securely using your card or bank account',
                'icon' => 'fa-credit-card'
            ];
        }
        if (auth()->user()->hasPermission('make-bank-transfer-payment')) {
            $paymentMethods['bank_transfer'] = [
                'label' => 'Bank Transfer',
                'description' => 'Transfer directly to school account',
                'icon' => 'fa-building'
            ];
        }
        if (auth()->user()->hasPermission('make-cash-payment')) {
            $paymentMethods['cash'] = [
                'label' => 'Cash Payment',
                'description' => 'Pay in cash at school office',
                'icon' => 'fa-money-bill'
            ];
        }
        if (auth()->user()->hasPermission('make-cheque-payment')) {
            $paymentMethods['cheque'] = [
                'label' => 'Cheque Payment',
                'description' => 'Pay by cheque',
                'icon' => 'fa-file-invoice'
            ];
        }

        // Also check school settings for additional configuration
        $settings = SchoolSetting::getInstance();
        $additionalSettings = $settings->additional_settings ?? [];
        if (is_string($additionalSettings)) {
            $additionalSettings = json_decode($additionalSettings, true) ?? [];
        }

        // Filter methods by school settings if configured
        if (!empty($additionalSettings['payment_methods'])) {
            $configuredMethods = $additionalSettings['payment_methods'];
            foreach (array_keys($paymentMethods) as $method) {
                if (!in_array($method, $configuredMethods)) {
                    unset($paymentMethods[$method]);
                }
            }
        }

        return view('parent-portal.payment-methods', compact(
            'bills',
            'totalAmount',
            'paymentMethods'
        ));
    }

    /**
     * Show payment method selection page - Permission centered
     */
    public function initiatePayment(Request $request)
    {
        // Check at least one payment method permission
        $this->requireAnyPermission([
            'make-paystack-payment',
            'make-bank-transfer-payment',
            'make-cash-payment',
            'make-cheque-payment'
        ]);
        
        $parentUser = auth()->user();
        $billIds = $request->query('bills');
        
        if (!$billIds) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Please select bills to pay');
        }

        // Parse bill IDs
        $selectedBillIds = explode(',', $billIds);
        
        // Get parent's children
        $childUsers = $parentUser->children()->get();
        $children = $childUsers->map(function ($u) { return $u->profile; });
        $childrenIds = $children->pluck('id');

        // Get selected bills - verify all belong to parent's children
        $bills = Bill::whereIn('id', $selectedBillIds)
            ->whereIn('student_id', $childrenIds)
            ->get();

        if ($bills->isEmpty()) {
            return redirect()->route('parent-portal.bills')
                ->with('error', 'Invalid bills selected');
        }

        // Calculate total amount
        $totalAmount = $bills->sum('total_amount');

        // Get available payment methods based on permissions
        $paymentMethods = [];
        
        if (auth()->user()->hasPermission('make-paystack-payment')) {
            $paymentMethods['paystack'] = true;
        }
        if (auth()->user()->hasPermission('make-bank-transfer-payment')) {
            $paymentMethods['bank_transfer'] = true;
        }
        if (auth()->user()->hasPermission('make-cash-payment')) {
            $paymentMethods['cash'] = true;
        }
        if (auth()->user()->hasPermission('make-cheque-payment')) {
            $paymentMethods['cheque'] = true;
        }

        // Also check school settings for additional configuration
        $settings = SchoolSetting::getInstance();
        $additionalSettings = $settings->additional_settings ?? [];
        if (is_string($additionalSettings)) {
            $additionalSettings = json_decode($additionalSettings, true) ?? [];
        }

        // Filter methods by school settings if configured
        if (!empty($additionalSettings['payment_methods'])) {
            $configuredMethods = $additionalSettings['payment_methods'];
            $paymentMethods = array_intersect_key($paymentMethods, array_flip($configuredMethods));
        }

        return view('parent-portal.payment-methods', compact(
            'bills',
            'totalAmount',
            'paymentMethods'
        ));
    }

    /**
     * Check if user has required permission
     */
    protected function requirePermission($permission)
    {
        if (!auth()->check() || !auth()->user()->hasPermission($permission)) {
            abort(403, "You do not have permission: {$permission}");
        }
    }

    /**
     * Check if user has at least one of the required permissions
     */
    protected function requireAnyPermission(array $permissions)
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (auth()->user()->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'You do not have access to this resource');
        }
    }

    /**
     * Show a single child's details
     */
    public function showChild($childId)
    {
        $this->requirePermission('view-own-children');

        $parentUser = auth()->user();
        
        // Get the student profile by ID
        $child = UserProfile::find($childId);
        if (!$child) {
            abort(404, 'Student profile not found');
        }

        // Check if this student belongs to the current parent
        if ($child->parent_id !== $parentUser->id) {
            abort(403, 'Unauthorized - This student is not your child');
        }

        $outstanding = Bill::where('student_id', $child->id)
            ->where('status', '!=', 'paid')
            ->sum('total_amount');

        return view('parent-portal.children-show', compact('child', 'outstanding'));
    }

    /**
     * View children's attendance records
     */
    public function viewAttendance(Request $request)
    {
        $this->requirePermission('view-own-children');

        $parentUser = auth()->user();
        $childUsers = $parentUser->children()
            ->with(['profile.schoolClass'])
            ->get();
        
        // Map to profiles for display
        $children = $childUsers->map(function ($u) { return $u->profile; });

        $selectedChild = null;
        $attendanceRecords = collect();

        if ($request->has('student')) {
            $selectedChild = $children->find($request->student);
            if ($selectedChild) {
                $attendanceRecords = \App\Models\Attendance::where('user_profile_id', $selectedChild->id)
                    ->with(['schoolClass'])
                    ->orderBy('date', 'desc')
                    ->paginate(20);
            }
        }

        return view('parent-portal.attendance', compact('children', 'selectedChild', 'attendanceRecords'));
    }

    /**
     * View children's academic results/grades
     */


public function viewResults(Request $request)
{
    $this->requirePermission('view-own-children');

    $parentUser = auth()->user();

    $children = $parentUser->children()
        ->with(['profile.schoolClass'])
        ->get()
        ->pluck('profile');

    $selectedChild   = null;
    $selectedSession = null;
    $selectedTerm    = null;

    $resultsWithGrades = collect();
    $averageScore = 0;
    $highestScore = 0;
    $lowestScore  = 0;
    $passCount    = 0;

    $sessions = AcademicSession::orderBy('created_at', 'desc')->get();
    $terms    = collect();

    if ($request->filled('student')) {
        $selectedChild = $children
            ->where('id', (int) $request->student)
            ->first();

    }

    if ($selectedChild && $request->filled('session')) {
        $selectedSession = AcademicSession::find($request->session);
        $terms = $selectedSession?->terms()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    if ($selectedChild && $selectedSession && $request->filled('term')) {
        $selectedTerm = AcademicTerm::find($request->term);

        if ($selectedTerm) {
            /** ✅ SINGLE SOURCE OF TRUTH */
            $service = app(ResultComputationService::class);

            $data = $service->compute(
                $selectedSession,
                $selectedTerm,
                $selectedChild
            );

            $resultsWithGrades = $data['results'];

            $averageScore = $data['summary']['average'] ?? 0;
            $highestScore = $data['summary']['highest'] ?? 0;
            $lowestScore  = $data['summary']['lowest'] ?? 0;
            $passCount    = $data['summary']['pass'] ?? 0;
        }
    }

    // AJAX response
    if ($request->expectsJson()) {
        $html = '';

        if (
            $selectedChild &&
            $selectedSession &&
            $selectedTerm &&
            $resultsWithGrades->isNotEmpty()
        ) {
            $html = view('parent-portal.results-content', compact(
                'selectedChild',
                'resultsWithGrades',
                'selectedSession',
                'selectedTerm',
                'averageScore',
                'highestScore',
                'lowestScore',
                'passCount'
            ))->render();
        }

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    return view('parent-portal.results', compact(
        'children',
        'selectedChild',
        'resultsWithGrades',
        'sessions',
        'terms',
        'selectedSession',
        'selectedTerm',
        'averageScore',
        'highestScore',
        'lowestScore',
        'passCount'
    ));
}


    /**
     * Get terms by academic session (AJAX endpoint)
     */
    public function getTermsBySession($sessionId)
    {
        $session = \App\Models\AcademicSession::find($sessionId);
        
        if (!$session) {
            return response()->json(['terms' => [], 'error' => 'Session not found'], 404);
        }

        $terms = $session->terms()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'terms' => $terms->map(function ($term) {
                return [
                    'id' => $term->id,
                    'name' => $term->name
                ];
            })->toArray()
        ]);
    }

    /**
     * Print child results using the admin's print route
     */
    public function printResults($childId)
    {
        $this->requirePermission('view-own-children');

        $parentUser = auth()->user();
        
        // Get the User by ID
        $childUser = User::find($childId);
        if (!$childUser || $childUser->parent_id !== $parentUser->id) {
            abort(403, 'Unauthorized - This student is not your child');
        }

        // Get the student profile
        $child = $childUser->profile;
        if (!$child) {
            abort(404, 'Student profile not found');
        }

        // Get session and term from request
        $sessionId = request('session') ?? \App\Models\AcademicSession::latest('created_at')->first()?->id;
        $termId = request('term') ?? \App\Models\AcademicTerm::latest('created_at')->first()?->id;

        if (!$sessionId || !$termId) {
            return back()->with('error', 'Please select a session and term first');
        }

        // Redirect to the admin's print route
        return redirect()->route('results.student.print', [
            'session' => $sessionId,
            'term' => $termId,
            'student' => $childId
        ]);
    }

    /**
     * Print child results with session, term, and student IDs in URL
     */
    public function printResultsWithParams($session, $term, $student)
    {
        $this->requirePermission('view-own-children');

        $parentUser = auth()->user();
        
        // Verify the student belongs to this parent using UserProfile
        $childProfile = UserProfile::find($student);
        if (!$childProfile || $childProfile->parent_id !== $parentUser->id) {
            abort(403, 'Unauthorized - This student is not your child');
        }

        // Redirect to the admin's print route
        return redirect()->route('results.student.print', [
            'session' => $session,
            'term' => $term,
            'student' => $student
        ]);
    }

    /**
     * View school announcements
     */
    public function viewAnnouncements()
    {
        $this->requireAnyPermission([
            'view-own-children',
            'view-own-bills',
            'view-own-payment-history'
        ]);

        // Using Event model for announcements if available, otherwise create collection
        $announcements = \App\Models\Event::where('type', 'announcement')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('parent-portal.announcements', compact('announcements'));
    }

    /**
     * View school calendar/events
     */
    public function viewCalendar()
    {
        $this->requireAnyPermission([
            'view-own-children',
            'view-own-bills',
            'view-own-payment-history'
        ]);

        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $events = \App\Models\Event::whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->orderBy('start_date', 'asc')
            ->get();

        $upcomingEvents = \App\Models\Event::where('start_date', '>=', now()->format('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return view('parent-portal.calendar', compact('events', 'upcomingEvents'));
    }

    /**
     * View performance analytics
     */
    public function viewPerformance(Request $request)
    {
        $this->requirePermission('view-own-children');

        $parentUser = auth()->user();
        $childUsers = $parentUser->children()
            ->with(['profile.schoolClass'])
            ->get();
        
        // Map to profiles for display
        $children = $childUsers->map(function ($u) { return $u->profile; });

        $selectedChild = null;
        $performanceData = null;

        if ($request->has('student')) {
            $selectedChild = $children->find($request->student);
            if ($selectedChild) {
                $grades = \App\Models\Grade::where('student_id', $selectedChild->id)
                    ->with(['subject'])
                    ->get();
                $attendance = \App\Models\Attendance::where('user_profile_id', $selectedChild->id)->get();

                // Check if we have data to analyze
                if ($grades->isEmpty() && $attendance->isEmpty()) {
                    // No data available
                    $performanceData = [
                        'no_data' => true,
                        'message' => 'No performance data available yet. Grades and attendance records will appear here once they are recorded.'
                    ];
                } else {
                    // Calculate core metrics using total_score field
                    $averageScore = $grades->avg('total_score') ?? 0;
                    $maxScore = $grades->max('total_score') ?? 0;
                    $minScore = $grades->min('total_score') ?? 0;
                    $bestGrade = $grades->sortByDesc('total_score')->first();
                    $worstGrade = $grades->sortBy('total_score')->first();
                    
                    $presentDays = $attendance->where('status', 'present')->count();
                    $totalDays = $attendance->count();
                    $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
                    
                    $totalTests = $grades->count();
                    $passedTests = $grades->where('total_score', '>=', 50)->count();
                    $failedTests = $grades->where('total_score', '<', 50)->count();
                    $passRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100) : 0;

                    // Group grades by subject name
                    $subjectStats = $grades->groupBy('subject.name')->map(function($subjectGrades) {
                        return [
                            'avg' => $subjectGrades->avg('total_score'),
                            'count' => $subjectGrades->count(),
                            'max' => $subjectGrades->max('total_score'),
                            'min' => $subjectGrades->min('total_score')
                        ];
                    });

                    // Generate AI-powered analysis only if we have grades
                    $analysis = $this->generatePerformanceAnalysis(
                        $selectedChild->first_name, 
                        $averageScore, 
                        $attendanceRate, 
                        $passRate, 
                        $bestGrade, 
                        $worstGrade, 
                        $subjectStats
                    );

                    $performanceData = [
                        'no_data' => false,
                        'overall_average' => round($averageScore, 2),
                        'best_subject' => $bestGrade && $bestGrade->subject ? $bestGrade->subject->name : 'N/A',
                        'best_score' => round($maxScore, 2),
                        'worst_subject' => $worstGrade && $worstGrade->subject ? $worstGrade->subject->name : 'N/A',
                        'worst_score' => round($minScore, 2),
                        'attendance_rate' => $attendanceRate,
                        'present_days' => $presentDays,
                        'total_days' => $totalDays,
                        'subjects' => $grades->map(fn($g) => [
                            'name' => $g->subject ? $g->subject->name : 'Unknown',
                            'score' => round($g->total_score, 2)
                        ])->values()->toArray(),
                        'total_tests' => $totalTests,
                        'passed_tests' => $passedTests,
                        'failed_tests' => $failedTests,
                        'pass_rate' => $passRate,
                        'recommendation' => $analysis['recommendation'],
                        'strengths' => $analysis['strengths'],
                        'improvements' => $analysis['improvements'],
                        'overall_rating' => $analysis['rating'],
                        'status_message' => $analysis['status']
                    ];
                }
            }
        }

        return view('parent-portal.performance', compact('children', 'selectedChild', 'performanceData'));
    }

    /**
     * View parent documents/forms
     */
    public function viewDocuments(Request $request)
    {
        $this->requireAnyPermission([
            'view-own-children',
            'view-own-bills',
            'view-own-payment-history'
        ]);

        $category = $request->query('category');
        $search = $request->query('search');

        // Mock documents data structure
        $documents = collect([
            (object)[
                'id' => 1,
                'title' => 'School Admission Form',
                'description' => 'Admission application form for new students',
                'category' => 'forms',
                'file_path' => 'documents/admission-form.pdf',
                'file_size' => 245000,
                'created_at' => now()->subDays(10)
            ],
            (object)[
                'id' => 2,
                'title' => 'School Policies Handbook',
                'description' => 'Complete school policies and regulations',
                'category' => 'policies',
                'file_path' => 'documents/policies.pdf',
                'file_size' => 520000,
                'created_at' => now()->subDays(30)
            ],
            (object)[
                'id' => 3,
                'title' => 'Annual Report 2024',
                'description' => 'School annual report for 2024 academic year',
                'category' => 'reports',
                'file_path' => 'documents/annual-report-2024.pdf',
                'file_size' => 1024000,
                'created_at' => now()->subDays(60)
            ]
        ]);

        if ($category) {
            $documents = $documents->filter(fn($d) => $d->category === $category);
        }

        if ($search) {
            $documents = $documents->filter(fn($d) => 
                str_contains(strtolower($d->title), strtolower($search)) ||
                str_contains(strtolower($d->description), strtolower($search))
            );
        }

        $categoryCounts = [
            'forms' => 5,
            'policies' => 3,
            'reports' => 4,
            'circulars' => 8
        ];

        return view('parent-portal.documents', compact('documents', 'categoryCounts'));
    }

    /**
     * Generate AI-powered performance analysis and recommendations
     */
    private function generatePerformanceAnalysis($studentName, $avgScore, $attendanceRate, $passRate, $bestGrade, $worstGrade, $subjectStats)
    {
        $strengths = [];
        $improvements = [];
        $recommendation = '';
        $rating = '';
        $status = '';

        // Academic Performance Analysis
        if ($avgScore >= 90) {
            $rating = 'Excellent';
            $strengths[] = "🌟 Outstanding academic performance with an average score of {$avgScore}%";
            $recommendation = "{$studentName} is demonstrating exceptional excellence across all subjects. Continue this outstanding performance and consider exploring advanced topics or mentoring peers.";
        } elseif ($avgScore >= 80) {
            $rating = 'Very Good';
            $strengths[] = "👍 Strong academic performance with an average score of {$avgScore}%";
            $recommendation = "{$studentName} shows consistent competence. Focus on closing the gap to achieve excellence in weaker areas.";
        } elseif ($avgScore >= 70) {
            $rating = 'Good';
            $strengths[] = "✓ Satisfactory performance with an average score of {$avgScore}%";
            $recommendation = "{$studentName} is meeting expectations. Increase effort in challenging subjects to improve overall performance.";
        } elseif ($avgScore >= 50) {
            $rating = 'Fair';
            $improvements[] = "⚠️ Performance below expectation - average score of {$avgScore}%. Immediate intervention recommended.";
            $recommendation = "{$studentName} needs extra support and tutoring. Engage with teachers to create an improvement plan.";
        } else {
            $rating = 'Poor';
            $improvements[] = "🚨 Critical performance concern - average score of {$avgScore}%. Urgent action required.";
            $recommendation = "{$studentName} requires immediate academic intervention. Schedule a meeting with teachers and consider tutoring support.";
        }

        // Attendance Analysis
        if ($attendanceRate >= 95) {
            $strengths[] = "📚 Excellent attendance with {$attendanceRate}% presence";
        } elseif ($attendanceRate >= 85) {
            $strengths[] = "📚 Good attendance rate of {$attendanceRate}%";
        } elseif ($attendanceRate >= 75) {
            $improvements[] = "⏰ Attendance below recommended level ({$attendanceRate}%). Regular presence is crucial for academic success.";
        } else {
            $improvements[] = "🔴 Critical attendance issue ({$attendanceRate}%). This is significantly impacting academic performance.";
        }

        // Pass Rate Analysis
        if ($passRate == 100) {
            $strengths[] = "🎯 Perfect pass rate - all assessments passed successfully";
        } elseif ($passRate >= 80) {
            $strengths[] = "🎯 High pass rate of {$passRate}% - strong test performance";
        } elseif ($passRate >= 50) {
            $improvements[] = "📊 Pass rate of {$passRate}% - some assessments need attention";
        } else {
            $improvements[] = "📊 Low pass rate of {$passRate}% - majority of assessments failing. Extra help needed.";
        }

        // Subject-specific Analysis
        if ($bestGrade && $bestGrade->subject) {
            $strengths[] = "⭐ Excelling in {$bestGrade->subject->name} with a score of " . round($bestGrade->total_score, 2) . "%";
        }

        if ($worstGrade && $worstGrade->subject) {
            $improvements[] = "📖 Focus on {$worstGrade->subject->name} - lowest performance at " . round($worstGrade->total_score, 2) . "%. Consider additional practice.";
        }

        // Subject Performance Pattern Analysis
        $strongSubjects = $subjectStats->filter(fn($s) => $s['avg'] >= 80)->keys()->toArray();
        $weakSubjects = $subjectStats->filter(fn($s) => $s['avg'] < 50)->keys()->toArray();

        if (!empty($strongSubjects)) {
            $strengths[] = "💪 Consistent strength in: " . implode(', ', $strongSubjects);
        }

        if (!empty($weakSubjects)) {
            $improvements[] = "🎓 Priority improvement areas: " . implode(', ', $weakSubjects) . ". Recommend tutoring or study groups.";
        }

        // Overall Status Determination
        if ($rating === 'Excellent' && $attendanceRate >= 95) {
            $status = "On Track - Exceptional Progress";
        } elseif ($rating === 'Very Good' || ($rating === 'Good' && $attendanceRate >= 85)) {
            $status = "On Track - Good Progress";
        } elseif ($rating === 'Good' || $rating === 'Fair') {
            $status = "Needs Improvement";
        } else {
            $status = "Critical - Urgent Intervention Needed";
        }

        // Ensure at least 3 improvement suggestions
        if (empty($improvements)) {
            $improvements[] = "Continue building on current strengths and maintain discipline";
            $improvements[] = "Explore advanced learning resources to deepen subject knowledge";
            $improvements[] = "Develop a consistent study schedule for continuous improvement";
        }

        return [
            'recommendation' => $recommendation,
            'strengths' => array_slice($strengths, 0, 4), // Top 4 strengths
            'improvements' => array_slice($improvements, 0, 4), // Top 4 areas to improve
            'rating' => $rating,
            'status' => $status
        ];
    }
}

