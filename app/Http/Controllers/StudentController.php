<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\SchoolClass;
use App\Models\ParentContact;
use App\Models\RegistrationToken;
use App\Mail\StudentEnrollmentNotification;
use App\Traits\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource with filters
     */
    public function index(Request $request)
    {
        // Authorization: Admin, Teachers can view all students; Students can view own profile only
        $user = auth()->user();
        
        $query = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        });

        // Apply role-based filters
        // Only filter if user is a student AND not an admin or teacher
        if ($user->isStudent() && !$user->isAdmin() && !$user->isTeacher()) {
            // Students can only see their own profile
            $query->where('user_id', $user->id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        // Apply class filter
        if ($request->filled('class')) {
            $query->where('school_class_id', $request->class);
        }

        // Apply gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply admission year filter
        if ($request->filled('admission_year')) {
            $query->whereYear('admission_date', $request->admission_year);
        }

        // Get students with pagination - sort by creation date (latest first)
        // All statuses are shown unless specifically filtered
        $perPage = $request->get('per_page', 15);
        $students = $query->with(['user', 'schoolClass'])
            ->latest('created_at')
            ->paginate($perPage);

        // Get dynamic lists for filters
        $schoolClasses = SchoolClass::active()->orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->count(),
            'active' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('status', 'active')->count(),
            'pending' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('status', 'pending')->count(),
            'inactive' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('status', 'inactive')->count(),
            'male' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('gender', 'male')->count(),
            'female' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'student');
                });
            })->where('gender', 'female')->count(),
        ];

        return view('students.index', compact('students', 'schoolClasses', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('create-student')) {
            abort(403, 'Unauthorized to create students');
        }
        
        $schoolClasses = SchoolClass::active()->get();
        return view('students.create', compact('schoolClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('create-student')) {
            abort(403, 'Unauthorized to create students');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'nationality' => 'nullable|string',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'admission_date' => 'nullable|date',
            'previous_school' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|array',
            'allergies.*' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_medical_consent' => 'nullable|boolean',
            'special_needs' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended,graduated,transferred',
            // Parent fields
            'parent1_name' => 'nullable|string|max:255',
            'parent1_email' => 'nullable|email',
            'parent1_phone' => 'nullable|string',
            'parent1_relationship' => 'nullable|string',
        ]);

        // Generate admission number first so we can use it for the student email
        $admissionNumber = Student::generateAdmissionNumber($validated['school_class_id'] ?? null);
        // Ensure admission number is unique
        $counter = 1;
        $baseNumber = $admissionNumber;
        while (UserProfile::where('admission_number', $admissionNumber)->exists()) {
            $admissionNumber = substr($baseNumber, 0, -3) . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'darul-arqam.com';
        $studentEmail = strtolower($admissionNumber) . '@' . $domain;
        // Ensure email uniqueness
        if (User::where('email', $studentEmail)->exists()) {
            $studentEmail = 'student-' . uniqid() . '@' . $domain;
        }

        // Create User first with admission-based email
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $studentEmail,
            'password' => bcrypt('password123'),
        ]);

        // Assign student role
        $user->assignRole('student');

        // Create parent user if parent email provided and doesn't exist
        $parentId = null;
        if (!empty($validated['parent1_email'])) {
            $parentUser = User::where('email', $validated['parent1_email'])->first();
            
            if (!$parentUser) {
                // Create new parent user
                $parentUser = User::create([
                    'name' => $validated['parent1_name'] ?? 'Parent User',
                    'email' => $validated['parent1_email'],
                    'password' => bcrypt('password123'),
                ]);
                // Assign parent role
                $parentUser->assignRole('parent');
                
                // Create UserProfile for parent with provided information
                UserProfile::create([
                    'user_id' => $parentUser->id,
                    'first_name' => explode(' ', $validated['parent1_name'] ?? 'Parent')[0] ?? 'Parent',
                    'last_name' => implode(' ', array_slice(explode(' ', $validated['parent1_name'] ?? 'Parent'), 1)) ?: 'User',
                    'phone' => $validated['parent1_phone'] ?? null,
                    'status' => 'active',
                ]);
            } else {
                // Update existing parent's UserProfile with provided information
                $parentProfile = $parentUser->profile;
                if ($parentProfile) {
                    $parentProfile->update([
                        'phone' => $validated['parent1_phone'] ?? $parentProfile->phone,
                    ]);
                } else {
                    // Create profile if it doesn't exist
                    UserProfile::create([
                        'user_id' => $parentUser->id,
                        'first_name' => explode(' ', $validated['parent1_name'] ?? 'Parent')[0] ?? 'Parent',
                        'last_name' => implode(' ', array_slice(explode(' ', $validated['parent1_name'] ?? 'Parent'), 1)) ?: 'User',
                        'phone' => $validated['parent1_phone'] ?? null,
                        'status' => 'active',
                    ]);
                }
            }
            
            $parentId = $parentUser->id;
        }

        // Generate admission number with class ID
        $admissionNumber = Student::generateAdmissionNumber($validated['school_class_id'] ?? null);

        // Create UserProfile with only fillable fields
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'school_class_id' => $validated['school_class_id'] ?? null,
            'admission_date' => $validated['admission_date'] ?? null,
            'admission_number' => $admissionNumber,
            'previous_school' => $validated['previous_school'] ?? null,
            'medical_conditions' => $validated['medical_conditions'] ?? null,
            'allergies' => !empty($validated['allergies']) ? json_encode($validated['allergies']) : null,
            'medications' => $validated['medications'] ?? null,
            'emergency_medical_consent' => $validated['emergency_medical_consent'] ?? false,
            'special_needs' => $validated['special_needs'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'parent_id' => $parentId,
            'relationship' => $validated['parent1_relationship'] ?? null,
        ]);

        // Keep backward compatibility - create Student record if table exists
        try {
            Student::create(array_merge($validated, [
                'admission_number' => $admissionNumber,
            ]));
        } catch (\Exception $e) {
            // Student table may not be needed
        }

        // Send enrollment notification email to parent if parent email was provided
        if (!empty($validated['parent1_email'])) {
            try {
                $userProfile = UserProfile::where('user_id', $user->id)->first();
                $emailContent = view('emails.student-enrollment', [
                    'parent' => $parentUser->profile,
                    'student' => $userProfile,
                    'admissionNumber' => $admissionNumber,
                ])->render();
                
                Mail::html($emailContent, function ($message) use ($validated) {
                    $message->to($validated['parent1_email'])
                        ->subject('Student Enrollment Confirmation - ' . config('app.name'));
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send student enrollment email to parent: ' . $e->getMessage(), ['exception' => $e]);
                // Continue with enrollment even if email fails
            }
        }

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully!' . (!empty($validated['parent1_email']) ? ' Notification email sent to parent.' : ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = UserProfile::with(['user', 'schoolClass', 'parent', 'parent.profile'])
            ->findOrFail($id);
        
        // Authorization: Check if user can view this student
        // Only restrict if user is a student AND not an admin or teacher
        $user = auth()->user();
        if ($user->isStudent() && !$user->isAdmin() && !$user->isTeacher() && $student->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this student profile');
        }

        return view('students.show', compact('student'));
    }

    /**
     * Display printable student profile
     */
    public function print(string $id)
    {
        $student = UserProfile::findOrFail($id);
        
        // Authorization
        $this->authorize('view-students');

        return view('students.print', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('edit-student')) {
            abort(403, 'Unauthorized to edit students');
        }

        $student = UserProfile::with('user', 'parent')->findOrFail($id);
        $schoolClasses = SchoolClass::active()->get();
        
        return view('students.edit', compact('student', 'schoolClasses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('edit-student')) {
            abort(403, 'Unauthorized to edit students');
        }

        $student = UserProfile::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'nationality' => 'nullable|string',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'admission_date' => 'nullable|date',
            'previous_school' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|array',
            'allergies.*' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_medical_consent' => 'nullable|boolean',
            'special_needs' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended,graduated,transferred',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('delete-student')) {
            abort(403, 'Unauthorized to delete students');
        }

        $student = UserProfile::findOrFail($id);
        $student->delete(); // Soft delete

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('view-students')) {
            abort(403, 'Unauthorized to export students');
        }

        $query = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'student');
            });
        });

        // Apply filters if provided
        if ($request->filled('class')) {
            $query->where('school_class_id', $request->class);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->get();

        $filename = 'students_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Admission Number',
                'First Name',
                'Middle Name',
                'Last Name',
                'Date of Birth',
                'Gender',
                'Blood Group',
                'Nationality',
                'Address',
                'Phone',
                'Status',
            ]);

            // Add student data
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_number,
                    $student->first_name,
                    $student->middle_name,
                    $student->last_name,
                    $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '',
                    $student->gender,
                    $student->blood_group,
                    $student->nationality,
                    $student->address,
                    $student->phone,
                    $student->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('create-student')) {
            abort(403, 'Unauthorized to import students');
        }
        return view('students.import');
    }

    /**
     * Import students from CSV
     */
    public function import(Request $request)
    {
        // Authorization: Check permission from role_permissions table
        if (!auth()->user()->hasPermission('create-student')) {
            abort(403, 'Unauthorized to import students');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $content = file_get_contents($path);
        // Remove BOM if present
        $content = preg_replace('/^[\xef\xbb\xbf]+/', '', $content);
        $lines = explode("\n", str_replace("\r", "", trim($content)));
        
        $csv = array_map('str_getcsv', $lines);
        
        // Remove header rows
        $firstRow = array_shift($csv); 
        // If the first row was the instructions row, the next row is the actual header
        if (isset($firstRow[0]) && strpos($firstRow[0], 'INSTRUCTIONS') !== false) {
            array_shift($csv);
        } else if (isset($firstRow[0]) && strpos($firstRow[0], 'Admission Number') !== false) {
            // It was just the header row, already shifted
        } else if (isset($csv[0][0]) && strpos($csv[0][0], 'Admission Number') !== false) {
            // The first row was something else, and second row is header
            array_shift($csv);
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($csv as $index => $row) {
            try {
                // Skip empty rows or rows that look like headers
                if (empty(array_filter($row)) || (isset($row[0]) && strpos($row[0], 'Admission Number') !== false)) {
                    continue;
                }

                $data = [
                    'first_name' => trim($row[1] ?? ''),
                    'last_name' => trim($row[3] ?? ''),
                    'middle_name' => trim($row[2] ?? ''),
                    'date_of_birth' => trim($row[4] ?? ''),
                    'gender' => strtolower(trim($row[5] ?? '')),
                    'blood_group' => trim($row[6] ?? ''),
                    'nationality' => trim($row[7] ?? '') ?: 'Nigerian',
                    'address' => trim($row[8] ?? ''),
                    'phone' => trim($row[10] ?? ''),
                    'school_class_id' => null, // Will be resolved below
                    'admission_date' => now()->toDateString(),
                    'status' => strtolower(trim($row[13] ?? '')) ?: 'active',
                ];

                // Validate required fields
                if (empty($data['first_name']) || empty($data['last_name'])) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (First Name or Last Name)";
                    continue;
                }

                // Resolve school class ID from column 11
                $classInput = trim($row[11] ?? '');
                if (!empty($classInput)) {
                    // Find class by ID 
                    $schoolClass = SchoolClass::where('id', $classInput)->first();
                    
                    if ($schoolClass) {
                        $data['school_class_id'] = $schoolClass->id;
                    } else {
                        $skipped++;
                        $errors[] = "Row " . ($index + 2) . ": School class with ID '{$classInput}' not found";
                        continue;
                    }
                } else {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": School class ID is required";
                    continue;
                }

                // Create admission number and email for this imported student
                $classId = $data['school_class_id'];
                $admissionNumber = Student::generateAdmissionNumber($classId);
                // Ensure admission number is unique
                $counter = 1;
                $baseNumber = $admissionNumber;
                while (UserProfile::where('admission_number', $admissionNumber)->exists()) {
                    $admissionNumber = substr($baseNumber, 0, -3) . str_pad($counter, 3, '0', STR_PAD_LEFT);
                    $counter++;
                }

                $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'darul-arqam.com';
                $studentEmail = strtolower($admissionNumber) . '@' . $domain;
                if (User::where('email', $studentEmail)->exists()) {
                    $studentEmail = 'student-' . uniqid() . '@' . $domain;
                }

                // Create User
                $user = User::create([
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $studentEmail,
                    'password' => bcrypt('password123'),
                ]);

                $user->assignRole('student');

                // Create UserProfile with the admission number
                UserProfile::create(array_merge($data, [
                    'user_id' => $user->id,
                    'admission_number' => $admissionNumber,
                ]));

                $imported++;

            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Import completed: {$imported} students imported successfully";
        if ($skipped > 0) {
            $message .= ", {$skipped} rows skipped";
        }

        return redirect()
            ->route('students.index')
            ->with('success', $message)
            ->with('warning', $skipped > 0 ? "Some rows were skipped. Check details below." : null)
            ->with('import_errors', $errors);
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'students_import_template.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Output a BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            $validClasses = SchoolClass::select('id', 'name')->get()->map(function($c) {
                return $c->name . '(ID:' . $c->id . ')';
            })->toArray();
            $classExample = SchoolClass::first() ? SchoolClass::first()->id : '1';
            
            // Add instructions row
            fputcsv($file, [
                'INSTRUCTIONS:',
                'Fill from Row 3.',
                'Do not edit Row 2 headers.',
                'Valid Class IDs: ' . implode(', ', $validClasses),
                'Dates must be YYYY-MM-DD',
                'Leave Admission Number empty'
            ]);

            // Add CSV headers
            fputcsv($file, [
                'Admission Number (Auto-generated)',
                'First Name *',
                'Middle Name',
                'Last Name *',
                'Date of Birth (YYYY-MM-DD)',
                'Gender (male/female)',
                'Blood Group',
                'Nationality',
                'Address',
                'Email',
                'Phone',
                'Class ID * (Use exact ID)',
                'Section',
                'Status (active/inactive)',
            ]);

            // Add sample data row
            fputcsv($file, [
                '', // Leave empty
                'Ahmed',
                'Hassan',
                'Ibrahim',
                '2010-01-15',
                'male',
                'O+',
                'Nigerian',
                '123 Main Street, Lagos',
                'student@example.com',
                '+234 XXX XXX XXXX',
                $classExample,
                'A',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}

