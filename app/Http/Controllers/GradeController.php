<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\GradeScale;
use App\Models\ExamType;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Display gradebook / grade entry interface
     */
    public function index(Request $request)
    {
        $term = $request->get('term', 'First Term');
        $session = $request->get('session', '2024/2025');
        $examTypeId = $request->get('exam_type_id');
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');

        $examTypes = ExamType::active()->get();
        $classes = SchoolClass::active()->orderBy('name')->get();
        $subjects = Subject::active()->orderBy('name')->get();

        $students = null;
        $existingGrades = [];

        if ($classId && $subjectId && $examTypeId) {
            $class = SchoolClass::findOrFail($classId);
            $students = Student::where('class_level', $class->name)
                ->where('section', $class->section)
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            // Get existing grades
            $existingGrades = Grade::forTerm($term, $session)
                ->forClass($classId)
                ->forSubject($subjectId)
                ->forExamType($examTypeId)
                ->get()
                ->keyBy('student_id');
        }

        $gradeScales = GradeScale::ordered()->get();

        return view('grades.index', compact(
            'term',
            'session',
            'examTypes',
            'classes',
            'subjects',
            'students',
            'existingGrades',
            'gradeScales',
            'classId',
            'subjectId',
            'examTypeId'
        ));
    }

    /**
     * Store or update grades
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'term' => 'required|string',
            'session' => 'required|string',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:user_profiles,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
            'grades.*.remark' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['grades'] as $gradeData) {
                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'exam_type_id' => $validated['exam_type_id'],
                        'term' => $validated['term'],
                        'session' => $validated['session'],
                    ],
                    [
                        'school_class_id' => $validated['class_id'],
                        'score' => $gradeData['score'],
                        'remark' => $gradeData['remark'] ?? null,
                        'recorded_by' => null, // Would be auth()->user()->id in real app
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('grades.index', [
                    'term' => $validated['term'],
                    'session' => $validated['session'],
                    'class_id' => $validated['class_id'],
                    'subject_id' => $validated['subject_id'],
                    'exam_type_id' => $validated['exam_type_id'],
                ])
                ->with('success', 'Grades saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error saving grades: ' . $e->getMessage());
        }
    }

    /**
     * Display class results view
     */
    public function classResults(Request $request)
    {
        $term = $request->get('term', 'First Term');
        $session = $request->get('session', '2024/2025');
        $examTypeId = $request->get('exam_type_id');
        $classId = $request->get('class_id');

        $examTypes = ExamType::active()->get();
        $classes = SchoolClass::active()->orderBy('name')->get();

        $results = null;

        if ($classId && $examTypeId) {
            $class = SchoolClass::findOrFail($classId);

            // Get all students in the class
            $students = Student::where('class_level', $class->name)
                ->where('section', $class->section)
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            // Get grades for each student
            $results = $students->map(function ($student) use ($term, $session, $examTypeId) {
                $studentGrades = Grade::forTerm($term, $session)
                    ->forStudent($student->id)
                    ->forExamType($examTypeId)
                    ->with('subject')
                    ->get();

                $totalScore = $studentGrades->sum('score');
                $average = $studentGrades->count() > 0 ? $totalScore / $studentGrades->count() : 0;

                return [
                    'student' => $student,
                    'grades' => $studentGrades,
                    'total_score' => $totalScore,
                    'average' => round($average, 2),
                    'overall_grade' => Grade::calculateGrade($average),
                ];
            });

            // Sort by average (descending) and add position
            $results = $results->sortByDesc('average')->values();
            $results = $results->map(function ($result, $index) {
                $result['position'] = $index + 1;
                return $result;
            });
        }

        return view('grades.class-results', compact(
            'term',
            'session',
            'examTypes',
            'classes',
            'results',
            'classId',
            'examTypeId'
        ));
    }

    /**
     * Display student result profile
     */
    public function studentProfile(Student $student)
    {
        // Current term results
        $currentTerm = 'First Term';
        $currentSession = '2024/2025';

        $currentResults = Grade::forTerm($currentTerm, $currentSession)
            ->forStudent($student->id)
            ->with(['subject', 'examType'])
            ->get();

        $currentTotal = $currentResults->sum('score');
        $currentAverage = $currentResults->count() > 0 ? $currentTotal / $currentResults->count() : 0;
        $currentGrade = Grade::calculateGrade($currentAverage);

        // Historical results grouped by term
        $historicalResults = Grade::forStudent($student->id)
            ->where(function ($query) use ($currentTerm, $currentSession) {
                $query->where('term', '!=', $currentTerm)
                    ->orWhere('session', '!=', $currentSession);
            })
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy(function ($grade) {
                return $grade->session . ' - ' . $grade->term;
            });

        return view('grades.student-profile', compact(
            'student',
            'currentTerm',
            'currentSession',
            'currentResults',
            'currentTotal',
            'currentAverage',
            'currentGrade',
            'historicalResults'
        ));
    }

    /**
     * Grade scale management - list
     */
    public function gradeScales()
    {
        $gradeScales = GradeScale::ordered()->get();
        return view('grades.scales.index', compact('gradeScales'));
    }

    /**
     * Store grade scale
     */
    public function storeGradeScale(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'remark' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_passing' => 'boolean',
        ]);

        $validated['order'] = GradeScale::count();

        GradeScale::create($validated);

        return redirect()
            ->route('grades.scales')
            ->with('success', 'Grade scale created successfully!');
    }

    /**
     * Update grade scale
     */
    public function updateGradeScale(Request $request, GradeScale $gradeScale)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'remark' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_passing' => 'boolean',
        ]);

        $gradeScale->update($validated);

        return redirect()
            ->route('grades.scales')
            ->with('success', 'Grade scale updated successfully!');
    }

    /**
     * Delete grade scale
     */
    public function destroyGradeScale(GradeScale $gradeScale)
    {
        $gradeScale->delete();

        return redirect()
            ->route('grades.scales')
            ->with('success', 'Grade scale deleted successfully!');
    }

    /**
     * Exam types management - list
     */
    public function examTypes()
    {
        $examTypes = ExamType::all();
        return view('grades.exam-types.index', compact('examTypes'));
    }

    /**
     * Store exam type
     */
    public function storeExamType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:exam_types,code',
            'weight' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExamType::create($validated);

        return redirect()
            ->route('grades.exam-types')
            ->with('success', 'Exam type created successfully!');
    }

    /**
     * Update exam type
     */
    public function updateExamType(Request $request, ExamType $examType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:exam_types,code,' . $examType->id,
            'weight' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $examType->update($validated);

        return redirect()
            ->route('grades.exam-types')
            ->with('success', 'Exam type updated successfully!');
    }

    /**
     * Delete exam type
     */
    public function destroyExamType(ExamType $examType)
    {
        $examType->delete();

        return redirect()
            ->route('grades.exam-types')
            ->with('success', 'Exam type deleted successfully!');
    }
}
