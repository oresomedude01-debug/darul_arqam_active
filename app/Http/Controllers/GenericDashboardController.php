<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class GenericDashboardController extends Controller
{
    /**
     * Generic Dashboard - Permission-based KPIs for other roles
     */
    public function index()
    {
        $user = auth()->user();
        
        // Only for users who are not Admin, Teacher, Student, or Parent
        if ($user->hasAnyRole(['Administrator', 'teacher', 'student', 'parent'])) {
            return redirect()->route('dashboard');
        }

        $currentSession = AcademicSession::where('is_active', true)->first();

        $data = [
            // Dynamic KPIs based on permissions
            'kpis' => $this->getPermissionBasedKPIs($user),
            
            // Permission-specific charts
            'systemOverview' => $this->getSystemOverview(),
            'studentDistribution' => $this->getStudentDistributionChart($user),
            'classEnrollment' => $this->getClassEnrollmentOverview($user),
            'attendanceSummary' => $this->getAttendanceSummary($user),
            'resultsOverview' => $this->getResultsOverview($user),
            'billingOverview' => $this->getBillingOverview($user),
            'userRolesDistribution' => $this->getUserRolesDistribution($user),
            'accessibleData' => $this->getAccessibleData($user),
            'userRole' => $this->getUserRoleLabel($user),
            'roleLabel' => $this->getUserRoleLabel($user),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'currentSession' => $currentSession,
        ];

        return view('dashboard.generic', compact('data', 'user'));
    }

    /**
     * Get permission-based KPIs
     */
    private function getPermissionBasedKPIs($user)
    {
        $kpis = [];

        // Permission-based KPI 1: View Students
        if ($user->can('view-students') || $user->hasPermissionTo('view-students')) {
            $activeStudents = Student::where('status', 'active')->count();
            $totalStudents = Student::count();
            $kpis[] = [
                'label' => 'Total Students',
                'value' => $totalStudents,
                'icon' => 'fas fa-user-graduate',
                'color' => 'blue',
                'permission' => 'view-students',
                'description' => $activeStudents . ' active',
                'subtext' => 'Students in system'
            ];
        }

        // Permission-based KPI 2: View Teachers
        if ($user->can('view-teachers') || $user->hasPermissionTo('view-teachers')) {
            $activeTeachers = User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))
                ->where('active', true)->count();
            $totalTeachers = User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count();
            $kpis[] = [
                'label' => 'Total Teachers',
                'value' => $totalTeachers,
                'icon' => 'fas fa-chalkboard-user',
                'color' => 'green',
                'permission' => 'view-teachers',
                'description' => $activeTeachers . ' active',
                'subtext' => 'Teaching staff'
            ];
        }

        // Permission-based KPI 3: View Classes
        if ($user->can('view-classes') || $user->hasPermissionTo('view-classes')) {
            $activeClasses = SchoolClass::where('status', 'active')->count();
            $totalClasses = SchoolClass::count();
            $kpis[] = [
                'label' => 'Active Classes',
                'value' => $activeClasses,
                'icon' => 'fas fa-school',
                'color' => 'purple',
                'permission' => 'view-classes',
                'description' => $totalClasses . ' total',
                'subtext' => 'Operating classes'
            ];
        }

        // Permission-based KPI 4: View Users
        if ($user->can('view-users') || $user->hasPermissionTo('view-users')) {
            $activeUsers = User::where('active', true)->count();
            $totalUsers = User::count();
            $kpis[] = [
                'label' => 'Total System Users',
                'value' => $totalUsers,
                'icon' => 'fas fa-users',
                'color' => 'orange',
                'permission' => 'view-users',
                'description' => $activeUsers . ' active',
                'subtext' => 'All system users'
            ];
        }

        // Permission-based KPI 5: Manage Settings
        if ($user->can('manage-settings') || $user->hasPermissionTo('manage-settings')) {
            $activeSessions = AcademicSession::where('is_active', true)->count();
            $totalSessions = AcademicSession::count();
            $kpis[] = [
                'label' => 'Active Sessions',
                'value' => $activeSessions,
                'icon' => 'fas fa-calendar-alt',
                'color' => 'teal',
                'permission' => 'manage-settings',
                'description' => $totalSessions . ' total',
                'subtext' => 'Academic sessions'
            ];
        }

        // Permission-based KPI 6: Manage Payments
        if ($user->can('manage-payments') || $user->hasPermissionTo('manage-payments')) {
            $unpaidBills = StudentBill::where('status', '!=', 'paid')->count();
            $totalBills = StudentBill::count();
            $kpis[] = [
                'label' => 'Billing Overview',
                'value' => $totalBills,
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'red',
                'permission' => 'manage-payments',
                'description' => $unpaidBills . ' unpaid',
                'subtext' => 'Total bills'
            ];
        }

        // Permission-based KPI 7: View Results
        if ($user->can('view-results') || $user->hasPermissionTo('view-results')) {
            $resultCount = Result::count();
            $kpis[] = [
                'label' => 'Academic Results',
                'value' => $resultCount,
                'icon' => 'fas fa-chart-bar',
                'color' => 'indigo',
                'permission' => 'view-results',
                'description' => 'Results recorded',
                'subtext' => 'Student grades'
            ];
        }

        // Permission-based KPI 8: Manage Attendance
        if ($user->can('manage-attendance') || $user->hasPermissionTo('manage-attendance')) {
            $presentCount = Attendance::where('status', 'present')->count();
            $totalAttendance = Attendance::count();
            $rate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;
            $kpis[] = [
                'label' => 'System Attendance',
                'value' => $rate . '%',
                'icon' => 'fas fa-check-circle',
                'color' => 'emerald',
                'permission' => 'manage-attendance',
                'description' => 'Present rate',
                'subtext' => 'Attendance tracking'
            ];
        }

        return $kpis;
    }

    /**
     * Get system overview - general stats visible to all
     */
    private function getSystemOverview()
    {
        return [
            'labels' => ['Students', 'Teachers', 'Classes', 'Sessions'],
            'datasets' => [
                [
                    'data' => [
                        Student::where('status', 'active')->count(),
                        User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count(),
                        SchoolClass::where('status', 'active')->count(),
                        AcademicSession::count()
                    ],
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get accessible data based on user permissions
     */
    private function getAccessibleData($user)
    {
        $accessible = [];

        if ($user->can('view-students') || $user->hasPermissionTo('view-students')) {
            $accessible['students'] = [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'inactive' => Student::where('status', 'inactive')->count(),
            ];
        }

        if ($user->can('view-teachers') || $user->hasPermissionTo('view-teachers')) {
            $accessible['teachers'] = [
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))
                    ->where('active', true)->count(),
            ];
        }

        if ($user->can('view-classes') || $user->hasPermissionTo('view-classes')) {
            $accessible['classes'] = [
                'total' => SchoolClass::count(),
                'active' => SchoolClass::where('status', 'active')->count(),
            ];
        }

        if ($user->can('view-results') || $user->hasPermissionTo('view-results')) {
            $accessible['hasResults'] = true;
        }

        if ($user->can('manage-payments') || $user->hasPermissionTo('manage-payments')) {
            $accessible['hasPayments'] = true;
        }

        return $accessible;
    }

    /**
     * Get user role label
     */
    private function getUserRoleLabel($user)
    {
        $roles = $user->roles()->pluck('name')->toArray();
        return count($roles) > 0 ? implode(', ', $roles) : 'Staff';
    }

    /**
     * Get permission-based students distribution
     */
    public function getStudentDistributionChart($user)
    {
        if (!($user->can('view-students') || $user->hasPermissionTo('view-students'))) {
            return null;
        }

        $activeStudents = Student::where('status', 'active')->count();
        $inactiveStudents = Student::where('status', 'inactive')->count();

        return [
            'labels' => ['Active', 'Inactive'],
            'datasets' => [
                [
                    'data' => [$activeStudents, $inactiveStudents],
                    'backgroundColor' => ['#10b981', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get permission-based class enrollment overview
     */
    public function getClassEnrollmentOverview($user)
    {
        if (!($user->can('view-classes') || $user->hasPermissionTo('view-classes'))) {
            return null;
        }

        $classes = SchoolClass::active()->with('students')->limit(8)->get();
        
        return [
            'labels' => $classes->pluck('full_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Students Enrolled',
                    'data' => $classes->map(fn($c) => $c->students()->count()),
                    'backgroundColor' => '#3b82f6'
                ]
            ]
        ];
    }

    /**
     * Get permission-based attendance summary
     */
    public function getAttendanceSummary($user)
    {
        if (!($user->can('manage-attendance') || $user->hasPermissionTo('manage-attendance'))) {
            return null;
        }

        $present = Attendance::where('status', 'present')->count();
        $absent = Attendance::where('status', 'absent')->count();
        $late = Attendance::where('status', 'late')->count();
        $total = $present + $absent + $late;

        return [
            'labels' => ['Present', 'Absent', 'Late'],
            'datasets' => [
                [
                    'data' => [
                        $total > 0 ? round(($present / $total) * 100, 1) : 0,
                        $total > 0 ? round(($absent / $total) * 100, 1) : 0,
                        $total > 0 ? round(($late / $total) * 100, 1) : 0
                    ],
                    'backgroundColor' => ['#10b981', '#ef4444', '#f59e0b']
                ]
            ]
        ];
    }

    /**
     * Get permission-based results overview
     */
    public function getResultsOverview($user)
    {
        if (!($user->can('view-results') || $user->hasPermissionTo('view-results'))) {
            return null;
        }

        $excellentCount = Result::where('score', '>=', 80)->count();
        $goodCount = Result::whereBetween('score', [70, 79])->count();
        $averageCount = Result::whereBetween('score', [60, 69])->count();
        $belowAverageCount = Result::where('score', '<', 60)->count();

        return [
            'labels' => ['Excellent', 'Good', 'Average', 'Below Average'],
            'datasets' => [
                [
                    'data' => [$excellentCount, $goodCount, $averageCount, $belowAverageCount],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get permission-based billing overview
     */
    public function getBillingOverview($user)
    {
        if (!($user->can('manage-payments') || $user->hasPermissionTo('manage-payments'))) {
            return null;
        }

        $paid = StudentBill::where('status', 'paid')->sum('total_amount');
        $pending = StudentBill::where('status', 'pending')->sum('total_amount');
        $overdue = StudentBill::where('status', 'overdue')->sum('total_amount');

        return [
            'labels' => ['Paid', 'Pending', 'Overdue'],
            'datasets' => [
                [
                    'data' => [$paid, $pending, $overdue],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get permission-based user roles distribution
     */
    public function getUserRolesDistribution($user)
    {
        if (!($user->can('view-users') || $user->hasPermissionTo('view-users'))) {
            return null;
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count();
        $teachers = User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count();
        $students = User::whereHas('roles', fn($q) => $q->where('slug', 'student'))->count();
        $parents = User::whereHas('roles', fn($q) => $q->where('slug', 'parent'))->count();
        $others = User::whereHas('roles', fn($q) => $q->whereNotIn('slug', ['admin', 'teacher', 'student', 'parent']))->count();

        return [
            'labels' => ['Admins', 'Teachers', 'Students', 'Parents', 'Others'],
            'datasets' => [
                [
                    'data' => [$admins, $teachers, $students, $parents, $others],
                    'backgroundColor' => ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#06b6d4']
                ]
            ]
        ];
    }
}
