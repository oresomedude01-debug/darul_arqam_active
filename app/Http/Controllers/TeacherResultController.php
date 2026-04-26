<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Result;
use App\Models\AcademicTerm;
use App\Models\Subject;
use Carbon\Carbon;

class TeacherResultController extends Controller
{
    /**
     * Display all classes assigned to the teacher
     */
    public function viewMyClasses(Request $request)
    {
        if (!auth()->user()->hasPermission('view-class-results')) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();

        // Get all classes assigned to this teacher (use user_id, not profile_id)
        $classes = SchoolClass::where('teacher_id', $user->id)
            ->with('students', 'subjects')
            ->orderBy('name')
            ->get();

        // Get active and recent terms
        $activeTerms = AcademicTerm::where('is_active', true)->get();
        $allTerms = AcademicTerm::orderBy('start_date', 'desc')->limit(5)->get();

        return view('teacher.results.my-classes', compact('classes', 'activeTerms', 'allTerms'));
    }

    /**
     * Display results grid for a specific class and term
     */
    public function viewClassResults(Request $request)
    {
        if (!auth()->user()->hasPermission('view-class-results')) {
            abort(403, 'Unauthorized');
        }

        $classId = $request->query('class');
        $termId = $request->query('term');

        if (!$classId || !$termId) {
            return back()->with('error', 'Please select a class and term');
        }

        $user = auth()->user();

        // Verify teacher owns this class
        $class = SchoolClass::where('id', $classId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $term = AcademicTerm::findOrFail($termId);

        // Get students in the class
        $students = Student::whereHas('schoolClass', function ($q) use ($classId) {
            $q->where('school_classes.id', $classId);
        })->orderBy('first_name')->get();

        // Get subjects taught in this class
        $subjects = $class->subjects()->orderBy('name')->get();

        // Get existing results for this class and term
        $results = Result::where('school_class_id', $classId)
            ->where('academic_term_id', $termId)
            ->get()
            ->groupBy('student_id');

        // Check if term is active (editable)
        $isActiveTerm = $term->is_active;

        // Get all terms for filtering
        $allTerms = AcademicTerm::orderBy('start_date', 'desc')->get();

        return view('teacher.results.class-results', compact(
            'class',
            'term',
            'students',
            'subjects',
            'results',
            'isActiveTerm',
            'allTerms'
        ));
    }

    /**
     * Show edit form for a result
     */
    public function editResult($id)
    {
        if (!auth()->user()->hasPermission('edit-class-results')) {
            abort(403, 'Unauthorized');
        }

        $result = Result::findOrFail($id);

        // Verify teacher owns this result's class
        $user = auth()->user();

        $class = SchoolClass::where('id', $result->school_class_id)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        // Verify term is active
        $term = $result->academicTerm;
        if (!$term->is_active) {
            return back()->with('error', 'Cannot edit results for inactive terms');
        }

        return view('teacher.results.edit-result', compact('result', 'class', 'term'));
    }

    /**
     * Update result scores and data
     */
    public function updateResult(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('edit-class-results')) {
            abort(403, 'Unauthorized');
        }

        $result = Result::findOrFail($id);

        // Verify teacher owns this result's class
        $user = auth()->user();

        SchoolClass::where('id', $result->school_class_id)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        // Verify term is active
        $term = $result->academicTerm;
        if (!$term->is_active) {
            return back()->with('error', 'Cannot edit results for inactive terms');
        }

        $validated = $request->validate([
            'ca_score' => 'required|numeric|min:0|max:100',
            'exam_score' => 'required|numeric|min:0|max:100',
            'teacher_comment' => 'nullable|string|max:500',
            'behaviour_rating' => 'nullable|integer|min:1|max:5',
            'psychomotor_rating' => 'nullable|integer|min:1|max:5',
            'affective_rating' => 'nullable|integer|min:1|max:5',
            'behaviour_punctuality' => 'nullable|integer|min:1|max:5',
            'behaviour_participation' => 'nullable|integer|min:1|max:5',
            'behaviour_respect' => 'nullable|integer|min:1|max:5',
            'psychomotor_handwriting' => 'nullable|integer|min:1|max:5',
            'psychomotor_creativity' => 'nullable|integer|min:1|max:5',
            'psychomotor_sports' => 'nullable|integer|min:1|max:5',
            'affective_perseverance' => 'nullable|integer|min:1|max:5',
            'affective_control' => 'nullable|integer|min:1|max:5',
            'affective_initiative' => 'nullable|integer|min:1|max:5',
        ]);

        // Calculate total score
        $validated['total_score'] = $validated['ca_score'] + $validated['exam_score'];
        $validated['teacher_id'] = $user->id;
        $validated['submitted_at'] = now();

        $result->update($validated);

        return back()->with('success', 'Result updated successfully');
    }

    /**
     * Display batch edit view for class results
     */
    public function batchEditResults(Request $request)
    {
        if (!auth()->user()->hasPermission('edit-class-results')) {
            abort(403, 'Unauthorized');
        }

        $classId = $request->query('class');
        $termId = $request->query('term');

        if (!$classId || !$termId) {
            return back()->with('error', 'Please select a class and term');
        }

        $user = auth()->user();

        // Verify teacher owns this class
        $class = SchoolClass::where('id', $classId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $term = AcademicTerm::findOrFail($termId);

        // Verify term is active
        if (!$term->is_active) {
            return back()->with('error', 'Cannot edit results for inactive terms');
        }

        // Get students and results
        $students = Student::whereHas('schoolClass', function ($q) use ($classId) {
            $q->where('school_classes.id', $classId);
        })->orderBy('first_name')->get();

        $subjects = $class->subjects()->orderBy('name')->get();

        $results = Result::where('school_class_id', $classId)
            ->where('academic_term_id', $termId)
            ->get()
            ->groupBy('student_id');

        return view('teacher.results.batch-edit-results', compact(
            'class',
            'term',
            'students',
            'subjects',
            'results'
        ));
    }

    /**
     * Update batch results from form submission
     */
    public function storeBatchResults(Request $request)
    {
        if (!auth()->user()->hasPermission('edit-class-results')) {
            abort(403, 'Unauthorized');
        }

        $classId = $request->input('class_id');
        $termId = $request->input('term_id');

        $user = auth()->user();

        // Verify teacher owns this class
        $class = SchoolClass::where('id', $classId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $term = AcademicTerm::findOrFail($termId);

        // Verify term is active
        if (!$term->is_active) {
            return back()->with('error', 'Cannot edit results for inactive terms');
        }

        // Update each result
        $results = $request->input('results', []);
        $updated = 0;
        $created = 0;

        foreach ($results as $resultId => $data) {
            // Skip if no scores provided
            if (empty($data['ca_score']) && empty($data['exam_score'])) {
                continue;
            }

            $caScore = floatval($data['ca_score'] ?? 0);
            $examScore = floatval($data['exam_score'] ?? 0);

            // Check if this is a new result (key starts with 'new_')
            if (strpos($resultId, 'new_') === 0) {
                // Extract student_id and subject_id from key (new_studentId_subjectId)
                $parts = explode('_', $resultId);
                if (count($parts) === 3) {
                    $studentId = intval($parts[1]);
                    $subjectId = intval($parts[2]);

                    // Create new result with academic_session_id
                    Result::create([
                        'school_class_id' => $classId,
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'academic_session_id' => $term->academic_session_id,
                        'academic_term_id' => $termId,
                        'ca_score' => $caScore,
                        'exam_score' => $examScore,
                        'total_score' => $caScore + $examScore,
                        'teacher_id' => $user->id,
                        'submitted_at' => now(),
                    ]);
                    $created++;
                }
            } else {
                // Update existing result
                $result = Result::find($resultId);
                if ($result && $result->school_class_id === $classId) {
                    $result->update([
                        'ca_score' => $caScore,
                        'exam_score' => $examScore,
                        'total_score' => $caScore + $examScore,
                        'teacher_comment' => $data['teacher_comment'] ?? $result->teacher_comment,
                        'teacher_id' => $user->id,
                        'submitted_at' => now(),
                    ]);
                    $updated++;
                }
            }
        }

        $message = "Results saved! Created: {$created}, Updated: {$updated}";
        return back()->with('success', $message);
    }

    /**
     * Release results for a class and term
     */
    public function releaseResults(Request $request)
    {
        if (!auth()->user()->hasPermission('release-class-results')) {
            abort(403, 'Unauthorized');
        }

        $classId = $request->input('class_id');
        $termId = $request->input('term_id');

        $user = auth()->user();

        // Verify teacher owns this class
        $class = SchoolClass::where('id', $classId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $term = AcademicTerm::findOrFail($termId);

        // Update all submitted results to released
        $updated = Result::where('school_class_id', $classId)
            ->where('academic_term_id', $termId)
            ->where('submitted_at', '!=', null)
            ->update([
                'is_released' => true,
                'released_at' => now(),
            ]);

        return back()->with('success', "Released {$updated} results successfully");
    }

    /**
     * View previous released results (read-only)
     */
    public function viewPreviousResults(Request $request)
    {
        if (!auth()->user()->hasPermission('view-class-results')) {
            abort(403, 'Unauthorized');
        }

        $classId = $request->query('class');
        $termId = $request->query('term');

        if (!$classId || !$termId) {
            return back()->with('error', 'Please select a class and term');
        }

        $user = auth()->user();

        // Verify teacher owns this class
        $class = SchoolClass::where('id', $classId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $term = AcademicTerm::findOrFail($termId);

        // Get students and results (read-only view)
        $students = Student::whereHas('schoolClass', function ($q) use ($classId) {
            $q->where('school_classes.id', $classId);
        })->orderBy('first_name')->get();

        $subjects = $class->subjects()->orderBy('name')->get();

        $results = Result::where('school_class_id', $classId)
            ->where('academic_term_id', $termId)
            ->where('is_released', true)
            ->get()
            ->groupBy('student_id');

        $allTerms = AcademicTerm::orderBy('start_date', 'desc')->get();

        return view('teacher.results.view-previous-results', compact(
            'class',
            'term',
            'students',
            'subjects',
            'results',
            'allTerms'
        ));
    }
}
