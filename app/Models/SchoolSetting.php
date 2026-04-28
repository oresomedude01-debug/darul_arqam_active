<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $table = 'school_settings';

    protected $fillable = [
        'school_name',
        'pwa_app_name',
        'pwa_short_name',
        'pwa_icon',
        'pwa_theme_color',
        'pwa_background_color',
        'school_address',
        'school_phone',
        'school_email',
        'school_website',
        'school_logo',
        'school_motto',
        'school_vision',
        'school_mission',
        'principal_name',
        'vice_principal_name',
        'footer_text',
        'active_session_id',
        'active_term_id',
        'grading_system',
        'grade_scale',
        'number_of_terms',
        'session_start_date',
        'session_end_date',
        'school_calendar',
        'holiday_dates',
        'enable_parent_portal',
        'enable_student_portal',
        'enable_online_payments',
        'default_currency',
        'timezone',
        'require_photo_on_registration',
        'require_parent_approval_for_student_registration',
        'allow_bulk_grade_upload',
        'send_sms_notifications',
        'send_email_notifications',
        'additional_settings',
        // Financial fields
        'bank_name',
        'account_holder_name',
        'account_number',
        'account_type',
        'bank_code',
        'routing_number',
        'swift_code',
        'iban',
        'paystack_public_key',
        'paystack_secret_key',
        'paystack_merchant_email',
        'enable_online_payment',
        'default_payment_method',
    ];

    protected $casts = [
        'school_days' => 'array',
        'grade_boundaries' => 'array',
        'promotion_settings' => 'array',
        'additional_settings' => 'array',
        'term_start_date' => 'date',
        'term_end_date' => 'date',
        'session_start_date' => 'date',
        'session_end_date' => 'date',
        'teachers_can_enter_scores' => 'boolean',
        'parents_can_view_results' => 'boolean',
        'parents_can_view_attendance' => 'boolean',
        'require_daily_attendance' => 'boolean',
        'enable_notifications' => 'boolean',
        'enable_fees_module' => 'boolean',
        'enable_library_module' => 'boolean',
        'enable_online_payment' => 'boolean',
    ];

    /**
     * Get the singleton instance of school settings
     */
    public static function getInstance()
    {
        return self::first() ?? self::create([
            'school_name' => 'School Name',
            'school_address' => '',
            'school_phone' => '',
            'school_email' => '',
        ]);
    }

    /**
     * Get the school setting value by key
     */
    public static function get($key, $default = null)
    {
        $settings = self::getInstance();
        return $settings->{$key} ?? $default;
    }

    /**
     * Set a school setting value
     */
    public static function set($key, $value)
    {
        $settings = self::getInstance();
        $settings->{$key} = $value;
        $settings->save();
        return $settings;
    }

    /**
     * Get grade boundaries from additional_settings
     */
    public function getGradeBoundariesAttribute()
    {
        return $this->additional_settings['grade_boundaries'] ?? [
            'A' => 80,
            'B' => 70,
            'C' => 60,
            'D' => 50,
            'E' => 40,
            'F' => 0
        ];
    }

    /**
     * Get CA weight from additional_settings
     */
    public function getCaWeightAttribute()
    {
        return $this->additional_settings['ca_weight'] ?? 30;
    }

    /**
     * Get exam weight from additional_settings
     */
    public function getExamWeightAttribute()
    {
        return $this->additional_settings['exam_weight'] ?? 70;
    }

    /**
     * Get passing score from additional_settings
     */
    public function getPassingScoreAttribute()
    {
        return $this->additional_settings['passing_score'] ?? 40;
    }

    /**
     * Check if school is operating on a specific day
     */
    public function isOperatingDay($day)
    {
        return in_array($day, $this->school_days ?? []);
    }

    /**
     * Get all operating days
     */
    public function getOperatingDays()
    {
        return $this->school_days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }

    /**
     * Get operating days as a comma-separated string
     */
    public function getOperatingDaysString()
    {
        return implode(', ', $this->getOperatingDays());
    }

    /**
     * Get grade for a given score
     */
    public function getGradeForScore($score)
    {
        $boundaries = $this->additional_settings['grade_boundaries'] ?? [
            'A' => 80,
            'B' => 70,
            'C' => 60,
            'D' => 50,
            'E' => 40,
            'F' => 0,
        ];
        
        // Sort boundaries in descending order
        arsort($boundaries);
        
        foreach ($boundaries as $grade => $minScore) {
            if ($score >= $minScore) {
                return $grade;
            }
        }
        
        return 'F';
    }

    /**
     * Get grade point for a given score
     */
    public function getGradePointForScore($score)
    {
        $grade = $this->getGradeForScore($score);
        
        $gradePoints = [
            'A' => 5.0,
            'B' => 4.0,
            'C' => 3.0,
            'D' => 2.0,
            'E' => 1.0,
            'F' => 0.0,
        ];
        
        return $gradePoints[$grade] ?? 0.0;
    }

    /**
     * Check if a score passes
     */
    public function isPassing($score)
    {
        $passingScore = $this->additional_settings['passing_score'] ?? 40;
        return $score >= $passingScore;
    }

    /**
     * Calculate final score from CA and Exam
     */
    public function calculateFinalScore($ca, $exam)
    {
        $caWeight = ($this->additional_settings['ca_weight'] ?? 30) / 100;
        $examWeight = ($this->additional_settings['exam_weight'] ?? 70) / 100;
        
        return ($ca * $caWeight) + ($exam * $examWeight);
    }

    
}
