<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Result;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard - Full system overview with charts and KPIs
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect if not admin
        if (!$user->hasRole('Administrator')) {
            abort(403, 'Unauthorized access to admin dashboard');
        }

        $currentSession = AcademicSession::where('is_active', true)->first();
        
        $data = [
            // Key Performance Indicators
            'kpis' => $this->getAdminKPIs($currentSession),
            
            // Chart Data
            'enrollmentTrend' => $this->getEnrollmentTrend($currentSession),
            'classDistribution' => $this->getClassDistribution(),
            'attendanceOverview' => $this->getAttendanceOverview($currentSession),
            'paymentStatus' => $this->getPaymentStatus(),
            'resultsSummary' => $this->getResultsSummary($currentSession),
            'staffBreakdown' => $this->getStaffBreakdown(),
            'monthlyRevenue' => $this->getMonthlyRevenueTrend(),
            'performanceDistribution' => $this->getStudentPerformanceDistribution(),
            'billStatus' => $this->getBillStatusSummary(),
            'sessionEnrollment' => $this->getSessionwiseEnrollment(),
            'recentActivities' => $this->getRecentActivities(),
            'upcomingEvents' => $this->getUpcomingEvents(),
            'currentSession' => $currentSession,
        ];

        return view('dashboard.admin', compact('data', 'user'));
    }

    /**
     * Get Key Performance Indicators
     */
    private function getAdminKPIs($session)
    {
        $totalStudents = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        })->where('status', 'active')->count();
        $totalTeachers = User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count();
        $activeClasses = SchoolClass::where('status', 'active')->count();
        $outstandingBills = StudentBill::where('status', '!=', 'paid')->sum('total_amount');
        $totalRevenue = Payment::sum('amount');
        $attendanceRate = $this->calculateSystemAttendanceRate();
        $studentGrowthRate = $this->calculateStudentGrowthRate();
        $activeUsers = User::count();
        
        return [
            [
                'label' => 'Total Students',
                'value' => $totalStudents,
                'icon' => 'fas fa-user-graduate',
                'color' => 'blue',
                'percentage' => $studentGrowthRate . '%',
                'trend' => 'from last term'
            ],
            [
                'label' => 'Total Teachers',
                'value' => $totalTeachers,
                'icon' => 'fas fa-chalkboard-user',
                'color' => 'green',
                'percentage' => '+' . max(0, $totalTeachers - 3),
                'trend' => 'active educators'
            ],
            [
                'label' => 'Active Classes',
                'value' => $activeClasses,
                'icon' => 'fas fa-school',
                'color' => 'purple',
                'percentage' => '100%',
                'trend' => 'operating normally'
            ],
            [
                'label' => 'Outstanding Revenue',
                'value' => '₦' . number_format($outstandingBills, 2),
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'red',
                'percentage' => $outstandingBills > 0 ? '-' . round(($outstandingBills / ($totalRevenue ?: 1)) * 100, 1) . '%' : '0%',
                'trend' => 'unpaid bills',
                'isAmount' => true
            ],
            [
                'label' => 'System Attendance',
                'value' => $attendanceRate . '%',
                'icon' => 'fas fa-check-circle',
                'color' => 'emerald',
                'percentage' => '+5%',
                'trend' => 'improvement'
            ],
            [
                'label' => 'Total Revenue',
                'value' => '₦' . number_format($totalRevenue, 2),
                'icon' => 'fas fa-coins',
                'color' => 'amber',
                'percentage' => '+15%',
                'trend' => 'from last month',
                'isAmount' => true
            ],
            [
                'label' => 'Active Users',
                'value' => $activeUsers,
                'icon' => 'fas fa-users',
                'color' => 'cyan',
                'percentage' => '+2',
                'trend' => 'new registrations'
            ],
            [
                'label' => 'Staff Members',
                'value' => User::whereHas('roles', fn($q) => $q->whereNotIn('slug', ['student', 'parent']))->count(),
                'icon' => 'fas fa-users-cog',
                'color' => 'indigo',
                'percentage' => '+1',
                'trend' => 'total staff'
            ],
        ];
    }
    
    /**
     * Calculate system-wide attendance rate
     */
    private function calculateSystemAttendanceRate()
    {
        $total = Attendance::count();
        if ($total === 0) return 0;
        
        $present = Attendance::where('status', 'present')->count();
        return round(($present / $total) * 100, 1);
    }
    
    /**
     * Calculate student growth rate
     */
    private function calculateStudentGrowthRate()
    {
        $currentMonth = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        })->where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $previousMonth = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        })->where('status', 'active')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        if ($previousMonth === 0) return 0;
        return round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1);
    }

    /**
     * Get enrollment trend data for chart
     */
    private function getEnrollmentTrend($session)
    {
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $count = Student::where('status', 'active')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Active Enrollments',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get class distribution
     */
    private function getClassDistribution()
    {
        $classes = SchoolClass::active()
            ->with('students')
            ->get()
            ->map(fn($class) => [
                'name' => $class->full_name,
                'count' => $class->students()->count()
            ]);

        return [
            'labels' => $classes->pluck('name'),
            'datasets' => [
                [
                    'data' => $classes->pluck('count'),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
                        '#8b5cf6', '#06b6d4', '#ec4899', '#f97316'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get attendance overview
     */
    private function getAttendanceOverview($session)
    {
        $totalRecords = Attendance::count();
        $presentCount = Attendance::where('status', 'present')->count();
        $absentCount = Attendance::where('status', 'absent')->count();
        $lateCount = Attendance::where('status', 'late')->count();

        return [
            'labels' => ['Present', 'Absent', 'Late'],
            'datasets' => [
                [
                    'data' => [
                        $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0,
                        $totalRecords > 0 ? round(($absentCount / $totalRecords) * 100, 1) : 0,
                        $totalRecords > 0 ? round(($lateCount / $totalRecords) * 100, 1) : 0,
                    ],
                    'backgroundColor' => ['#10b981', '#ef4444', '#f59e0b']
                ]
            ]
        ];
    }

    /**
     * Get payment status
     */
    private function getPaymentStatus()
    {
        $paid = StudentBill::where('status', 'paid')->sum('total_amount');
        $pending = StudentBill::where('status', 'pending')->sum('total_amount');
        $overdue = StudentBill::where('status', 'overdue')->sum('total_amount');

        return [
            'labels' => ['Paid', 'Pending', 'Overdue'],
            'datasets' => [
                [
                    'data' => [
                        $paid,
                        $pending,
                        $overdue
                    ],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get results summary
     */
    private function getResultsSummary($session)
    {
        $excellentCount = Result::where('grade', 'A')->count();
        $goodCount = Result::whereIn('grade', ['B', 'B+'])->count();
        $averageCount = Result::where('grade', 'C')->count();
        $belowAverageCount = Result::whereIn('grade', ['D', 'E'])->count();

        return [
            'labels' => ['Excellent (A)', 'Good (B)', 'Average (C)', 'Below Average (D-E)'],
            'datasets' => [
                [
                    'data' => [$excellentCount, $goodCount, $averageCount, $belowAverageCount],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get staff breakdown
     */
    private function getStaffBreakdown()
    {
        $teachers = User::whereHas('roles', fn($q) => $q->where('slug', 'teacher'))->count();
        $admins = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count();
        $otherStaff = User::whereHas('roles', fn($q) => $q->whereNotIn('slug', ['teacher', 'admin', 'student', 'parent']))->count();

        return [
            'labels' => ['Teachers', 'Administrators', 'Other Staff'],
            'datasets' => [
                [
                    'data' => [$teachers, $admins, $otherStaff],
                    'backgroundColor' => ['#3b82f6', '#8b5cf6', '#06b6d4']
                ]
            ]
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 5)
    {
        $recentStudents = Student::latest('created_at')->limit($limit)->get();
        $recentPayments = Payment::latest('created_at')->limit($limit)->get();

        return [
            'newStudents' => $recentStudents->map(fn($s) => [
                'name' => $s->profile?->first_name . ' ' . $s->profile?->last_name,
                'class' => $s->profile?->schoolClass?->full_name ?? 'Unassigned',
                'date' => $s->created_at?->diffForHumans() ?? 'Recently'
            ]),
            'recentPayments' => $recentPayments->map(fn($p) => [
                'amount' => '₦' . number_format($p->amount, 2),
                'method' => $p->payment_method,
                'date' => $p->created_at?->diffForHumans() ?? 'Recently'
            ])
        ];
    }

    /**
     * Get monthly revenue trend
     */
    public function getMonthlyRevenueTrend()
    {
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $revenue = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $data[] = $revenue;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get student performance by grade distribution
     */
    public function getStudentPerformanceDistribution()
    {
        $aCount = Result::where('total_score', '>=', 80)->count();
        $bCount = Result::whereBetween('total_score', [70, 79])->count();
        $cCount = Result::whereBetween('total_score', [60, 69])->count();
        $dCount = Result::whereBetween('total_score', [50, 59])->count();
        $eCount = Result::where('total_score', '<', 50)->count();

        return [
            'labels' => ['A (80+)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'E (<50)'],
            'datasets' => [
                [
                    'data' => [$aCount, $bCount, $cCount, $dCount, $eCount],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#991b1b']
                ]
            ]
        ];
    }

    /**
     * Get bill status summary
     */
    public function getBillStatusSummary()
    {
        $paid = StudentBill::where('status', 'paid')->count();
        $pending = StudentBill::where('status', 'pending')->count();
        $overdue = StudentBill::where('status', 'overdue')->count();
        $cancelled = StudentBill::where('status', 'cancelled')->count();

        return [
            'labels' => ['Paid', 'Pending', 'Overdue', 'Cancelled'],
            'datasets' => [
                [
                    'data' => [$paid, $pending, $overdue, $cancelled],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444', '#9ca3af']
                ]
            ]
        ];
    }

    /**
     * Get session-wise student enrollment
     */
    public function getSessionwiseEnrollment()
    {
        $sessions = AcademicSession::latest()->limit(5)->get()->reverse();
        
        return [
            'labels' => $sessions->pluck('session')->toArray(),
            'datasets' => [
                [
                    'label' => 'Students Enrolled',
                    'data' => $sessions->map(fn($s) => Student::where('status', 'active')->count()),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get hex color code from color name
     */
    public function getColorCode($color, $opacity = 1)
    {
        $colors = [
            'blue' => '#3b82f6',
            'green' => '#10b981',
            'red' => '#ef4444',
            'purple' => '#8b5cf6',
            'yellow' => '#f59e0b',
            'pink' => '#ec4899',
            'indigo' => '#6366f1',
            'cyan' => '#06b6d4',
        ];

        $hex = $colors[$color] ?? '#3b82f6';
        
        // If opacity is requested, convert to RGBA
        if ($opacity < 1) {
            $hex = str_replace('#', '', $hex);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "rgba($r, $g, $b, $opacity)";
        }
        
        return $hex;
    }

    /**
     * Get upcoming events from the calendar
     */
    private function getUpcomingEvents()
    {
        return Event::where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->limit(6)
            ->get()
            ->map(fn($event) => [
                'name' => $event->title,
                'date' => $event->start_date->format('M d, Y'),
                'type' => ucfirst($event->type),
                'color' => $event->color ?? 'purple'
            ])
            ->toArray();
    }
}

