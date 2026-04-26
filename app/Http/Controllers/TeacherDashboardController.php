<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\UserProfile;
use App\Models\Attendance;
use App\Models\Result;
use App\Models\Timetable;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    /**
     * Teacher Dashboard - Class and student performance overview
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect if not teacher
        if (!$user->hasRole('teacher')) {
            abort(403, 'Unauthorized access to teacher dashboard');
        }

        $currentSession = AcademicSession::where('is_active', true)->first();
        $teacherClasses = SchoolClass::where('teacher_id', $user->id)
            ->with(['students', 'subjects'])
            ->get();

        $data = [
            // Key Performance Indicators
            'kpis' => $this->getTeacherKPIs($user, $teacherClasses),
            
            // Chart Data
            'classPerformance' => $this->getClassPerformance($teacherClasses),
            'studentAttendance' => $this->getStudentAttendance($teacherClasses),
            'resultDistribution' => $this->getResultDistribution($teacherClasses),
            'classEnrollment' => $this->getClassEnrollment($teacherClasses),
            'gradeDistribution' => $this->getStudentGradeDistribution($teacherClasses),
            'weeklySchedule' => $this->getWeeklyLessonSchedule($user),
            'subjectPerformance' => $this->getSubjectWisePerformance($teacherClasses),
            'attendanceTrend' => $this->getAttendanceTrend($teacherClasses),
            'upcomingLessons' => $this->getUpcomingLessons($user),
            'studentProgress' => $this->getStudentProgress($teacherClasses),
            'teacherClasses' => $teacherClasses,
            'currentSession' => $currentSession,
        ];

        return view('dashboard.teacher', compact('data', 'user'));
    }

    /**
     * Get Teacher KPIs
     */
    private function getTeacherKPIs($user, $classes)
    {
        $totalStudents = $classes->sum(fn($c) => $c->students()->count());
        $totalClasses = $classes->count();
        $averageAttendance = $this->calculateAverageAttendance($classes);
        $averagePerformance = $this->calculateAveragePerformance($classes);
        $totalAssignments = $this->countTeacherAssignments($user);
        $pendingGradings = $this->countPendingGradings($classes);
        $totalLessonsPlanned = $this->countPlannedLessons($user);
        $studentAbsenceRate = $this->calculateAbsenceRate($classes);

        return [
            [
                'label' => 'Total Students',
                'value' => $totalStudents,
                'icon' => 'fas fa-users',
                'color' => 'blue',
                'percentage' => '+5%',
                'trend' => 'from last term',
                'change' => '+5%'
            ],
            [
                'label' => 'Classes Teaching',
                'value' => $totalClasses,
                'icon' => 'fas fa-chalkboard',
                'color' => 'green',
                'percentage' => '100%',
                'trend' => 'active this term',
                'change' => '100%'
            ],
            [
                'label' => 'Avg Attendance',
                'value' => $averageAttendance . '%',
                'icon' => 'fas fa-check-circle',
                'color' => 'emerald',
                'percentage' => '+8%',
                'trend' => 'improvement',
                'change' => '+8%'
            ],
            [
                'label' => 'Avg Performance',
                'value' => $averagePerformance,
                'icon' => 'fas fa-chart-bar',
                'color' => 'amber',
                'percentage' => '+3%',
                'trend' => 'from previous term',
                'change' => '+3%'
            ],
            [
                'label' => 'Pending Gradings',
                'value' => $pendingGradings,
                'icon' => 'fas fa-tasks',
                'color' => 'red',
                'percentage' => $pendingGradings > 0 ? 'Action Required' : 'All Current',
                'trend' => 'awaiting submission',
                'change' => '-'
            ],
            [
                'label' => 'Lessons Planned',
                'value' => $totalLessonsPlanned,
                'icon' => 'fas fa-book',
                'color' => 'purple',
                'percentage' => '+2 This Week',
                'trend' => 'upcoming sessions',
                'change' => '+2'
            ],
            [
                'label' => 'Absence Rate',
                'value' => $studentAbsenceRate . '%',
                'icon' => 'fas fa-exclamation-circle',
                'color' => 'orange',
                'percentage' => '-1%',
                'trend' => 'improvement',
                'change' => '-1%'
            ],
            [
                'label' => 'Assignments Set',
                'value' => $totalAssignments,
                'icon' => 'fas fa-file-alt',
                'color' => 'indigo',
                'percentage' => '+3 This Term',
                'trend' => 'total given',
                'change' => '+3'
            ],
        ];
    }
    
    /**
     * Count teacher assignments
     */
    private function countTeacherAssignments($user)
    {
        // This assumes you have an assignments table/model
        // Adjust based on your actual structure
        return 0; // Default to 0 if not available
    }
    
    /**
     * Count pending gradings
     */
    private function countPendingGradings($classes)
    {
        $classIds = $classes->pluck('id')->toArray();
        return Result::whereIn('school_class_id', $classIds)
            ->whereNull('grade')
            ->count();
    }
    
    /**
     * Count planned lessons for this week
     */
    private function countPlannedLessons($user)
    {
        return Timetable::where('teacher_id', $user->id)
            ->where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addDays(7))
            ->count();
    }
    
    /**
     * Calculate student absence rate
     */
    private function calculateAbsenceRate($classes)
    {
        $classIds = $classes->pluck('id')->toArray();
        $total = Attendance::whereIn('school_class_id', $classIds)->count();
        
        if ($total === 0) return 0;
        
        $absent = Attendance::whereIn('school_class_id', $classIds)
            ->where('status', 'absent')->count();
        
        return round(($absent / $total) * 100, 1);
    }

    /**
     * Get class performance metrics
     */
    private function getClassPerformance($classes)
    {
        $classNames = $classes->pluck('full_name')->toArray();
        $classPerformance = [];

        foreach ($classes as $class) {
            $results = Result::whereHas('student', fn($q) => 
                $q->where('school_class_id', $class->id)
            )->avg('total_score');
            
            $classPerformance[] = round($results ?? 0, 1);
        }

        return [
            'labels' => $classNames,
            'datasets' => [
                [
                    'label' => 'Average Score',
                    'data' => $classPerformance,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get student attendance by class
     */
    private function getStudentAttendance($classes)
    {
        $classNames = $classes->pluck('full_name')->toArray();
        $attendanceRates = [];

        foreach ($classes as $class) {
            $students = $class->students()->count();
            if ($students == 0) {
                $attendanceRates[] = 0;
                continue;
            }

            $totalAttendance = Attendance::whereIn('user_profile_id', 
                $class->students()->pluck('id')
            )->count();

            $presentCount = Attendance::whereIn('user_profile_id', 
                $class->students()->pluck('id')
            )->where('status', 'present')->count();

            $rate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;
            $attendanceRates[] = $rate;
        }

        return [
            'labels' => $classNames,
            'datasets' => [
                [
                    'label' => 'Attendance Rate (%)',
                    'data' => $attendanceRates,
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#ef4444',
                        '#8b5cf6', '#06b6d4'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get result distribution
     */
    private function getResultDistribution($classes)
    {
        $excellent = 0;
        $good = 0;
        $average = 0;
        $below = 0;

        foreach ($classes as $class) {
            $results = Result::whereHas('student', fn($q) => 
                $q->where('school_class_id', $class->id)
            )->get();

            foreach ($results as $result) {
                $score = $result->total_score ?? 0;
                if ($score >= 80) $excellent++;
                elseif ($score >= 70) $good++;
                elseif ($score >= 60) $average++;
                else $below++;
            }
        }

        return [
            'labels' => ['Excellent (80+)', 'Good (70-79)', 'Average (60-69)', 'Below Avg (<60)'],
            'datasets' => [
                [
                    'data' => [$excellent, $good, $average, $below],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Get class enrollment chart
     */
    private function getClassEnrollment($classes)
    {
        $classNames = [];
        $enrollmentData = [];
        $capacityData = [];

        foreach ($classes as $class) {
            $classNames[] = $class->full_name;
            $enrollmentData[] = $class->students()->count();
            $capacityData[] = $class->capacity;
        }

        return [
            'labels' => $classNames,
            'datasets' => [
                [
                    'label' => 'Current Enrollment',
                    'data' => $enrollmentData,
                    'backgroundColor' => '#3b82f6'
                ],
                [
                    'label' => 'Capacity',
                    'data' => $capacityData,
                    'backgroundColor' => '#e5e7eb'
                ]
            ]
        ];
    }

    /**
     * Get upcoming lessons
     */
    private function getUpcomingLessons($user)
    {
        $upcoming = Timetable::where('teacher_id', $user->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return $upcoming->map(fn($lesson) => [
            'subject' => $lesson->subject?->name ?? 'N/A',
            'class' => $lesson->schoolClass?->full_name ?? 'N/A',
            'time' => $lesson->start_time->format('M d, H:i'),
            'day' => $lesson->start_time->format('l')
        ])->toArray();
    }

    /**
     * Get student progress
     */
    private function getStudentProgress($classes)
    {
        $studentProgress = [];

        foreach ($classes as $class) {
            $students = $class->students()->limit(5)->get();
            
            foreach ($students as $student) {
                $avgScore = Result::where('student_id', $student->user_id)->avg('total_score') ?? 0;
                
                $studentProgress[] = [
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'class' => $class->full_name,
                    'avgScore' => round($avgScore, 1),
                    'trend' => rand(0, 100) > 50 ? 'up' : 'down'
                ];
            }
        }

        return array_slice($studentProgress, 0, 8);
    }

    /**
     * Calculate average attendance rate
     */
    private function calculateAverageAttendance($classes)
    {
        $total = 0;
        $count = 0;

        foreach ($classes as $class) {
            $students = $class->students()->count();
            if ($students == 0) continue;

            $attendance = Attendance::whereIn('user_profile_id', 
                $class->students()->pluck('id')
            )->where('status', 'present')->count();

            $total += Attendance::whereIn('user_profile_id', 
                $class->students()->pluck('id')
            )->count();

            $count += $students;
        }

        return $total > 0 ? round(($attendance / $total) * 100, 1) : 0;
    }

    /**
     * Calculate average performance
     */
    private function calculateAveragePerformance($classes)
    {
        $totalScore = 0;
        $resultCount = 0;

        foreach ($classes as $class) {
            $results = Result::whereHas('student', fn($q) => 
                $q->where('school_class_id', $class->id)
            )->get();

            $resultCount += $results->count();
            $totalScore += $results->sum('total_score');
        }

        if ($resultCount == 0) return 'A';
        
        $avg = $totalScore / $resultCount;
        
        if ($avg >= 80) return 'A';
        elseif ($avg >= 70) return 'B';
        elseif ($avg >= 60) return 'C';
        elseif ($avg >= 50) return 'D';
        else return 'E';
    }

    /**
     * Get weekly lesson schedule
     */
    public function getWeeklyLessonSchedule($user)
    {
        $days = [];
        $lessonCounts = [];

        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $days[] = $date->format('D');
            
            $count = Timetable::where('teacher_id', $user->id)
                ->whereDate('start_time', $date->toDateString())
                ->count();
            
            $lessonCounts[] = $count;
        }

        return [
            'labels' => $days,
            'datasets' => [
                [
                    'label' => 'Lessons This Week',
                    'data' => $lessonCounts,
                    'backgroundColor' => '#8b5cf6',
                    'borderColor' => '#7c3aed',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Get student grade distribution for all classes
     */
    public function getStudentGradeDistribution($classes)
    {
        $aCount = 0;
        $bCount = 0;
        $cCount = 0;
        $dCount = 0;
        $eCount = 0;

        foreach ($classes as $class) {
            $results = Result::whereHas('student', fn($q) => 
                $q->where('school_class_id', $class->id)
            )->get();

            foreach ($results as $result) {
                $score = $result->total_score ?? 0;
                if ($score >= 80) $aCount++;
                elseif ($score >= 70) $bCount++;
                elseif ($score >= 60) $cCount++;
                elseif ($score >= 50) $dCount++;
                else $eCount++;
            }
        }

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
     * Get subject-wise performance
     */
    public function getSubjectWisePerformance($classes)
    {
        $classIds = $classes->pluck('id')->toArray();
        $subjects = [];
        $scores = [];

        // Get all unique subjects for the classes
        $classSubjects = Result::whereIn('school_class_id', $classIds)
            ->with('subject')
            ->distinct('subject_id')
            ->get()
            ->pluck('subject')
            ->unique('id');

        foreach ($classSubjects as $subject) {
            $subjects[] = $subject->name;
            
            $avgScore = Result::where('subject_id', $subject->id)
                ->whereIn('school_class_id', $classIds)
                ->avg('total_score') ?? 0;
            
            $scores[] = round($avgScore, 1);
        }

        return [
            'labels' => $subjects,
            'datasets' => [
                [
                    'label' => 'Average Score',
                    'data' => $scores,
                    'borderColor' => '#06b6d4',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get attendance trend for teacher's classes
     */
    public function getAttendanceTrend($classes)
    {
        $weeks = [];
        $attendanceRates = [];

        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            
            $weeks[] = $startDate->format('M d');
            
            $classIds = $classes->pluck('id')->toArray();
            $total = Attendance::whereIn('school_class_id', $classIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();
            
            $present = Attendance::whereIn('school_class_id', $classIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count();
            
            $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
            $attendanceRates[] = $rate;
        }

        return [
            'labels' => $weeks,
            'datasets' => [
                [
                    'label' => 'Weekly Attendance Rate',
                    'data' => $attendanceRates,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }
}
