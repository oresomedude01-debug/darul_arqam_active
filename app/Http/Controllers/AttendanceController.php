<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\UserProfile;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard with stats and filters
     */
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $classId = $request->get('class_id');

        // Get stats for the selected date
        $query = Attendance::forDate($date);

        if ($classId) {
            $query->forClass($classId);
        }

        $stats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];

        if ($date) {
            $stats = [
                'total' => UserProfile::whereNotNull('school_class_id')->count(),
                'present' => (clone $query)->byStatus('present')->count(),
                'absent' => (clone $query)->byStatus('absent')->count(),
                'late' => (clone $query)->byStatus('late')->count(),
                'excused' => (clone $query)->byStatus('excused')->count(),
            ];
        }

        // Get classes with missing attendance for today
        $classesWithoutAttendance = SchoolClass::active()
            ->whereDoesntHave('students.attendances', function($q) use ($date) {
                $q->forDate($date);
            })
            ->count();

        // Recent attendance records
        $recentRecords = Attendance::with(['student', 'schoolClass'])
            ->latest('date')
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Get all classes for filter
        $classes = SchoolClass::active()->orderBy('name')->get();

        return view('attendance.index', compact('stats', 'classesWithoutAttendance', 'recentRecords', 'classes', 'date', 'classId'));
    }

    /**
     * Show take attendance form
     */
    public function create(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $classId = $request->get('class_id');

        $classes = SchoolClass::active()->orderBy('name')->get();

        $students = null;
        $existingAttendance = [];

        if ($classId) {
            $class = SchoolClass::findOrFail($classId);
            $students = $class->students()->orderBy('first_name')->get();

            // Get existing attendance for this date and class
            $existingAttendance = Attendance::forDate($date)
                ->forClass($classId)
                ->get()
                ->keyBy('user_profile_id');
        }

        return view('attendance.create', compact('classes', 'students', 'existingAttendance', 'date', 'classId'));
    }

    /**
     * Store attendance records
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:school_classes,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:user_profiles,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['attendance'] as $record) {
                Attendance::updateOrCreate(
                    [
                        'user_profile_id' => $record['student_id'],
                        'date' => $validated['date'],
                        'school_class_id' => $validated['class_id'],
                    ],
                    [
                        'status' => $record['status'],
                        'notes' => $record['notes'] ?? null,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('attendance.index', ['date' => $validated['date'], 'class_id' => $validated['class_id']])
                ->with('success', 'Attendance recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error recording attendance: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance records/history
     */
    public function records(Request $request)
    {
        $query = Attendance::with(['student', 'schoolClass']);

        // Filters
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('class_id')) {
            $query->forClass($request->class_id);
        }

        if ($request->filled('student_id')) {
            $query->forStudent($request->student_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $attendances = $query->latest('date')->latest('created_at')->paginate(20)->withQueryString();

        // Get filters data
        $classes = SchoolClass::active()->orderBy('name')->get();
        $students = UserProfile::whereNotNull('school_class_id')->orderBy('first_name')->get();

        return view('attendance.records', compact('attendances', 'classes', 'students'));
    }

    /**
     * Show student attendance profile
     */
    public function studentProfile(UserProfile $student)
    {
        $student->load(['attendances' => function($query) {
            $query->latest('date')->limit(30);
        }]);

        // Calculate stats
        $totalRecords = $student->attendances()->count();
        $presentCount = $student->attendances()->byStatus('present')->count();
        $absentCount = $student->attendances()->byStatus('absent')->count();
        $lateCount = $student->attendances()->byStatus('late')->count();
        $excusedCount = $student->attendances()->byStatus('excused')->count();

        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;

        // Recent attendance (last 30 days)
        $recentAttendance = $student->attendances()
            ->with(['schoolClass'])
            ->where('date', '>=', now()->subDays(30))
            ->latest('date')
            ->get();

        // Monthly breakdown (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');

            $monthlyData[$monthName] = [
                'present' => $student->attendances()
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->byStatus('present')
                    ->count(),
                'absent' => $student->attendances()
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->byStatus('absent')
                    ->count(),
            ];
        }

        return view('attendance.student-profile', compact(
            'student',
            'totalRecords',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'attendanceRate',
            'recentAttendance',
            'monthlyData'
        ));
    }

    /**
     * Mark all students as present (AJAX)
     */
    public function markAllPresent(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $class = SchoolClass::findOrFail($validated['class_id']);
        $students = $class->students()->get();

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                Attendance::updateOrCreate(
                    [
                        'user_profile_id' => $student->id,
                        'date' => $validated['date'],
                        'school_class_id' => $validated['class_id'],
                    ],
                    [
                        'status' => 'present',
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'All students marked as present']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
