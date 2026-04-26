<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(Request $request)
    {
        $query = SchoolClass::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->byTeacher($request->teacher_id);
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        // Validate sort field
        $allowedSorts = ['name', 'class_code', 'capacity', 'status', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $classes = $query->paginate(15)->withQueryString();

        // Calculate stats - count enrolled students for each class
        $allClasses = SchoolClass::all();
        $fullCount = 0;
        foreach ($allClasses as $class) {
            $studentCount = $class->students()->count();
            if ($studentCount >= $class->capacity) {
                $fullCount++;
            }
        }

        $stats = [
            'total' => SchoolClass::count(),
            'active' => SchoolClass::where('status', 'active')->count(),
            'inactive' => SchoolClass::where('status', 'inactive')->count(),
            'full' => $fullCount,
        ];

        // Get all teachers for filter
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->where('status', 'active')->orderBy('first_name')->get();

        return view('classes.index', compact('classes', 'stats', 'teachers'));
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->where('status', 'active')->orderBy('first_name')->get();

        return view('classes.create', compact('teachers'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'class_code' => 'required|string|max:255|unique:school_classes,class_code',
            'teacher_id' => 'nullable|exists:user_profiles,user_id',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
            'description' => 'nullable|string',
        ]);

        $class = SchoolClass::create($validated);

        return redirect()
            ->route('classes.show', $class)
            ->with('success', 'Class created successfully!');
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $class)
    {
        $class->load([
            'students',
            'timetables' => function($query) {
                $query->orderBy('day_of_week')
                      ->orderBy('start_time');
            },
            'timetables.subject',
            'timetables.teacher'
        ]);

        // Get all students who are not yet enrolled in any class
        $allStudents = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        })
        ->where('school_class_id', null)
        ->where('status', 'active')
        ->orderBy('first_name')
        ->get();

        // Get school operating days from school_days column (array type)
        $schoolSettings = \App\Models\SchoolSetting::first();
        if ($schoolSettings && !empty($schoolSettings->school_days)) {
            $days = array_map(function($day) { 
                return strtolower($day); 
            }, $schoolSettings->school_days);
        } else {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        }

        return view('classes.show', compact('class', 'allStudents', 'days'));
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(SchoolClass $class)
    {
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->where('status', 'active')->orderBy('first_name')->get();

        return view('classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'class_code' => ['required', 'string', 'max:255', Rule::unique('school_classes', 'class_code')->ignore($class->id)],
            'teacher_id' => 'nullable|exists:user_profiles,user_id',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
            'description' => 'nullable|string',
        ]);

        // Update the class
        $class->update($validated);

        return redirect()
            ->route('classes.show', $class)
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }

    /**
     * Enroll students in a class
     */
    public function enrollStudents(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:user_profiles,id',
        ]);

        $enrolled = 0;
        foreach ($validated['student_ids'] as $studentId) {
            $student = UserProfile::find($studentId);
            if ($student && !$student->school_class_id) {
                $student->update(['school_class_id' => $class->id]);
                $enrolled++;
            }
        }

        return redirect()
            ->route('classes.show', $class)
            ->with('success', "$enrolled student(s) enrolled successfully!");
    }

    /**
     * Move students to another class
     */
    public function moveStudents(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:user_profiles,id',
            'target_class_id' => 'required|exists:school_classes,id',
        ]);

        $targetClass = SchoolClass::find($validated['target_class_id']);
        
        if (!$targetClass) {
            return redirect()
                ->route('classes.show', $class)
                ->with('error', 'Target class not found!');
        }

        $moved = 0;
        foreach ($validated['student_ids'] as $studentId) {
            $student = UserProfile::find($studentId);
            if ($student && $student->school_class_id == $class->id) {
                $student->update(['school_class_id' => $targetClass->id]);
                $moved++;
            }
        }

        return redirect()
            ->route('classes.show', $class)
            ->with('success', "$moved student(s) moved to {$targetClass->full_name} successfully!");
    }

    /**
     * Export classes to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = SchoolClass::with('classTeacher');

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('academic_year')) {
            $query->byAcademicYear($request->academic_year);
        }

        $classes = $query->orderBy('name')->get();

        $filename = 'classes_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($classes) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Class Code',
                'Class Name',
                'Section',
                'Class Teacher',
                'Capacity',
                'Current Enrollment',
                'Available Seats',
                'Room Number',
                'Academic Year',
                'Status',
            ]);

            // Add data rows
            foreach ($classes as $class) {
                fputcsv($file, [
                    $class->class_code,
                    $class->name,
                    $class->section ?? '-',
                    $class->classTeacher ? $class->classTeacher->full_name : '-',
                    $class->capacity,
                    $class->current_enrollment,
                    $class->available_seats,
                    $class->room_number ?? '-',
                    $class->academic_year ?? '-',
                    ucfirst($class->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
