<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\UserProfile;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherClassController extends Controller
{
    /**
     * Display all classes assigned to the teacher with quick action options
     */
    public function viewMyClasses()
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to view classes');
        }

        // Get all classes assigned to this teacher
        $classes = SchoolClass::where('teacher_id', $user->id)
            ->with(['students', 'teacher'])
            ->orderBy('name')
            ->get();

        return view('teacher.my-classes', compact('classes'));
    }

    /**
     * Display all students in the teacher's assigned class(es)
     * Teachers can only view students, not edit or delete
     */
    public function viewStudents(Request $request)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to view class students');
        }

        // Get the teacher's profile
        $teacher = $user->profile;
        
        // Get all classes assigned to this teacher via teacher_id
        $teacherClasses = SchoolClass::where('teacher_id', $user->id)->get();

        if ($teacherClasses->isEmpty()) {
            $students = collect([]);
            $stats = [
                'total' => 0,
                'active' => 0,
                'pending' => 0,
                'inactive' => 0,
                'male' => 0,
                'female' => 0,
            ];
            $selectedClass = null;
        } else {
            // If a specific class is selected, filter by that class
            $selectedClassId = $request->get('class');
            
            if ($selectedClassId) {
                $selectedClass = $teacherClasses->find($selectedClassId);
                
                if (!$selectedClass) {
                    abort(403, 'Unauthorized to view this class');
                }
            } else {
                // Default to first class if no selection
                $selectedClass = $teacherClasses->first();
            }

            // Get students in selected class
            $query = UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('school_class_id', $selectedClass->id);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%");
                });
            }

            // Apply gender filter
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Get students with pagination
            $perPage = $request->get('per_page', 15);
            $students = $query->with(['user', 'schoolClass'])
                ->latest('created_at')
                ->paginate($perPage);

            // Calculate statistics for selected class only
            $totalQuery = UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('school_class_id', $selectedClass->id);

            $stats = [
                'total' => $totalQuery->count(),
                'active' => (clone $totalQuery)->where('status', 'active')->count(),
                'pending' => (clone $totalQuery)->where('status', 'pending')->count(),
                'inactive' => (clone $totalQuery)->where('status', 'inactive')->count(),
                'male' => (clone $totalQuery)->where('gender', 'male')->count(),
                'female' => (clone $totalQuery)->where('gender', 'female')->count(),
            ];
        }

        return view('teacher.class-students', compact('students', 'teacherClasses', 'selectedClass', 'stats'));
    }

    /**
     * View a specific student's details (read-only for teachers)
     */
    public function viewStudent($studentId)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to view student');
        }

        $student = Student::with(['parent', 'schoolClass'])->findOrFail($studentId);

        // Verify student is in one of teacher's classes
        $isAuthorized = SchoolClass::where('teacher_id', $user->id)
            ->where('id', $student->school_class_id)
            ->exists();

        if (!$isAuthorized) {
            abort(403, 'Unauthorized to view this student');
        }

        // Get student's attendance
        $attendance = $student->attendances()
            ->latest()
            ->limit(10)
            ->get();

        return view('teacher.student-detail', compact('student', 'attendance'));
    }

    /**
     * Export class students to CSV (read-only export for teachers)
     */
    public function exportStudents(Request $request)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to export students');
        }

        $selectedClassId = $request->get('class');

        // Get the class and verify teacher has access
        $schoolClass = SchoolClass::where('teacher_id', $user->id)
            ->findOrFail($selectedClassId);

        // Get all students in this class
        $students = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        })->where('school_class_id', $schoolClass->id)
          ->with(['user', 'schoolClass'])
          ->get();

        // Generate CSV
        $filename = "class-{$schoolClass->class_code}-students-" . now()->format('Y-m-d-His') . ".csv";
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
        ];

        $handle = fopen("php://memory", 'r+');
        
        // Write CSV header
        fputcsv($handle, [
            'Admission Number',
            'Full Name',
            'Gender',
            'Date of Birth',
            'Admission Date',
            'Status',
            'Email',
            'Phone'
        ]);

        // Write student data
        foreach ($students as $student) {
            fputcsv($handle, [
                $student->admission_number,
                $student->full_name,
                ucfirst($student->gender),
                $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '',
                $student->admission_date ? $student->admission_date->format('Y-m-d') : '',
                ucfirst($student->status),
                $student->user->email ?? '',
                $student->phone ?? '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    /**
     * View class attendance with filters (read-only for teachers)
     */
    public function viewAttendance(Request $request)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to view attendance');
        }

        // Get teacher's classes
        $teacherClasses = SchoolClass::where('teacher_id', $user->id)->get();
        
        if ($teacherClasses->isEmpty()) {
            abort(403, 'You are not assigned to any class');
        }

        // Get selected class or use first class
        $selectedClass = null;
        if ($request->has('class')) {
            $selectedClass = SchoolClass::where('teacher_id', $user->id)
                ->where('id', $request->class)
                ->first();
        }
        
        if (!$selectedClass && $teacherClasses->count() > 0) {
            $selectedClass = $teacherClasses->first();
        }

        // Start query for attendance records
        $query = Attendance::query();
        
        if ($selectedClass) {
            $query->where('school_class_id', $selectedClass->id);
        }

        // Apply filters
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Get attendance with pagination
        $attendance = $query->with(['student', 'recorder'])
            ->orderBy('date', 'desc')
            ->paginate(25);

        // Calculate stats for selected class
        $stats = [];
        if ($selectedClass) {
            $totalQuery = Attendance::where('school_class_id', $selectedClass->id);
            $stats = [
                'total' => (clone $totalQuery)->count(),
                'present' => (clone $totalQuery)->where('status', 'present')->count(),
                'absent' => (clone $totalQuery)->where('status', 'absent')->count(),
                'late' => (clone $totalQuery)->where('status', 'late')->count(),
                'excused' => (clone $totalQuery)->where('status', 'excused')->count(),
            ];
        }

        return view('teacher.class-attendance', compact('attendance', 'teacherClasses', 'selectedClass', 'stats'));
    }

    /**
     * Show form to mark attendance for a class (teachers only)
     */
    public function markAttendance(Request $request)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to mark attendance');
        }

        // Get teacher's classes
        $teacherClasses = SchoolClass::where('teacher_id', $user->id)->get();
        
        if ($teacherClasses->isEmpty()) {
            abort(403, 'You are not assigned to any class');
        }

        // Get selected class or use first class
        $selectedClass = null;
        if ($request->has('class')) {
            $selectedClass = SchoolClass::where('teacher_id', $user->id)
                ->where('id', $request->class)
                ->first();
        }
        
        if (!$selectedClass && $teacherClasses->count() > 0) {
            $selectedClass = $teacherClasses->first();
        }

        // Check if today is a school operating day
        $schoolSettings = SchoolSetting::first();
        $todayName = now()->format('l'); // e.g., "Monday"
        $isOperatingDay = $schoolSettings && $schoolSettings->isOperatingDay($todayName);

        // Get selected date (default to today)
        $selectedDate = $request->has('date') ? $request->date : now()->format('Y-m-d');
        $selectedDateObj = \Carbon\Carbon::parse($selectedDate);
        $selectedDateName = $selectedDateObj->format('l');
        $isSelectedDateOperating = $schoolSettings && $schoolSettings->isOperatingDay($selectedDateName);

        // Get students in the selected class
        $students = [];
        $existingAttendance = [];
        
        if ($selectedClass) {
            $students = Student::where('school_class_id', $selectedClass->id)
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            // Get existing attendance records for this date
            $existingAttendance = Attendance::where('school_class_id', $selectedClass->id)
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('user_profile_id');
        }

        return view('teacher.mark-attendance', compact(
            'teacherClasses',
            'selectedClass',
            'students',
            'selectedDate',
            'isSelectedDateOperating',
            'existingAttendance'
        ));
    }

    /**
     * Store attendance records (teachers only)
     */
    public function storeAttendance(Request $request)
    {
        $user = auth()->user();
        
        // Only teachers can access this feature
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized to mark attendance');
        }

        // Validate input
        $validated = $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:user_profiles,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:255',
        ]);

        // Verify teacher owns this class
        $class = SchoolClass::where('teacher_id', $user->id)
            ->where('id', $validated['class_id'])
            ->firstOrFail();

        // Check if selected date is an operating day
        $schoolSettings = SchoolSetting::first();
        $selectedDateObj = \Carbon\Carbon::parse($validated['date']);
        $selectedDateName = $selectedDateObj->format('l');
        
        if (!$schoolSettings || !$schoolSettings->isOperatingDay($selectedDateName)) {
            return redirect()->back()->with('error', 'Attendance can only be marked for school operating days.');
        }

        try {
            // Delete existing attendance for this date and class
            Attendance::where('school_class_id', $class->id)
                ->whereDate('date', $validated['date'])
                ->delete();

            // Create new attendance records
            foreach ($validated['attendance'] as $record) {
                Attendance::create([
                    'user_profile_id' => $record['student_id'],
                    'school_class_id' => $class->id,
                    'date' => $validated['date'],
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'recorded_by' => $user->id,
                ]);
            }

            return redirect()->route('teacher.class.students')
                ->with('success', 'Attendance marked successfully for ' . count($validated['attendance']) . ' students.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error marking attendance: ' . $e->getMessage());
        }
    }

    /**
     * View timetable for teacher's assigned classes
     */
    public function viewClassesTimetable(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        // Get school operating days
        $schoolSetting = SchoolSetting::first();
        $schoolDays = $schoolSetting ? $schoolSetting->school_days : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Get all classes assigned to this teacher
        $query = SchoolClass::where('teacher_id', $user->profile->id)
            ->with([
                'timetables' => function($q) {
                    $q->orderBy('day_of_week')->orderBy('period_number');
                },
                'timetables.subject',
                'subjects' // Load all subjects assigned to the class
            ]);

        // If a specific class is requested, filter to that class only
        if ($request->has('class')) {
            $query->where('id', $request->get('class'));
        }

        $classes = $query->get();

        return view('teacher.classes-timetable', compact('classes', 'schoolDays'));
    }

    /**
     * View teacher's personal timetable (all periods across all classes)
     */
    public function viewPersonalTimetable()
    {
        $user = auth()->user();
        
        if (!$user->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        // Get all timetable entries where this teacher is assigned
        $timetables = \App\Models\Timetable::where('teacher_id', $user->profile->id)
            ->where('type', 'class')
            ->with(['schoolClass', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('teacher.my-timetable', compact('timetables'));
    }
}



