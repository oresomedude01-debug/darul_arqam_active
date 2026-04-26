<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\GradeScale;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    /**
     * Dashboard - Overview of all result sessions
     */
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        $termStats = $this->getTermStats();

        return view('results.index', compact('sessions', 'termStats'));
    }

    /**
     * Session management - Select term to manage
     */
    public function showSession(AcademicSession $session)
    {
        $terms = AcademicTerm::where('academic_session_id', $session->id)
            ->orderBy('term')
            ->get()
            ->map(function ($term) use ($session) {
                $resultCount = Result::where('academic_session_id', $session->id)
                    ->where('academic_term_id', $term->id)
                    ->count();

                return [
                    'term' => $term,
                    'result_count' => $resultCount,
                    'status' => $resultCount > 0 ? 'incomplete' : 'pending',
                ];
            });

        return view('results.session-show', compact('session', 'terms'));
    }

    /**
     * Term management - Select class to manage results
     */
    public function manageTerm(AcademicSession $session, AcademicTerm $term)
    {
        $classes = SchoolClass::active()
            ->orderBy('name')
            ->get()
            ->map(function ($class) use ($session, $term) {
                $studentCount = Student::where('school_class_id', $class->id)
                    ->where('status', 'active')
                    ->count();

                // Get the number of subjects taught in this class
                $subjectCount = $class->subjects()->count();

                // Expected results = students × subjects
                $expectedResults = $studentCount * $subjectCount;

                // Count actual results entered
                $resultCount = Result::where('school_class_id', $class->id)
                    ->where('academic_session_id', $session->id)
                    ->where('academic_term_id', $term->id)
                    ->count();

                $class->student_count = $studentCount;
                $class->result_count = $resultCount;
                $class->completion_percentage = $expectedResults > 0
                    ? round(($resultCount / $expectedResults) * 100)
                    : 0;

                return $class;
            });

        return view('results.term-manage', compact('session', 'term', 'classes'));
    }

    private function gradeFromSettings($score, $boundaries)
    {
        // Sort boundaries from highest → lowest
        arsort($boundaries);

        foreach ($boundaries as $grade => $minScore) {
            if ($score >= (int) $minScore) {
                return $grade;
            }
        }

        return 'N/A';
    }


    /**
     * Class Results - Enter and manage scores for a class (with cumulative scoring)
     */
public function manageClass(AcademicSession $session, AcademicTerm $term, SchoolClass $class, Request $request)
{
    // Authorization: Check if user is admin OR teacher of this class
    $user = auth()->user();
    $isAdmin = $user->hasRole('Administrator');
    $isTeacher = $user->hasRole('teacher');
    
    // If not admin and not teacher, deny access
    if (!$isAdmin && !$isTeacher) {
        abort(403, 'Unauthorized access');
    }
    
    // If teacher (and not admin), apply teacher restrictions
    if ($isTeacher && !$isAdmin) {
        // Teachers can only access their assigned classes
        // Verify using school_classes.teacher_id foreign key
        $isTeacherOfClass = SchoolClass::where('id', $class->id)
            ->where('teacher_id', $user->id)
            ->exists();
        
        if (!$isTeacherOfClass) {
            abort(403, 'Unauthorized: You can only edit results for your assigned classes');
        }
        
        // Teachers can only edit active terms
        if (!$term->is_active) {
            abort(403, 'Unauthorized: You can only edit results for active terms');
        }
    }
    // Admins skip all teacher restrictions

    // Get subjects for this class
    $subjects = $class->subjects()->active()->orderBy('name')->get();
    if ($subjects->isEmpty()) {
        return redirect()->back()->with('error', 'No subjects assigned to this class.');
    }

    // Get students in class
    $students = Student::where('school_class_id', $class->id)
        ->where('status', 'active')
        ->orderBy('first_name', 'asc')
        ->get();

    if ($students->isEmpty()) {
        return redirect()->back()->with('error', 'No students in this class.');
    }

    // Get all results
    $allResults = Result::where('school_class_id', $class->id)
        ->where('academic_session_id', $session->id)
        ->where('academic_term_id', $term->id)
        ->get();

    // Load settings
    $settings = SchoolSetting::getInstance();
    $additionalSettings = $settings->additional_settings ?? [];
    if (is_string($additionalSettings)) {
        $additionalSettings = json_decode($additionalSettings, true) ?? [];
    }
    $gradeBoundaries = $additionalSettings['grade_boundaries'] ?? [];

    // Compute grades - show only current term for input
    $resultsWithGrades = $allResults->map(function ($result) use ($gradeBoundaries) {

        $caScore = $result->ca_score ?? 0;
        $examScore = $result->exam_score ?? 0;

        // Current term score (simple addition, no cumulative here)
        $currentScore = ($caScore) + ($examScore);

        // Compute grade using current term score only
        $grade = $this->gradeFromSettings($currentScore, $gradeBoundaries);

        // Attach computed values
        $result->computed_grade = $grade;
        $result->computed_current_score = $currentScore;
        $result->computed_cumulative_score = $currentScore; // Same as current for entry page

        return $result;

    })->groupBy(function ($result) {
        return $result->student_id . '_' . $result->subject_id;
    });

    return view('results.class-manage', compact(
        'session',
        'term',
        'class',
        'subjects',
        'students',
        'resultsWithGrades',
        'gradeBoundaries'
    ));
}



    /**
     * Store/Update results for all students in a class
     */
    public function storeClassResults(Request $request)
    {
        // Authorization check
        $user = auth()->user();
        $classId = $request->input('school_class_id');
        $termId = $request->input('academic_term_id');
        
        $isAdmin = $user->hasRole('Administrator');
        $isTeacher = $user->hasRole('teacher');
        
        // Admin can do anything
        if (!$isAdmin && !$isTeacher) {
            abort(403, 'Unauthorized to update results');
        }
        
        if ($isTeacher && !$isAdmin) {
            // Teachers can only update their assigned classes
            // Verify using school_classes.teacher_id foreign key
            $isTeacherOfClass = SchoolClass::where('id', $classId)
                ->where('teacher_id', $user->id)
                ->exists();
            
            if (!$isTeacherOfClass) {
                return back()->withErrors(['results' => 'Unauthorized: You can only edit results for your assigned classes']);
            }
            
            // Teachers can only edit active terms
            $term = AcademicTerm::find($termId);
            if (!$term || !$term->is_active) {
                return back()->withErrors(['results' => 'Unauthorized: You can only edit results for active terms']);
            }
        }


        // Transform nested array data to validate properly
        $resultsArray = $request->input('results', []);
        
        // Debug: log what we received
        \Log::debug('Results data received:', [
            'data' => $resultsArray, 
            'count' => count($resultsArray),
            'all_request' => $request->all()
        ]);
        
        // If no results provided, return error
        if (empty($resultsArray)) {
            \Log::warning('No results provided in request');
            return back()->withErrors(['results' => 'No results provided']);
        }
        
        $transformedResults = [];
        
        foreach ($resultsArray as $key => $data) {
            if (is_array($data) && isset($data['student_id']) && isset($data['subject_id'])) {
                $transformedResults[] = $data;
            }
        }
        
        \Log::debug('Transformed results:', [
            'count' => count($transformedResults), 
            'data' => $transformedResults,
            'sample_keys' => array_slice(array_keys($resultsArray), 0, 5)
        ]);
        
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
            'school_class_id' => 'required|exists:school_classes,id',
        ]);
        
        // Validate each result entry individually
        foreach ($transformedResults as $resultData) {
            if (!isset($resultData['student_id']) || !isset($resultData['subject_id'])) {
                return back()->withErrors(['results' => 'Invalid result data structure']);
            }
            
            // Validate student exists in user_profiles
            if (!Student::where('id', $resultData['student_id'])->exists()) {
                return back()->withErrors(['results' => 'Invalid student ID: ' . $resultData['student_id']]);
            }
            
            // Validate subject exists
            if (!Subject::where('id', $resultData['subject_id'])->exists()) {
                return back()->withErrors(['results' => 'Invalid subject ID: ' . $resultData['subject_id']]);
            }
        }

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $updatedCount = 0;

            foreach ($transformedResults as $resultData) {
                // Skip if no scores provided
                $caScore = floatval($resultData['ca_score'] ?? 0);
                $examScore = floatval($resultData['exam_score'] ?? 0);
                
                \Log::debug('Processing result', [
                    'student_id' => $resultData['student_id'],
                    'subject_id' => $resultData['subject_id'],
                    'ca_score' => $caScore,
                    'exam_score' => $examScore,
                ]);
                
                if ($caScore === 0.0 && $examScore === 0.0) {
                    \Log::debug('Skipping result with no scores');
                    continue;
                }

                $existingResult = Result::where('student_id', $resultData['student_id'])
                    ->where('subject_id', $resultData['subject_id'])
                    ->where('school_class_id', $request->school_class_id)
                    ->where('academic_session_id', $request->academic_session_id)
                    ->where('academic_term_id', $request->academic_term_id)
                    ->first();

                $scoreData = [
                    'ca_score' => $caScore,
                    'exam_score' => $examScore,
                ];

                if ($existingResult) {
                    $existingResult->ca_score = $caScore;
                    $existingResult->exam_score = $examScore;
                    $existingResult->total_score = $existingResult->calculateTotalScore();
                    $existingResult->grade = $existingResult->calculateGrade();
                    $existingResult->remark = $existingResult->calculateRemark();
                    $existingResult->save();
                    \Log::debug('Result updated', ['result_id' => $existingResult->id]);
                    $updatedCount++;
                } else {
                    \Log::debug('Creating new result');
                    $newResult = new Result([
                        'student_id' => $resultData['student_id'],
                        'subject_id' => $resultData['subject_id'],
                        'school_class_id' => $request->school_class_id,
                        'academic_session_id' => $request->academic_session_id,
                        'academic_term_id' => $request->academic_term_id,
                        'ca_score' => $caScore,
                        'exam_score' => $examScore,
                    ]);
                    // Calculate total score and grade before saving
                    $newResult->total_score = $newResult->calculateTotalScore();
                    $newResult->grade = $newResult->calculateGrade();
                    $newResult->remark = $newResult->calculateRemark();
                    $newResult->save();
                    \Log::debug('Result created', ['result_id' => $newResult->id]);
                    $createdCount++;
                }
            }

            DB::commit();

            $message = "Results saved! Created: {$createdCount}, Updated: {$updatedCount}";
            \Log::info($message);
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving results: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['results' => 'Error saving results: ' . $e->getMessage()]);
        }
    }

    /**
     * View results for a specific subject in a class
     */
    public function viewSubjectResults(AcademicSession $session, AcademicTerm $term, SchoolClass $class, Subject $subject)
    {
        // Get school settings for score limits
        $settings = SchoolSetting::getInstance();
        $maxCaScore = $settings->ca_weight ?? 30;
        $maxExamScore = $settings->max_exam_score ?? 70;
        $caWeight = ($settings->ca_weight ?? 40) ;
        $examWeight = ($settings->exam_weight ?? 60) ;

        // Get all students with their results for this subject
        $studentsResults = Student::where('school_class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get()
            ->map(function ($student) use ($session, $term, $subject, $caWeight, $examWeight) {
                $result = Result::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('academic_session_id', $session->id)
                    ->where('academic_term_id', $term->id)
                    ->first();

                $student->result = $result;
                $student->ca_score = $result?->ca_score ?? 0;
                $student->exam_score = $result?->exam_score ?? 0;
                // Calculate weighted total score
                $student->total_score = ($student->ca_score) + ($student->exam_score);

                return $student;
            });

        // Calculate statistics
        $stats = $this->calculateSubjectStats($studentsResults);

        return view('results.subject-results', compact(
            'session',
            'term',
            'class',
            'subject',
            'studentsResults',
            'stats',
            'maxCaScore',
            'maxExamScore'
        ));
    }

    private function isPassingScore($score, $passingScore)
{
    return $score >= (int) $passingScore;
}


    /**
     * View class report card - Student scores and grades (with cumulative scoring)
     */
public function classReport(AcademicSession $session, AcademicTerm $term, SchoolClass $class)
{
    // Get all students in this class
    $students = Student::where('school_class_id', $class->id)
        ->where('status', 'active')
        ->orderBy('first_name')
        ->get();

    // Get settings
    $settings = SchoolSetting::getInstance();
    $additionalSettings = $settings->additional_settings ?? [];
    if (is_string($additionalSettings)) {
        $additionalSettings = json_decode($additionalSettings, true) ?? [];
    }
    $gradeBoundaries = $additionalSettings['grade_boundaries'] ?? [];
    $passingScore = $additionalSettings['passing_score'] ?? 40;
    $caWeight = ($additionalSettings['ca_weight'] ?? 40);
    $examWeight = ($additionalSettings['exam_weight'] ?? 60);

    // Get all results for this class/term/session
    $allResults = Result::where('school_class_id', $class->id)
        ->where('academic_session_id', $session->id)
        ->where('academic_term_id', $term->id)
        ->get();

    // Build report data using cumulative scores (averaged across terms)
    $reportData = $students->map(function ($student) use ($allResults, $gradeBoundaries, $caWeight, $examWeight, $passingScore) {

        $studentResults = $allResults->where('student_id', $student->id);

        $totalScore = 0;
        $subjectCount = 0;
        $passCount = 0;

        foreach ($studentResults as $result) {

            // Get cumulative data - includes all previous terms
            $cumulativeData = $result->getCumulativeScore();
            $cumulativePrevious = $result->getCumulativePreviousScores();

            $caScore   = $result->ca_score ?? 0;
            $examScore = $result->exam_score ?? 0;

            // Current term score (simple addition)
            $currentTermTotal = ($caScore) + ($examScore);

            // Calculate average across all terms
            $previousTotal = $cumulativePrevious['previous_total'];
            $allTermsTotal = $previousTotal + $currentTermTotal;
            
            // Get number of terms to divide by
            $termCount = AcademicTerm::where('academic_session_id', $result->academic_session_id)
                ->where('term', '<=', $result->academicTerm->term ?? 1)
                ->count();
            
            // Cumulative score is the average of all terms
            $cumulativeScore = $termCount > 0 ? $allTermsTotal / $termCount : $currentTermTotal;

            // Compute grade using cumulative score
            $grade = $this->gradeFromSettings($cumulativeScore, $gradeBoundaries);

            $totalScore += $cumulativeScore;
            $subjectCount++;

            // PASS BASED on cumulative score and passing_score column
            if ($this->isPassingScore($cumulativeScore, $passingScore)) {
                $passCount++;
            }
        }

        return [
            'student' => $student,
            'total_score' => $totalScore,
            'average' => $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0,
            'subjects_taken' => $subjectCount,
            'passed' => $passCount,
            'failed' => $subjectCount - $passCount,
            'pass_percentage' => $subjectCount > 0 ? round(($passCount / $subjectCount) * 100) : 0,
        ];

    })->sortByDesc('average');

    // Class statistics
    $classStats = [
        'class_average' => round($reportData->average('average'), 2),
        'total_students' => $students->count(),
        'average_subjects' => round($reportData->average('subjects_taken')),
        'average_passed' => round($reportData->average('passed'), 1),
        'average_failed' => round($reportData->average('failed'), 1),
    ];

    return view('results.class-report', compact(
        'session',
        'term',
        'class',
        'reportData',
        'classStats',
        'gradeBoundaries'
    ));
}


    /**
     * Student Result Card - Individual report card (with cumulative scoring)
     */
    public function studentCard(AcademicSession $session, AcademicTerm $term, Student $student)
    {
        $results = Result::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->where('academic_term_id', $term->id)
            ->with('subject')
            ->get();

        $settings = SchoolSetting::getInstance();
        
        // Get grade boundaries from school settings - from additional_settings JSON
        $additionalSettings = $settings->additional_settings ?? [];
        if (is_string($additionalSettings)) {
            $additionalSettings = json_decode($additionalSettings, true) ?? [];
        }
        $gradeBoundaries = $additionalSettings['grade_boundaries'] ?? [];

        $caWeight   = $additionalSettings['ca_weight'] ?? 40;
        $examWeight = $additionalSettings['exam_weight'] ?? 60;

        // ✅ Passing score from SchoolSetting
        $passingScore = $additionalSettings['passing_score'] ?? 40; // default if not set

        // Compute grades with cumulative scoring (average across all terms)
        $resultsWithGrades = $results->map(function ($result) use ($gradeBoundaries) {

            // Get cumulative data - includes all previous terms
            $cumulativeData = $result->getCumulativeScore();
            $cumulativePrevious = $result->getCumulativePreviousScores();
            $previousTermBreakdown = $result->getPreviousTermBreakdown();

            $caScore   = $result->ca_score ?? 0;
            $examScore = $result->exam_score ?? 0;

            // Current term score (simple addition)
            $currentTermTotal = ($caScore) + ($examScore);

            // Calculate average across all terms
            $previousTotal = $cumulativePrevious['previous_total'];
            $allTermsTotal = $previousTotal + $currentTermTotal;
            
            // Get number of terms to divide by
            $termCount = AcademicTerm::where('academic_session_id', $result->academic_session_id)
                ->where('term', '<=', $result->academicTerm->term ?? 1)
                ->count();
            
            // Cumulative score is the average of all terms
            $cumulativeScore = $termCount > 0 ? $allTermsTotal / $termCount : $currentTermTotal;

            $grade  = $this->gradeFromSettings($cumulativeScore, $gradeBoundaries);
            $remark = $grade; // Remark is same as grade for now

            return [
                'result'              => $result,
                'current_score'       => $currentTermTotal,
                'cumulative_score'    => $cumulativeScore,
                'previous_terms'      => $previousTermBreakdown,
                'grade'               => $grade,
                'remark'              => $remark,
            ];
        });

        // SUMMARY - using cumulative scores
        $cumulativeScores = $resultsWithGrades->pluck('cumulative_score');
        
        $summary = [
            'total_subjects' => $results->count(),
            'average_score'  => $cumulativeScores->average(),
            'highest_score'  => $cumulativeScores->max(),
            'lowest_score'   => $cumulativeScores->min(),

            // Pass count based on cumulative scores
            'pass_count' => $resultsWithGrades->filter(fn($r) =>
                $r['cumulative_score'] >= $passingScore
            )->count(),

            'fail_count' => $resultsWithGrades->filter(fn($r) =>
                $r['cumulative_score'] < $passingScore
            )->count(),
        ];

        // Get first result record to extract assessment data
        $firstResult = $resultsWithGrades->first()['result'] ?? null;

        // Parse JSON assessment data from first result (stored as JSON in database)
        $behaviourAssessments = [
            'punctuality' => $firstResult?->behaviour_punctuality ?? 'Very Good',
            'participation' => $firstResult?->behaviour_participation ?? 'Very Good',
            'respect' => $firstResult?->behaviour_respect ?? 'Very Good',
        ];

        $psychomotorAssessments = [
            'handwriting' => $firstResult?->psychomotor_handwriting ?? 'Very Good',
            'creativity' => $firstResult?->psychomotor_creativity ?? 'Very Good',
            'sports' => $firstResult?->psychomotor_sports ?? 'Very Good',
        ];

        $affectiveAssessments = [
            'perseverance' => $firstResult?->affective_perseverance ?? 'Very Good',
            'control' => $firstResult?->affective_control ?? 'Very Good',
            'initiative' => $firstResult?->affective_initiative ?? 'Very Good',
        ];

        return view('results.student-card', compact(
            'student',
            'session',
            'term',
            'resultsWithGrades',
            'summary',
            'passingScore',
            'behaviourAssessments',
            'psychomotorAssessments',
            'affectiveAssessments'
        ));
    }    /**
     * Term Summary - All classes performance overview
     */
    public function termSummary(AcademicSession $session, AcademicTerm $term)
    {
        $settings = SchoolSetting::getInstance();
        $caWeight = ($settings->ca_weight ?? 40) ;
        $examWeight = ($settings->exam_weight ?? 60) ;
        
        $classes = SchoolClass::active()
            ->orderBy('name')
            ->get()
            ->map(function ($class) use ($session, $term, $caWeight, $examWeight) {
                $results = Result::where('school_class_id', $class->id)
                    ->where('academic_session_id', $session->id)
                    ->where('academic_term_id', $term->id)
                    ->get();

                // Calculate weighted scores
                $scores = $results->map(function($r) use ($caWeight, $examWeight) {
                    $caScore = $r->ca_score ?? 0;
                    $examScore = $r->exam_score ?? 0;
                    return ($caScore ) + ($examScore );
                });

                return [
                    'class' => $class,
                    'result_count' => $results->count(),
                    'average_score' => $scores->isNotEmpty() ? round($scores->average(), 2) : 0,
                    'highest_score' => $scores->isNotEmpty() ? $scores->max() : 0,
                    'lowest_score' => $scores->isNotEmpty() ? $scores->min() : 0,
                ];
            });

        return view('results.term-summary', compact('session', 'term', 'classes'));
    }

    /**
     * Helper: Get term statistics for dashboard
     */
    private function getTermStats()
    {
        $currentSession = AcademicSession::where('is_active', true)->first();

        if (!$currentSession) {
            return [];
        }

        return AcademicTerm::where('academic_session_id', $currentSession->id)
            ->orderBy('term')
            ->get()
            ->map(function ($term) use ($currentSession) {
                $resultCount = Result::where('academic_session_id', $currentSession->id)
                    ->where('academic_term_id', $term->id)
                    ->count();

                return [
                    'term' => $term,
                    'result_count' => $resultCount,
                ];
            });
    }

    /**
     * Helper: Calculate subject statistics
     */
    private function calculateSubjectStats($studentsResults)
    {
        $scores = $studentsResults->map(fn($s) => $s->total_score)->filter(fn($s) => $s > 0);

        return [
            'average' => $scores->isNotEmpty() ? round($scores->average(), 2) : 0,
            'highest' => $scores->isNotEmpty() ? $scores->max() : 0,
            'lowest' => $scores->isNotEmpty() ? $scores->min() : 0,
            'count' => $scores->count(),
        ];
    }

    /**
     * Helper: Get grade from score
     */
    private function gradeFromScore($score, $gradeScales)
    {
        $scale = $gradeScales->first(function ($gs) use ($score) {
            return $score >= $gs->min_score && $score <= $gs->max_score;
        });

        return $scale?->grade ?? 'F';
    }

    /**
     * Helper: Get remark from grade
     */
    private function remarkFromGrade($grade, $gradeScales)
    {
        $scale = $gradeScales->firstWhere('grade', $grade);
        return $scale?->remark ?? 'Incomplete';
    }

    /**
     * Helper: Check if grade is passing
     */
    private function isPassing($grade)
    {
        $scale = GradeScale::whereGrade($grade)->first();
        return $scale?->is_passing ?? false;
    }

    /**
     * Print result card for a student
     */
    /**
     * Print result card for a student (with cumulative scoring)
     */
    public function printResultCard(AcademicSession $session, AcademicTerm $term, Student $student)
    {
        // Generate QR code URL
        $qrCodeUrl = route('results.student.print', [
            'session' => $session->id,
            'term' => $term->id,
            'student' => $student->id
        ]);

        // Generate QR code data URI using endroid/qr-code v6
        try {
            $qrCode = new \Endroid\QrCode\QrCode($qrCodeUrl);
            $result = new \Endroid\QrCode\Writer\PngWriter();
            $pngData = $result->write($qrCode)->getString();
            $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($pngData);
        } catch (\Exception $e) {
            $qrCodeDataUri = null;
        }

        // Get all results for this student in this term
        $results = Result::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->where('academic_term_id', $term->id)
            ->with(['subject', 'schoolClass'])
            ->orderBy('subject_id')
            ->get();

        // Get attendance data for this term
        $attendanceData = $this->getStudentAttendanceForTerm($student->id, $session->id, $term->id);

        // Get settings
        $settings = SchoolSetting::getInstance();
        $additionalSettings = $settings->additional_settings ?? [];
        if (is_string($additionalSettings)) {
            $additionalSettings = json_decode($additionalSettings, true) ?? [];
        }
        $gradeBoundaries = $additionalSettings['grade_boundaries'] ?? [];

        $caWeight   = $additionalSettings['ca_weight'] ?? 40;
        $examWeight = $additionalSettings['exam_weight'] ?? 60;

        // ✅ Passing score from SchoolSetting
        $passingScore = $additionalSettings['passing_score'] ?? 40;

        // Pre-compute scores and grades for each result with cumulative scoring
        $resultsWithGrades = $results->map(function ($result) use ($caWeight, $examWeight, $gradeBoundaries) {

            // Get cumulative scores (includes previous terms)
            $cumulativeData = $result->getCumulativeScore();
            $cumulativePrevious = $result->getCumulativePreviousScores();
            $previousTermBreakdown = $result->getPreviousTermBreakdown();

            $caScore   = $result->ca_score ?? 0;
            $examScore = $result->exam_score ?? 0;

            // Current term score (simple addition)
            $currentTermTotal = ($caScore) + ($examScore);

            // Calculate average across all terms
            $previousTotal = $cumulativePrevious['previous_total'];
            $allTermsTotal = $previousTotal + $currentTermTotal;
            
            // Get number of terms to divide by
            $termCount = AcademicTerm::where('academic_session_id', $result->academic_session_id)
                ->where('term', '<=', $result->academicTerm->term ?? 1)
                ->count();
            
            // Cumulative score is the average of all terms
            $cumulativeScore = $termCount > 0 ? $allTermsTotal / $termCount : $currentTermTotal;

            $grade  = $this->gradeFromSettings($cumulativeScore, $gradeBoundaries);
            $remark = $grade; // Remark is same as grade

            return [
                'result'              => $result,
                'ca_score'            => $caScore,
                'exam_score'          => $examScore,
                'current_total'       => $currentTermTotal,
                'cumulative_total'    => $cumulativeScore,
                'previous_terms'      => $previousTermBreakdown,
                'grade'               => $grade,
                'remark'              => $remark,
            ];
        });

        // Calculate overall statistics using cumulative scores
        $cumulativeScores = $resultsWithGrades->pluck('cumulative_total');
        $totalScore    = $cumulativeScores->sum();
        $subjectCount  = $resultsWithGrades->count();

        // ✅ PASS COUNT now uses cumulative scores
        $passCount = $resultsWithGrades->filter(fn($r) =>
            $r['cumulative_total'] >= $passingScore
        )->count();

        // Average
        $averageScore = $subjectCount > 0
            ? round($totalScore / $subjectCount, 2)
            : 0;

        // Overall grade based on average cumulative score
        $overallGrade = $this->gradeFromSettings($averageScore, $gradeBoundaries);

        // Get the student's class for this term
        $studentClass = $results->first()?->schoolClass;
        
        // Calculate class statistics (average scores by subject and total student count)
        $classStatistics = [];
        $classStudentCount = 0;
        
        if ($studentClass) {
            // Get all students in this class during this term (those with results)
            $classStudentsInTerm = Result::where('academic_session_id', $session->id)
                ->where('academic_term_id', $term->id)
                ->where('school_class_id', $studentClass->id)
                ->distinct('student_id')
                ->count('student_id');
            
            $classStudentCount = $classStudentsInTerm;
            
            // Calculate average score per subject for this class/session/term
            $subjectAverages = Result::where('academic_session_id', $session->id)
                ->where('academic_term_id', $term->id)
                ->where('school_class_id', $studentClass->id)
                ->selectRaw('subject_id, AVG(total_score) as avg_score')
                ->groupBy('subject_id')
                ->with('subject')
                ->get();
            
            foreach ($subjectAverages as $subjectData) {
                $classStatistics[$subjectData->subject_id] = round($subjectData->avg_score, 1);
            }
        }

        // ✅ Fetch school settings
        $schoolSettings = SchoolSetting::getInstance();

        return view('results.print-card', compact(
            'student',
            'session',
            'term',
            'resultsWithGrades',
            'gradeBoundaries',
            'attendanceData',
            'averageScore',
            'overallGrade',
            'passCount',
            'subjectCount',
            'passingScore',
            'qrCodeDataUri',
            'classStatistics',
            'classStudentCount',
            'schoolSettings'
        ));
    }

    /**
     * Get student attendance for a specific term
     */
    private function getStudentAttendanceForTerm($studentId, $sessionId, $termId)
    {
        $term = AcademicTerm::findOrFail($termId);
        
        // Get attendance records for the term date range
        $attendanceRecords = \App\Models\Attendance::where('user_profile_id', $studentId)
            ->whereBetween('date', [$term->start_date, $term->end_date])
            ->get();

        // Count by status
        $present = $attendanceRecords->where('status', 'present')->count();
        $absent = $attendanceRecords->where('status', 'absent')->count();
        $late = $attendanceRecords->where('status', 'late')->count();
        $excused = $attendanceRecords->where('status', 'excused')->count();
        $total = $attendanceRecords->count();

        return [
            'present' => (int) $present,
            'absent' => (int) $absent,
            'late' => (int) $late,
            'excused' => (int) $excused,
            'total' => (int) $total,
            'attendance_percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Print selection page - Choose session, term, class, student
     */
    public function printSelection()
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        
        return view('results.print-selection', compact('sessions'));
    }

    /**
     * Class Report Selection - Select session, term, and class to view report
     */
    public function classReportSelection()
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        
        // Prepare data for cascading selects
        $sessionData = [];
        $termData = [];
        $classData = [];
        
        foreach ($sessions as $session) {
            $terms = AcademicTerm::where('academic_session_id', $session->id)
                ->orderBy('term')
                ->get(['id', 'term']);
            
            $sessionData[$session->id] = $session->session;
            $termData[$session->id] = $terms->toArray();
            
            foreach ($terms as $term) {
                $classes = SchoolClass::orderBy('name')->get(['id', 'name']);
                $classData[$term->id] = $classes->toArray();
            }
        }
        
        return view('results.class-report-selection', compact('sessions', 'sessionData', 'termData', 'classData'));
    }

    /**
     * Get terms for a session (AJAX)
     */
    public function getTermsForSession(AcademicSession $session)
    {
        // Get only released results for this session
        $termsWithResults = AcademicTerm::where('academic_session_id', $session->id)
            ->whereHas('results', function ($query) {
                $query->where('is_released', true);
            })
            ->select('id', 'term')
            ->orderBy('term')
            ->get();

        return response()->json($termsWithResults);
    }

    /**
     * Get classes for a term (AJAX)
     */
    public function getClassesForTerm(AcademicTerm $term)
    {
        // Get distinct classes that have released results in this term
        $classIds = Result::where('academic_term_id', $term->id)
            ->where('is_released', true)
            ->pluck('school_class_id')
            ->unique();

        $classesWithResults = SchoolClass::whereIn('id', $classIds)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($classesWithResults);
    }

    /**
     * Get students for a class (AJAX)
     */
    public function getStudentsForClass(Request $request)
    {
        $sessionId = $request->session_id;
        $termId = $request->term_id;
        $classId = $request->class_id;

        // Get distinct student IDs that have released results for this session, term, and class
        $studentIds = Result::where('academic_session_id', $sessionId)
            ->where('academic_term_id', $termId)
            ->where('school_class_id', $classId)
            ->where('is_released', true)
            ->pluck('student_id')
            ->unique();

        $studentsWithResults = Student::whereIn('id', $studentIds)
            ->select('id', 'first_name', 'last_name', 'admission_number')
            ->orderBy('first_name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'admission_number' => $student->admission_number,
                ];
            });

        return response()->json($studentsWithResults);
    }

    /**
     * Release results for a term
     */
    public function releaseTermResults(AcademicSession $session, AcademicTerm $term)
    {
        // Get all results for this term that haven't been released yet
        $results = Result::where('academic_session_id', $session->id)
            ->where('academic_term_id', $term->id)
            ->where('is_released', false)
            ->get();

        foreach ($results as $result) {
            $result->release();
        }

        return redirect()->back()->with('success', 'Results released successfully! ' . $results->count() . ' result(s) are now available for students.');
    }

    /**
     * Save comments for all results of a student in a term
     */
    public function saveComments(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:academic_terms,id',
            'student_id' => 'required|exists:user_profiles,id',
            'class_teacher_comment' => 'nullable|string|max:1000',
            'head_teacher_comment' => 'nullable|string|max:1000',
            'behaviour_punctuality' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'behaviour_participation' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'behaviour_respect' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'psychomotor_handwriting' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'psychomotor_creativity' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'psychomotor_sports' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'affective_perseverance' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'affective_control' => 'nullable|in:Excellent,Very Good,Good,Fair',
            'affective_initiative' => 'nullable|in:Excellent,Very Good,Good,Fair',
        ]);

        try {
            // Prepare update data
            $updateData = [];

            // Add comments if provided
            if ($request->filled('class_teacher_comment')) {
                $updateData['class_teacher_comment'] = $request->class_teacher_comment;
            }
            if ($request->filled('head_teacher_comment')) {
                $updateData['head_teacher_comment'] = $request->head_teacher_comment;
            }

            // Add individual assessment fields
            $assessmentFields = [
                'behaviour_punctuality',
                'behaviour_participation',
                'behaviour_respect',
                'psychomotor_handwriting',
                'psychomotor_creativity',
                'psychomotor_sports',
                'affective_perseverance',
                'affective_control',
                'affective_initiative',
            ];

            foreach ($assessmentFields as $field) {
                if ($request->filled($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            // Update all results for this student in this term
            $updated = Result::where('academic_session_id', $request->session_id)
                ->where('academic_term_id', $request->term_id)
                ->where('student_id', $request->student_id)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Comments and ratings saved successfully for {$updated} record(s)",
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving comments: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recall results for a term
     */
    public function recallTermResults(AcademicSession $session, AcademicTerm $term)
    {
        // Get all released results for this term
        $results = Result::where('academic_session_id', $session->id)
            ->where('academic_term_id', $term->id)
            ->where('is_released', true)
            ->get();

        foreach ($results as $result) {
            $result->recall();
        }

        return redirect()->back()->with('success', 'Results recalled successfully! Results are no longer available for students.');
    }
}
