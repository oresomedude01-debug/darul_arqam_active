<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'results';

    protected $fillable = [
        'student_id',
        'subject_id',
        'school_class_id',
        'academic_session_id',
        'academic_term_id',
        'total_score',
        'ca_score',
        'exam_score',
        'grade',
        'remark',
        'position_in_class',
        'position_in_subject',
        'remarks',
        'teacher_comment',
        'class_teacher_comment',
        'head_teacher_comment',
        'status',
        'teacher_id',
        'approved_by',
        'submitted_at',
        'approved_at',
        'is_released',
        'behaviour_rating',
        'psychomotor_rating',
        'affective_rating',
        'behaviour_punctuality',
        'behaviour_participation',
        'behaviour_respect',
        'psychomotor_handwriting',
        'psychomotor_creativity',
        'psychomotor_sports',
        'affective_perseverance',
        'affective_control',
        'affective_initiative',
        'assessment_ratings',
        'individual_assessments',
        'released',
        'released_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'is_released' => 'boolean',
        'released' => 'boolean',
    ];

    // Relationships
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
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function recorder()
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }

    // Scopes
    public function scopeForTerm($query, $term, $session)
    {
        return $query->where('term', $term)->where('session', $session);
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

    public function scopeForExamType($query, $examTypeId)
    {
        return $query->where('exam_type_id', $examTypeId);
    }

    // Helpers
    public static function calculateGrade($score)
    {
        $gradeScale = GradeScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->grade : 'F';
    }

    public static function getGradeRemark($score)
    {
        $gradeScale = GradeScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $gradeScale ? $gradeScale->remark : 'Fail';
    }

    // Auto-calculate grade when score is set
    protected static function booted()
    {
        static::saving(function ($grade) {
            if ($grade->score !== null) {
                $grade->grade = self::calculateGrade($grade->score);
            }
        });
    }
}
