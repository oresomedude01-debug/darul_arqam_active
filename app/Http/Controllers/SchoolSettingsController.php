<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\AcademicTerm;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingsController extends Controller
{
    /**
     * Display the school settings page
     */
    public function index()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.index', compact('settings'));
    }

    /**
     * Display the general settings form
     */
    public function editGeneral()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string',
            'school_phone' => 'nullable|string|max:20',
            'school_email' => 'nullable|email',
            'school_motto' => 'nullable|string',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $settings = SchoolSetting::getInstance();

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            if ($settings->school_logo && Storage::exists('public/' . $settings->school_logo)) {
                Storage::delete('public/' . $settings->school_logo);
            }

            $validated['school_logo'] = $request->file('school_logo')->store('school-logo', 'public');
        }

        $settings->update($validated);

        return redirect()->route('settings.school.index')
            ->with('success', 'School information updated successfully!');
    }

    /**
     * Display the academic settings form
     */
    public function editAcademic()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-academic', compact('settings'));
    }

    /**
     * Update academic settings
     */
    public function updateAcademic(Request $request)
    {
        $validated = $request->validate([
            'current_session' => 'required|string|max:20|exists:academic_terms,session',
            'current_term' => 'required|in:First Term,Second Term,Third Term',
        ]);

        $settings = SchoolSetting::getInstance();
        $settings->update($validated);

        return redirect()->route('settings.school.edit-academic')
            ->with('success', 'Academic settings updated successfully!');
    }

    /**
     * Display the school days form
     */
    public function editSchoolDays()
    {
        $settings = SchoolSetting::getInstance();
        $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        return view('settings.school.edit-school-days', compact('settings', 'allDays'));
    }

    /**
     * Update school days
     */
    public function updateSchoolDays(Request $request)
    {
        $validated = $request->validate([
            'school_days' => 'required|array|min:1',
            'school_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $settings = SchoolSetting::getInstance();
        $settings->school_days = $validated['school_days'];
        $settings->save();

        return redirect()->route('settings.school.index')
            ->with('success', 'School operating days updated successfully!');
    }

    /**
     * Display the grading settings form
     */
    public function editGrading()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-grading', compact('settings'));
    }

    /**
     * Update grading settings
     */
    public function updateGrading(Request $request)
    {
        $validated = $request->validate([
            'passing_score' => 'required|integer|min:0|max:100',
            'ca_weight' => 'required|integer|min:0|max:100',
            'exam_weight' => 'required|integer|min:0|max:100',
            'grade_boundaries' => 'required|array',
            'grade_boundaries.*' => 'required|integer|min:0|max:100',
        ]);

        // Validate weights add up to 100
        if (($validated['ca_weight'] + $validated['exam_weight']) != 100) {
            return back()->withErrors(['weights' => 'CA and Exam weights must add up to 100%']);
        }

        $settings = SchoolSetting::getInstance();
        
        // Store grading settings in additional_settings JSON
        $additionalSettings = $settings->additional_settings ?? [];
        $additionalSettings['passing_score'] = $validated['passing_score'];
        $additionalSettings['ca_weight'] = $validated['ca_weight'];
        $additionalSettings['exam_weight'] = $validated['exam_weight'];
        $additionalSettings['grade_boundaries'] = $validated['grade_boundaries'];
        
        $settings->additional_settings = $additionalSettings;
        $settings->save();

        return redirect()->route('settings.school.index')
            ->with('success', 'Grading settings updated successfully!');
    }

    /**
     * Display the system preferences form
     */
    public function editPreferences()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-preferences', compact('settings'));
    }

    /**
     * Update system preferences
     */
    public function updatePreferences(Request $request)
    {
        $settings = SchoolSetting::getInstance();
        
        // Store preferences in additional_settings JSON
        $additionalSettings = $settings->additional_settings ?? [];
        $additionalSettings['teachers_can_enter_scores'] = $request->has('teachers_can_enter_scores');
        $additionalSettings['parents_can_view_results'] = $request->has('parents_can_view_results');
        $additionalSettings['parents_can_view_attendance'] = $request->has('parents_can_view_attendance');
        $additionalSettings['require_daily_attendance'] = $request->has('require_daily_attendance');
        $additionalSettings['enable_notifications'] = $request->has('enable_notifications');
        $additionalSettings['enable_fees_module'] = $request->has('enable_fees_module');
        $additionalSettings['enable_library_module'] = $request->has('enable_library_module');
        
        $settings->additional_settings = $additionalSettings;
        $settings->save();

        return redirect()->route('settings.school.index')
            ->with('success', 'System preferences updated successfully!');
    }

    /**
     * Display the promotion settings form
     */
    public function editPromotion()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-promotion', compact('settings'));
    }

    /**
     * Update promotion settings
     */
    public function updatePromotion(Request $request)
    {
        $validated = $request->validate([
            'auto_promotion' => 'required|boolean',
            'pass_mark' => 'required|integer|min:0|max:100',
            'min_average' => 'required|numeric|min:0|max:100',
            'max_failed_subjects' => 'required|integer|min:0',
            'allow_retention' => 'boolean',
            'allow_skipping' => 'boolean',
        ]);

        $settings = SchoolSetting::getInstance();
        
        // Store promotion settings in additional_settings JSON
        $additionalSettings = $settings->additional_settings ?? [];
        $additionalSettings['promotion_settings'] = [
            'auto_promotion' => $request->has('auto_promotion') && $validated['auto_promotion'] == '1',
            'pass_mark' => $validated['pass_mark'],
            'min_average' => $validated['min_average'],
            'max_failed_subjects' => $validated['max_failed_subjects'],
            'allow_retention' => $request->has('allow_retention'),
            'allow_skipping' => $request->has('allow_skipping'),
        ];
        $settings->additional_settings = $additionalSettings;
        $settings->save();

        return redirect()->route('settings.school.index')
            ->with('success', 'Promotion settings updated successfully!');
    }

    /**
     * Display the currency settings form
     */
    public function editCurrency()
    {
        $settings = SchoolSetting::getInstance();
        
        return view('settings.school.edit-currency', compact('settings'));
    }

    /**
     * Update currency settings
     */
    public function updateCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
        ]);

        $settings = SchoolSetting::getInstance();
        $settings->currency_code = $validated['currency_code'];
        $settings->currency_symbol = $validated['currency_symbol'];
        $settings->save();

        return redirect()->route('settings.school.index')
            ->with('success', 'Currency settings updated successfully!');
    }

    /**
     * Display academic sessions management page
     */
    public function academicSessions(Request $request)
    {
        $query = AcademicSession::query();
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where('session', 'like', '%' . $search . '%');
        }
        
        $sessions = $query->orderBy('session', 'desc')->get();
        
        $settings = SchoolSetting::getInstance();
        $activeSession = $settings->active_session_id ? AcademicSession::find($settings->active_session_id) : null;
        $activeTerm = $settings->active_term_id ? AcademicTerm::find($settings->active_term_id) : null;
        
        return view('settings.school.academic.index', compact('sessions', 'settings', 'activeSession', 'activeTerm'));
    }

    /**
     * Display create new academic session form
     */
    public function createAcademicSession()
    {
        return view('settings.school.academic.create');
    }

    /**
     * Store a new academic session with its three terms
     */
    public function storeAcademicSession(Request $request)
    {
        $validated = $request->validate([
            'session' => 'required|string|unique:academic_sessions,session',
            'term1_name' => 'required|string',
            'term1_start_date' => 'required|date',
            'term1_end_date' => 'required|date|after:term1_start_date',
            'term2_name' => 'required|string',
            'term2_start_date' => 'required|date|after:term1_end_date',
            'term2_end_date' => 'required|date|after:term2_start_date',
            'term3_name' => 'required|string',
            'term3_start_date' => 'required|date|after:term2_end_date',
            'term3_end_date' => 'required|date|after:term3_start_date',
        ], [
            'term2_start_date.after' => 'Second term must start after First term ends. No date overlap allowed.',
            'term3_start_date.after' => 'Third term must start after Second term ends. No date overlap allowed.',
        ]);

        // Create academic session
        $academicSession = AcademicSession::create([
            'session' => $validated['session'],
            'is_active' => false,
        ]);

        // Create three terms for the session
        AcademicTerm::create([
            'name' => $validated['term1_name'],
            'session' => $validated['session'],
            'academic_session_id' => $academicSession->id,
            'term' => 'First Term',
            'start_date' => $validated['term1_start_date'],
            'end_date' => $validated['term1_end_date'],
            'status' => 'upcoming',
            'is_active' => false,
        ]);

        AcademicTerm::create([
            'name' => $validated['term2_name'],
            'session' => $validated['session'],
            'academic_session_id' => $academicSession->id,
            'term' => 'Second Term',
            'start_date' => $validated['term2_start_date'],
            'end_date' => $validated['term2_end_date'],
            'status' => 'upcoming',
            'is_active' => false,
        ]);

        AcademicTerm::create([
            'name' => $validated['term3_name'],
            'session' => $validated['session'],
            'academic_session_id' => $academicSession->id,
            'term' => 'Third Term',
            'start_date' => $validated['term3_start_date'],
            'end_date' => $validated['term3_end_date'],
            'status' => 'upcoming',
            'is_active' => false,
        ]);

        return redirect()->route('settings.school.academic.sessions')
            ->with('success', 'Academic session created successfully with all three terms!');
    }

    /**
     * Display edit academic session form (edit terms calendar for a session)
     */
    public function editAcademicSession(AcademicSession $academicSession)
    {
        $terms = $academicSession->terms()->orderBy('term')->get();

        if ($terms->isEmpty()) {
            return redirect()->route('settings.school.academic.sessions')
                ->with('error', 'Session not found');
        }

        $settings = SchoolSetting::getInstance();
        $session = $academicSession->session;

        return view('settings.school.academic.edit', compact('academicSession', 'session', 'terms', 'settings'));
    }

    /**
     * Update academic session terms
     */
    public function updateAcademicSession(Request $request, AcademicSession $academicSession)
    {
        $validated = $request->validate([
            'term1_name' => 'required|string',
            'term1_start_date' => 'required|date',
            'term1_end_date' => 'required|date|after:term1_start_date',
            'term2_name' => 'required|string',
            'term2_start_date' => 'required|date|after:term1_end_date',
            'term2_end_date' => 'required|date|after:term2_start_date',
            'term3_name' => 'required|string',
            'term3_start_date' => 'required|date|after:term2_end_date',
            'term3_end_date' => 'required|date|after:term3_start_date',
            'current_term' => 'nullable|in:First Term,Second Term,Third Term',
        ], [
            'term2_start_date.after' => 'Second term must start after First term ends. No date overlap allowed.',
            'term3_start_date.after' => 'Third term must start after Second term ends. No date overlap allowed.',
        ]);

        $termNames = ['First Term', 'Second Term', 'Third Term'];
        $termFields = ['term1', 'term2', 'term3'];

        foreach ($termFields as $index => $field) {
            $termName = $termNames[$index];
            $term = $academicSession->terms()->where('term', $termName)->first();

            if ($term) {
                $term->update([
                    'name' => $validated["{$field}_name"],
                    'start_date' => $validated["{$field}_start_date"],
                    'end_date' => $validated["{$field}_end_date"],
                ]);
            }
        }

        // Update current session and term if current_term is provided
        if (!empty($validated['current_term'])) {
            // Deactivate all terms within THIS session only
            $academicSession->terms()->update(['is_active' => false]);
            
            // Activate the selected term
            $academicSession->terms()
                ->where('term', $validated['current_term'])
                ->update(['is_active' => true]);
            
            // Update SchoolSetting with active session and term IDs
            $activeTerm = $academicSession->terms()
                ->where('term', $validated['current_term'])
                ->first();
            
            $settings = SchoolSetting::getInstance();
            $settings->active_session_id = $academicSession->id;
            $settings->active_term_id = $activeTerm?->id;
            $settings->save();

            return redirect()->route('settings.school.academic.sessions')
                ->with('success', 'Session updated and current term set successfully!');
        }

        return redirect()->route('settings.school.academic.sessions')
            ->with('success', 'Session updated successfully!');
    }

    /**
     * Delete academic session and its terms
     */
    public function deleteAcademicSession(AcademicSession $academicSession)
    {
        $sessionName = $academicSession->session;
        $academicSession->delete();

        return redirect()->route('settings.school.academic.sessions')
            ->with('success', "Academic session $sessionName deleted successfully!");
    }

    /**
     * Set a session as current (quick action from sessions list)
     */
    public function setCurrentSession(AcademicSession $academicSession)
    {
        // Verify session exists with terms
        if ($academicSession->terms()->count() === 0) {
            return redirect()->route('settings.school.academic.sessions')
                ->with('error', 'Session has no terms');
        }

        // Deactivate all other sessions and terms
        AcademicSession::where('is_active', true)->update(['is_active' => false]);
        AcademicTerm::where('is_active', true)->update(['is_active' => false]);

        // Activate this session and its first term
        $firstTerm = $academicSession->terms()->where('term', 'First Term')->first();
        
        if ($firstTerm) {
            $firstTerm->update(['is_active' => true]);
        }
        
        $academicSession->update(['is_active' => true]);

        // Update SchoolSetting with active session and term IDs
        $settings = SchoolSetting::getInstance();
        $settings->active_session_id = $academicSession->id;
        $settings->active_term_id = $firstTerm?->id;
        $settings->save();

        return redirect()->route('settings.school.academic.sessions')
            ->with('success', 'Session set as current successfully!');
    }

    /**
     * Edit Financial Settings
     */
    public function editFinancial()
    {
        $settings = SchoolSetting::getInstance();
        return view('settings.school.edit-financial', compact('settings'));
    }

    /**
     * Update Financial Settings
     */
    public function updateFinancial(Request $request)
    {
        $validated = $request->validate([
            // Bank Account Details
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_type' => 'nullable|string|max:50',
            'bank_code' => 'nullable|string|max:20',
            'routing_number' => 'nullable|string|max:20',
            'swift_code' => 'nullable|string|max:20',
            'iban' => 'nullable|string|max:50',
            // Mobile Money
            'mobile_money_provider' => 'nullable|string|max:255',
            'mobile_money_number' => 'nullable|string|max:50',
            // Cheque Details
            'cheque_payable_to' => 'nullable|string|max:255',
            'cheque_instructions' => 'nullable|string',
            // General Payment Instructions
            'payment_instructions' => 'nullable|string',
            // Finance Contact
            'finance_contact_name' => 'nullable|string|max:255',
            'finance_contact_email' => 'nullable|email|max:255',
            'finance_contact_phone' => 'nullable|string|max:20',
        ]);

        $settings = SchoolSetting::getInstance();
        $settings->update($validated);

        return redirect()->route('settings.school.index')
            ->with('success', 'Financial settings updated successfully!');
    }

    /**
     * Edit Paystack Settings
     */
    public function editPaystack()
    {
        $settings = SchoolSetting::getInstance();
        return view('settings.school.edit-paystack', compact('settings'));
    }

    /**
     * Update Paystack Settings
     */
    public function updatePaystack(Request $request)
    {
        $validated = $request->validate([
            'paystack_public_key' => 'nullable|string|max:255',
            'paystack_secret_key' => 'nullable|string|max:255',
            'enable_online_payment' => 'nullable|boolean',
        ]);

        $settings = SchoolSetting::getInstance();
        
        // Handle checkbox: convert unchecked to false
        $validated['enable_online_payment'] = $request->has('enable_online_payment') ? true : false;
        
        $settings->update($validated);

        return redirect()->route('settings.school.index')
            ->with('success', 'Paystack settings updated successfully!');
    }
}