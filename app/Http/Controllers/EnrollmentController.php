<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistrationToken;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\SchoolSetting;
use App\Mail\ParentEnrollmentCredentials;
use App\Mail\StudentEnrolledNotification;
use App\Mail\EnrollmentCompletedNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    /**
     * Show token validation form (Step 1)
     */
    public function showTokenForm()
    {
        return view('enrollment.token');
    }

    /**
     * Validate token and proceed to enrollment (Step 1 submission)
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token_code' => 'required|string',
        ]);

        $token = RegistrationToken::where('code', $request->token_code)->first();

        if (!$token) {
            return back()->withErrors(['token_code' => 'Invalid token code. Please check and try again.']);
        }

        if (!$token->isValid()) {
            $reason = 'This token is no longer valid.';

            if ($token->status === 'consumed') {
                $reason = 'This token has already been used.';
            } elseif ($token->status === 'disabled') {
                $reason = 'This token has been disabled.';
            } elseif ($token->status === 'expired' || ($token->expires_at && Carbon::now()->isAfter($token->expires_at))) {
                $reason = 'This token has expired.';
            }

            return back()->withErrors(['token_code' => $reason]);
        }

        // Store token in session
        session([
            'enrollment_token_id' => $token->id,
            'enrollment_token_code' => $token->code,
            'enrollment_data' => [],
        ]);

        return redirect()->route('enrollment.step1');
    }

    /**
     * Show student details form (Step 2)
     */
    public function showStep1()
    {
        if (!session('enrollment_token_id')) {
            return redirect()->route('enrollment.token')->withErrors(['token' => 'Please enter a valid token first.']);
        }

        return view('enrollment.step1', [
            'data' => session('enrollment_data', [])
        ]);
    }

    /**
     * Process student details (Step 2 submission)
     */
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Please select a valid gender.',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('students/photos', 'public');
        }

        // Merge with existing data
        $enrollmentData = array_merge(session('enrollment_data', []), $validated);
        session(['enrollment_data' => $enrollmentData]);

        return redirect()->route('enrollment.step2');
    }

    /**
     * Show previous school form (Step 3)
     */
    public function showStep2()
    {
        if (!session('enrollment_token_id')) {
            return redirect()->route('enrollment.token');
        }

        return view('enrollment.step2', [
            'data' => session('enrollment_data', [])
        ]);
    }

    /**
     * Process previous school (Step 3 submission)
     */
    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'previous_school_name' => 'nullable|string|max:255',
            'previous_school_address' => 'nullable|string',
            'previous_school_grade' => 'nullable|string|max:100',
            'previous_school_year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'previous_school_reason' => 'nullable|string',
        ]);

        $enrollmentData = array_merge(session('enrollment_data', []), $validated);
        session(['enrollment_data' => $enrollmentData]);

        return redirect()->route('enrollment.step3');
    }

    /**
     * Show health information form (Step 4)
     */
    public function showStep3()
    {
        if (!session('enrollment_token_id')) {
            return redirect()->route('enrollment.token');
        }

        return view('enrollment.step3', [
            'data' => session('enrollment_data', [])
        ]);
    }

    /**
     * Process health information (Step 4 submission)
     */
    public function processStep3(Request $request)
    {
        $validated = $request->validate([
            'allergies' => 'nullable|array',
            'allergies.*' => 'string|max:255',
            'medical_conditions' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_medical_consent' => 'nullable|boolean',
            'special_needs' => 'nullable|string',
        ]);

        // Convert allergies to JSON if present
        if (isset($validated['allergies'])) {
            $validated['allergies'] = $validated['allergies'];
        }

        $validated['emergency_medical_consent'] = $request->has('emergency_medical_consent');

        $enrollmentData = array_merge(session('enrollment_data', []), $validated);
        session(['enrollment_data' => $enrollmentData]);

        return redirect()->route('enrollment.step4');
    }

    /**
     * Show parent/guardian form (Step 5)
     */
    public function showStep4()
    {
        if (!session('enrollment_token_id')) {
            return redirect()->route('enrollment.token');
        }

        return view('enrollment.step4', [
            'data' => session('enrollment_data', [])
        ]);
    }

    /**
     * Process parent/guardian and complete enrollment (Step 5 submission)
     */
    public function processStep4(Request $request)
    {
        $validated = $request->validate([
            'parent_name' => 'required|string|max:255',
            'parent_relationship' => 'required|string|max:50',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'required|email|max:255',
            'parent_occupation' => 'nullable|string|max:255',
        ]);

        // Merge all enrollment data
        $enrollmentData = array_merge(session('enrollment_data', []), $validated);

        // Get token
        $token = RegistrationToken::findOrFail(session('enrollment_token_id'));

        // Create or find parent user
        $parentEmail = $validated['parent_email'];
        $parentUser = User::where('email', $parentEmail)->first();
        $isNewParent = false;
        
        if (!$parentUser) {
            $isNewParent = true;
            // Create parent user
            $parentUser = User::create([
                'name' => $validated['parent_name'],
                'email' => $parentEmail,
                'password' => Hash::make('password123'), // Default password (must be changed)
            ]);
            
            // Create parent profile
            $parentUser->profile()->create([
                'first_name' => $validated['parent_name'],
                'phone' => $validated['parent_phone'],
                'occupation' => $validated['parent_occupation'] ?? null,
            ]);
            
            // Assign parent role
            $parentRole = Role::where('slug', 'parent')->first();
            if ($parentRole) {
                $parentUser->roles()->attach($parentRole->id);
            }
        }

        // Create student user
        $studentName = trim(($enrollmentData['first_name'] ?? '') . ' ' . ($enrollmentData['middle_name'] ?? '') . ' ' . ($enrollmentData['last_name'] ?? ''));
        
        // Get class ID from token (if assigned)
        $classId = $token->class_level ?? null;
        
        // Generate admission number with class information
        $admissionNumber = Student::generateAdmissionNumber($classId);
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'school.local';
        $studentEmail = strtolower($admissionNumber) . '@' . $domain;
        
        $studentUser = User::create([
            'name' => $studentName,
            'email' => $studentEmail,
            'password' => Hash::make(Str::random(16)),
        ]);
        
        // Create student profile with all data
        $profileData = [
            'first_name' => $enrollmentData['first_name'] ?? null,
            'middle_name' => $enrollmentData['middle_name'] ?? null,
            'last_name' => $enrollmentData['last_name'] ?? null,
            'gender' => $enrollmentData['gender'] ?? null,
            'date_of_birth' => $enrollmentData['date_of_birth'] ?? null,
            'blood_group' => $enrollmentData['blood_group'] ?? null,
            'nationality' => $enrollmentData['nationality'] ?? null,
            'phone' => $enrollmentData['phone'] ?? null,
            'address' => $enrollmentData['address'] ?? null,
            'admission_date' => now(),
            'admission_number' => $admissionNumber,
            'registration_token_id' => $token->id,
            'school_class_id' => $token->class_level ?? null, // Assign student to token's class
            'parent_id' => $parentUser->id,
            'relationship' => $validated['parent_relationship'],
            'status' => 'active',
            'medical_conditions' => $enrollmentData['medical_conditions'] ?? null,
        ];
        
        // Handle photo if uploaded
        if (!empty($enrollmentData['photo_path'])) {
            $profileData['photo'] = $enrollmentData['photo_path'];
        }

        $studentProfile = $studentUser->profile()->create($profileData);
        
        // Assign student role
        $studentRole = Role::where('slug', 'student')->first();
        if ($studentRole) {
            $studentUser->roles()->attach($studentRole->id);
        }

        // Mark token as consumed
        $token->markAsConsumed($studentUser->id, $request->ip());

        // Get admission number for email
        $admissionNumber = $studentProfile->admission_number;

        // Send enrollment notification emails to parent
        try {
            if ($isNewParent) {
                // New parent: send credentials email
                Mail::to($parentUser->email)->send(new ParentEnrollmentCredentials($parentUser, $studentName));
            } else {
                // Existing parent: send enrollment notification
                Mail::to($parentUser->email)->send(new StudentEnrolledNotification($parentUser, $studentName, $admissionNumber));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send parent enrollment email: ' . $e->getMessage());
        }

        // Send enrollment notification to school email
        $schoolSettings = SchoolSetting::first();
        if ($schoolSettings && $schoolSettings->school_email) {
            try {
                Mail::to($schoolSettings->school_email)
                    ->send(new EnrollmentCompletedNotification($studentUser, $parentUser, $enrollmentData));
            } catch (\Exception $e) {
                \Log::error('Failed to send enrollment notification to school: ' . $e->getMessage());
                // Continue with enrollment even if email fails
            }
        }

        // Store student info in session for success page
        session([
            'enrollment_student_name' => $studentName,
            'enrollment_admission_number' => $studentProfile->admission_number
        ]);

        // Clear enrollment data
        session()->forget(['enrollment_token_id', 'enrollment_token_code', 'enrollment_data']);

        return redirect()->route('enrollment.success');
    }

    /**
     * Show success page
     */
    public function showSuccess()
    {
        $admissionNumber = session('enrollment_admission_number');
        $studentName = session('enrollment_student_name');

        if (!$admissionNumber) {
            return redirect()->route('enrollment.token');
        }

        // Get school settings
        $schoolSettings = \App\Models\SchoolSetting::first();

        // Clear admission number from session after displaying
        session()->forget(['enrollment_admission_number', 'enrollment_student_name']);

        return view('enrollment.success', [
            'studentName' => $studentName,
            'admissionNumber' => $admissionNumber,
            'schoolSettings' => $schoolSettings
        ]);
    }

    public function success()
    {
        return $this->showSuccess();
    }
}
