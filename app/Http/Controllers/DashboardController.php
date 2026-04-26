<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Result;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Unified Dashboard - Shows role-specific KPIs and analytics
     * 
     * NOTE: Students and Parents are redirected by RoleBasedRedirect middleware
     * Only Admin and Teacher roles access this unified dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        $dashboardData = [];
        
        // Admin Dashboard KPIs
        if ($user->hasRole('Administrator')) {
            $dashboardData['admin'] = $this->getAdminKPIs();
        }
        
        // Teacher Dashboard KPIs
        if ($user->hasRole('teacher')) {
            $dashboardData['teacher'] = $this->getTeacherKPIs($user);
        }
        
        // Get user role for greeting
        $userRole = $this->getUserRoleDisplay($user);
        $userName = $user->profile?->first_name ?? $user->name ?? 'User';
        
        return view('dashboard', compact(
            'dashboardData',
            'userRole',
            'userName',
            'user'
        ));
    }
    
    /**
     * Get Admin KPIs
     */
    private function getAdminKPIs()
    {
        $currentSession = AcademicSession::where('is_active', true)->first();
        
        return [
            'stats' => [
                [
                    'label' => 'Total Students',
                    'value' => Student::where('status', 'active')->count(),
                    'icon' => 'fas fa-user-graduate',
                    'color' => 'blue',
                    'trend' => '+12% from last month'
                ],
                [
                    'label' => 'Total Teachers',
                    'value' => User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count(),
                    'icon' => 'fas fa-chalkboard-user',
                    'color' => 'green',
                    'trend' => '+3 new this month'
                ],
                [
                    'label' => 'Active Classes',
                    'value' => SchoolClass::where('status', 'active')->count(),
                    'icon' => 'fas fa-school',
                    'color' => 'purple',
                    'trend' => 'All operating'
                ],
                [
                    'label' => 'Outstanding Bills',
                    'value' => StudentBill::where('status', '!=', 'paid')->sum('total_amount'),
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => 'orange',
                    'trend' => 'Action needed',
                    'format' => 'currency'
                ],
            ],
            'resultsOverview' => $this->getResultsOverview($currentSession),
            'recentPayments' => $this->getRecentPayments(5),
            'pendingBills' => StudentBill::where('status', '!=', 'paid')->count(),
        ];
    }
    
    /**
     * Get Teacher KPIs
     */
    private function getTeacherKPIs($user)
    {
        $profile = $user->profile;
        $classes = $profile->teacher_classes()->get();
        $classIds = $classes->pluck('id');
        
        $currentSession = AcademicSession::where('is_active', true)->first();
        $currentTerm = AcademicTerm::where('academic_session_id', $currentSession->id ?? null)
            ->where('is_active', true)
            ->first();
        
        $totalStudents = Student::whereIn('school_class_id', $classIds)->where('status', 'active')->count();
        $resultCount = Result::whereIn('school_class_id', $classIds)
            ->where('academic_session_id', $currentSession->id ?? null)
            ->where('academic_term_id', $currentTerm->id ?? null)
            ->count();
        $expectedResults = $totalStudents * Subject::count();
        $completionPercentage = $expectedResults > 0 ? round(($resultCount / $expectedResults) * 100) : 0;
        
        return [
            'stats' => [
                [
                    'label' => 'My Classes',
                    'value' => $classes->count(),
                    'icon' => 'fas fa-book',
                    'color' => 'blue',
                    'trend' => 'All classes'
                ],
                [
                    'label' => 'Total Students',
                    'value' => $totalStudents,
                    'icon' => 'fas fa-user-graduate',
                    'color' => 'green',
                    'trend' => 'Active students'
                ],
                [
                    'label' => 'Results Entered',
                    'value' => $resultCount,
                    'icon' => 'fas fa-chart-line',
                    'color' => 'purple',
                    'trend' => "{$completionPercentage}% complete"
                ],
                [
                    'label' => 'Subjects Teaching',
                    'value' => Subject::count(),
                    'icon' => 'fas fa-book-open',
                    'color' => 'orange',
                    'trend' => 'Assigned'
                ],
            ],
            'classBreakdown' => $this->getTeacherClassBreakdown($classes, $currentSession, $currentTerm),
            'recentResults' => $this->getTeacherRecentResults($classIds, 5),
        ];
    }
    
    /**
     * Get Student KPIs
     */
    private function getStudentKPIs($user)
    {
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return ['stats' => []];
        }
        
        $currentSession = AcademicSession::where('is_active', true)->first();
        $currentTerm = AcademicTerm::where('academic_session_id', $currentSession->id ?? null)
            ->where('is_active', true)
            ->first();
        
        $results = Result::where('student_id', $student->id)
            ->where('academic_session_id', $currentSession->id ?? null)
            ->where('academic_term_id', $currentTerm->id ?? null)
            ->get();
        
        $averageScore = $results->count() > 0 ? round($results->avg('total_score'), 2) : 0;
        $passingScore = 40; // Default
        $passCount = $results->where('total_score', '>=', $passingScore)->count();
        
        return [
            'stats' => [
                [
                    'label' => 'My Class',
                    'value' => $student->schoolClass?->name ?? 'N/A',
                    'icon' => 'fas fa-book',
                    'color' => 'blue',
                    'trend' => 'Current placement'
                ],
                [
                    'label' => 'Subjects',
                    'value' => $results->count(),
                    'icon' => 'fas fa-book-open',
                    'color' => 'green',
                    'trend' => 'This term'
                ],
                [
                    'label' => 'Average Score',
                    'value' => $averageScore,
                    'icon' => 'fas fa-star',
                    'color' => 'purple',
                    'trend' => 'Cumulative'
                ],
                [
                    'label' => 'Passed',
                    'value' => $passCount . '/' . $results->count(),
                    'icon' => 'fas fa-check-circle',
                    'color' => 'green' . ($passCount == $results->count() ? '' : '-disabled'),
                    'trend' => 'Subjects passed'
                ],
            ],
            'recentResults' => $this->getStudentRecentResults($student, 5),
        ];
    }
    
    /**
     * Get Parent KPIs
     */
    private function getParentKPIs($user)
    {
        $parent = $user->profile;
        $children = Student::where('parent_id', $parent->id)->get();
        
        $totalOutstanding = StudentBill::whereIn('student_id', $children->pluck('id'))
            ->where('status', '!=', 'paid')
            ->sum('total_amount');
        
        $totalPaid = Payment::whereIn('student_id', $children->pluck('id'))->sum('amount');
        $pendingBills = StudentBill::whereIn('student_id', $children->pluck('id'))
            ->where('status', '!=', 'paid')
            ->count();
        
        return [
            'stats' => [
                [
                    'label' => 'My Children',
                    'value' => $children->count(),
                    'icon' => 'fas fa-children',
                    'color' => 'blue',
                    'trend' => 'Active students'
                ],
                [
                    'label' => 'Outstanding Bills',
                    'value' => $totalOutstanding,
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => 'red',
                    'trend' => $pendingBills . ' pending',
                    'format' => 'currency'
                ],
                [
                    'label' => 'Total Paid',
                    'value' => $totalPaid,
                    'icon' => 'fas fa-credit-card',
                    'color' => 'green',
                    'trend' => 'This session',
                    'format' => 'currency'
                ],
                [
                    'label' => 'Pending Bills',
                    'value' => $pendingBills,
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'orange',
                    'trend' => 'Needs attention'
                ],
            ],
            'childrenOverview' => $this->getChildrenOverview($children),
        ];
    }
    
    /**
     * Helper: Get results overview
     */
    private function getResultsOverview($session)
    {
        if (!$session) {
            return null;
        }
        
        $terms = AcademicTerm::where('academic_session_id', $session->id)->get();
        
        return $terms->map(function ($term) use ($session) {
            $resultCount = Result::where('academic_session_id', $session->id)
                ->where('academic_term_id', $term->id)
                ->count();
            
            return [
                'term' => $term->term,
                'count' => $resultCount,
            ];
        });
    }
    
    /**
     * Helper: Get recent payments
     */
    private function getRecentPayments($limit = 5)
    {
        return Payment::with('student')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($p) => [
                'student' => $p->student?->first_name . ' ' . $p->student?->last_name,
                'amount' => $p->amount,
                'date' => $p->created_at->format('M d, Y'),
            ]);
    }
    
    /**
     * Helper: Get teacher class breakdown
     */
    private function getTeacherClassBreakdown($classes, $session, $term)
    {
        if (!$session || !$term) {
            return null;
        }
        
        return $classes->map(function ($class) use ($session, $term) {
            $students = Student::where('school_class_id', $class->id)->where('status', 'active')->count();
            $results = Result::where('school_class_id', $class->id)
                ->where('academic_session_id', $session->id)
                ->where('academic_term_id', $term->id)
                ->count();
            
            return [
                'name' => $class->name,
                'students' => $students,
                'results' => $results,
            ];
        });
    }
    
    /**
     * Helper: Get teacher recent results
     */
    private function getTeacherRecentResults($classIds, $limit = 5)
    {
        return Result::whereIn('school_class_id', $classIds)
            ->latest()
            ->limit($limit)
            ->with('student', 'subject', 'schoolClass')
            ->get()
            ->map(fn($r) => [
                'student' => $r->student?->first_name . ' ' . $r->student?->last_name,
                'subject' => $r->subject?->name,
                'score' => $r->total_score,
                'class' => $r->schoolClass?->name,
            ]);
    }
    
    /**
     * Helper: Get student recent results
     */
    private function getStudentRecentResults($student, $limit = 5)
    {
        return Result::where('student_id', $student->id)
            ->latest()
            ->limit($limit)
            ->with('subject', 'academicTerm')
            ->get()
            ->map(fn($r) => [
                'subject' => $r->subject?->name,
                'score' => $r->total_score,
                'term' => $r->academicTerm?->term,
                'date' => $r->created_at->format('M d, Y'),
            ]);
    }
    
    /**
     * Helper: Get children overview
     */
    private function getChildrenOverview($children)
    {
        return $children->map(function ($child) {
            $outstandingBills = StudentBill::where('student_id', $child->id)
                ->where('status', '!=', 'paid')
                ->sum('total_amount');
            
            return [
                'name' => $child->first_name . ' ' . $child->last_name,
                'class' => $child->schoolClass?->name ?? 'N/A',
                'outstanding' => $outstandingBills,
                'admission' => $child->admission_number,
            ];
        });
    }
    
    /**
     * Helper: Get user role display name
     */
    private function getUserRoleDisplay($user)
    {
        if ($user->hasRole('Administrator')) {
            return 'Administrator';
        } elseif ($user->hasRole('teacher')) {
            return 'Teacher';
        } elseif ($user->hasRole('student')) {
            return 'Student';
        } elseif ($user->hasRole('parent')) {
            return 'Parent';
        }
        
        return 'User';
    }
}
