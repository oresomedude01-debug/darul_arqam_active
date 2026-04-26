<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_session_id',
        'academic_term_id',
        'student_id',
        'subject_id',
        'school_class_id',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'remark',
        'status',
        'teacher_id',
        'approved_by',
        'teacher_comment',
        'class_teacher_comment',
        'head_teacher_comment',
        'submitted_at',
        'approved_at',
        'is_released',
        'released_at',
        'behaviour_rating',
        'psychomotor_rating',
        'affective_rating',
        // Individual assessment items
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

    protected $casts = [
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    public function scopeForTerm($query, $termId)
    {
        return $query->where('academic_term_id', $termId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'approved', 'finalized']);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'finalized']);
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    /**
     * Calculate total score based on school settings weights
     */
    public function calculateTotalScore()
    {
        if ($this->ca_score === null || $this->exam_score === null) {
            return null;
        }

        $settings = SchoolSetting::getInstance();
        $caWeight = ($settings->ca_weight ?? 40);
        $examWeight = ($settings->exam_weight ?? 60);

        return ($this->ca_score) + ($this->exam_score );
    }

    /**
     * Calculate grade based on total score using GradeScale
     */
    public function calculateGrade()
    {
        if ($this->total_score === null) {
            return null;
        }

        $gradeScale = GradeScale::where('min_score', '<=', $this->total_score)
            ->where('max_score', '>=', $this->total_score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->grade : 'F';
    }

    /**
     * Get remark based on total score
     */
    public function calculateRemark()
    {
        if ($this->total_score === null) {
            return null;
        }

        $gradeScale = GradeScale::where('min_score', '<=', $this->total_score)
            ->where('max_score', '>=', $this->total_score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->remark : 'Fail';
    }

    /**
     * Check if score is passing
     */
    public function isPassing()
    {
        if ($this->total_score === null) {
            return false;
        }

        $settings = SchoolSetting::getInstance();
        return $this->total_score >= ($settings->passing_score ?? 40);
    }

    /**
     * Static method to calculate grade for a given score
     */
    public static function calculateGradeForScore($score)
    {
        if ($score === null) {
            return null;
        }

        $gradeScale = GradeScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->grade : 'F';
    }

    /**
     * Static method to get remark for a given score
     */
    public static function getRemarkForScore($score)
    {
        if ($score === null) {
            return null;
        }

        $gradeScale = GradeScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->remark : 'Fail';
    }

    /**
     * Mark as submitted by teacher
     */
    public function submitByTeacher($teacherId)
    {
        $this->update([
            'status' => 'submitted',
            'teacher_id' => $teacherId,
            'submitted_at' => Carbon::now(),
        ]);
    }

    /**
     * Approve by admin
     */
    public function approveByAdmin($adminId, $comment = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => Carbon::now(),
            'class_teacher_comment' => $comment,
        ]);
    }

    /**
     * Finalize result
     */
    public function finalize($adminId, $comment = null)
    {
        $this->update([
            'status' => 'finalized',
            'approved_by' => $adminId,
            'approved_at' => Carbon::now(),
            'class_teacher_comment' => $comment,
        ]);
    }

    /**
     * Release result to student
     */
    public function release()
    {
        $this->update([
            'is_released' => true,
            'released_at' => Carbon::now(),
        ]);
    }

    /**
     * Recall released result
     */
    public function recall()
    {
        $this->update([
            'is_released' => false,
            'released_at' => null,
        ]);
    }

    /**
     * Scope to get only released results
     */
    public function scopeReleased($query)
    {
        return $query->where('is_released', true);
    }

    /**
     * Scope to get only unreleased results
     */
    public function scopeUnreleased($query)
    {
        return $query->where('is_released', false);
    }

    /**
     * Get cumulative scores from ALL previous terms in the same session
     * Used for calculating cumulative average across all terms
     * Returns array with keys: previous_ca, previous_exam, previous_total
     * previous_total = sum of (all previous term CA + all previous term Exam)
     */
    public function getCumulativePreviousScores()
    {
        $currentTerm = $this->academicTerm;
        
        if (!$currentTerm || !$this->academicSession) {
            return ['previous_ca' => 0, 'previous_exam' => 0, 'previous_total' => 0];
        }

        // Get all terms in this session, ordered by term number
        $allTerms = AcademicTerm::where('academic_session_id', $this->academic_session_id)
            ->orderBy('term', 'asc')
            ->get();

        // Find current term index
        $currentTermIndex = $allTerms->search(function ($term) use ($currentTerm) {
            return $term->id === $currentTerm->id;
        });

        // If current term is the first term (index 0), no previous scores
        if ($currentTermIndex === false || $currentTermIndex === 0) {
            return ['previous_ca' => 0, 'previous_exam' => 0, 'previous_total' => 0];
        }

        // Get all previous terms (all terms before the current term)
        $previousTermIds = $allTerms->slice(0, $currentTermIndex)->pluck('id')->toArray();

        // Get cumulative scores from all previous terms for this student and subject
        $previousResults = Result::where('student_id', $this->student_id)
            ->where('subject_id', $this->subject_id)
            ->where('school_class_id', $this->school_class_id)
            ->where('academic_session_id', $this->academic_session_id)
            ->whereIn('academic_term_id', $previousTermIds)
            ->get();

        $previousCa = $previousResults->sum('ca_score');
        $previousExam = $previousResults->sum('exam_score');
        // Previous total is simple addition: CA + Exam from all previous terms
        $previousTotal = $previousCa + $previousExam;

        return [
            'previous_ca' => (float) $previousCa,
            'previous_exam' => (float) $previousExam,
            'previous_total' => (float) $previousTotal,
        ];
    }

    /**
     * Get individual term scores for breakdown display
     * Returns array of previous terms with their CA, Exam, and Total for each
     * Example: [
     *   ['term_name' => 'First Term', 'ca' => 20, 'exam' => 40, 'total' => 60],
     *   ['term_name' => 'Second Term', 'ca' => 25, 'exam' => 35, 'total' => 60],
     * ]
     */
    public function getPreviousTermBreakdown()
    {
        $currentTerm = $this->academicTerm;
        
        if (!$currentTerm || !$this->academicSession) {
            return [];
        }

        // Get all terms in this session, ordered by term number
        $allTerms = AcademicTerm::where('academic_session_id', $this->academic_session_id)
            ->orderBy('term', 'asc')
            ->get();

        // Find current term index
        $currentTermIndex = $allTerms->search(function ($term) use ($currentTerm) {
            return $term->id === $currentTerm->id;
        });

        // If current term is the first term, no previous terms
        if ($currentTermIndex === false || $currentTermIndex === 0) {
            return [];
        }

        // Get all previous terms
        $previousTerms = $allTerms->slice(0, $currentTermIndex);

        $breakdown = [];
        foreach ($previousTerms as $term) {
            $result = Result::where('student_id', $this->student_id)
                ->where('subject_id', $this->subject_id)
                ->where('school_class_id', $this->school_class_id)
                ->where('academic_session_id', $this->academic_session_id)
                ->where('academic_term_id', $term->id)
                ->first();

            if ($result) {
                $breakdown[] = [
                    'term_name' => $term->term,
                    'ca' => (float) $result->ca_score,
                    'exam' => (float) $result->exam_score,
                    'total' => (float) ($result->ca_score + $result->exam_score),
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Get cumulative score including current and all previous terms
     * Cumulative total = all previous CA + all previous Exam + current CA + current Exam
     */
    public function getCumulativeScore()
    {
        $previous = $this->getCumulativePreviousScores();
        $currentCa = $this->ca_score ?? 0;
        $currentExam = $this->exam_score ?? 0;
        $currentTotal = $currentCa + $currentExam;
        
        return [
            'ca_score' => $currentCa + $previous['previous_ca'],
            'exam_score' => $currentExam + $previous['previous_exam'],
            'total_score' => $currentTotal + $previous['previous_total'],
        ];
    }

    /**
     * Auto-calculate scores and grades when saving
     */
    protected static function booted()
    {
        static::saving(function ($result) {
            // Calculate total score if CA and Exam score are present
            if ($result->ca_score !== null && $result->exam_score !== null) {
                $result->total_score = $result->calculateTotalScore();
            }

            // Calculate grade and remark if total score is present
            if ($result->total_score !== null) {
                $result->grade = $result->calculateGrade();
                $result->remark = $result->calculateRemark();
            }
        });
    }
}
