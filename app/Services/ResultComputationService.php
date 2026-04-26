<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\Result;
use App\Models\SchoolSetting;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultComputationService
{
    public function compute(AcademicSession $session, AcademicTerm $term, $student)
    {
        // ✅ Only fetch RELEASED results
        $results = Result::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->where('academic_term_id', $term->id)
            ->where('is_released', true)
            ->with(['subject', 'academicTerm'])
            ->get();

        $settings = SchoolSetting::getInstance();
        $additionalSettings = $settings->additional_settings ?? [];

        if (is_string($additionalSettings)) {
            $additionalSettings = json_decode($additionalSettings, true) ?? [];
        }

        $gradeBoundaries = $additionalSettings['grade_boundaries'] ?? [];
        $passingScore   = $additionalSettings['passing_score'] ?? 40;

        $resultsWithGrades = $results->map(function ($result) use ($gradeBoundaries) {

            $previousBreakdown = $result->getPreviousTermBreakdown();
            $previousTotal     = $result->getCumulativePreviousScores()['previous_total'];

            $currentTotal = ($result->ca_score ?? 0) + ($result->exam_score ?? 0);

            $termCount = \App\Models\AcademicTerm::where(
                'academic_session_id',
                $result->academic_session_id
            )->where(
                'term',
                '<=',
                $result->academicTerm->term ?? 1
            )->count();

            $cumulativeScore = $termCount > 0
                ? ($previousTotal + $currentTotal) / $termCount
                : $currentTotal;

            $grade = $this->gradeFromSettings($cumulativeScore, $gradeBoundaries);

            $remark = $grade;

            return [
                'result'           => $result,
                'current_score'    => $currentTotal,
                'cumulative_score' => $cumulativeScore,
                'previous_terms'   => $previousBreakdown,
                'grade'            => $grade,
                'remark'           => $remark,
                'is_passing'       => $cumulativeScore >= 40,
            ];
        });

        return [
            'results' => $resultsWithGrades,
            'summary' => [
                'average' => $resultsWithGrades->avg('cumulative_score'),
                'highest' => $resultsWithGrades->max('cumulative_score'),
                'lowest'  => $resultsWithGrades->min('cumulative_score'),
                'pass'    => $resultsWithGrades->where('is_passing', true)->count(),
            ],
        ];
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


}
