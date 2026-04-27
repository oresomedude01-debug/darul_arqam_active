<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Mail\TeacherWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers
     */
    public function index(Request $request)
    {
        $query = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        });

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get teachers with pagination
        $perPage = $request->get('per_page', 15);
        $teachers = $query->with(['user', 'schoolClass'])
            ->latest('created_at')
            ->paginate($perPage);

        // Calculate statistics
        $stats = [
            'total' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'teacher');
                });
            })->count(),
            'active' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'teacher');
                });
            })->where('status', 'active')->count(),
            'inactive' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'teacher');
                });
            })->where('status', 'inactive')->count(),
            'male' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'teacher');
                });
            })->where('gender', 'male')->count(),
            'female' => UserProfile::whereHas('user', function($q) {
                $q->whereHas('roles', function($r) {
                    $r->where('slug', 'teacher');
                });
            })->where('gender', 'female')->count(),
        ];

        return view('teachers.index', compact('teachers', 'stats'));
    }

    /**
     * Show the form for creating a new teacher
     */
    public function create()
    {
        $allSubjects = Subject::active()->orderBy('name')->pluck('name')->toArray();
        $allClasses = SchoolClass::active()->orderBy('name')->get()->map(function($class) {
            return $class->name . ' - ' . $class->class_code;
        })->toArray();
        
        return view('teachers.create', compact('allSubjects', 'allClasses'));
    }

    /**
     * Store a newly created teacher in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'qualification' => 'required|string',
            'subjects' => 'array',
            'classes' => 'array',
            'date_joined' => 'required|date',
            'profile_picture' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Create user with teacher role
        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => "{$validated['first_name']} {$validated['last_name']}",
        ]);

        // Attach teacher role
        $teacherRole = \App\Models\Role::where('slug', 'teacher')->first();
        if ($teacherRole) {
            $user->roles()->attach($teacherRole->id);
        }

        // Handle profile picture upload
        $profilePicture = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('teachers', 'public');
        }

        // Create user profile with teacher details
        $userProfile = UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'country' => $validated['country'],
            'qualification' => $validated['qualification'],
            'subjects' => !empty($validated['subjects']) ? $validated['subjects'] : [],
            'classes' => !empty($validated['classes']) ? $validated['classes'] : [],
            'date_joined' => $validated['date_joined'],
            'profile_picture' => $profilePicture,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Assign classes to teacher (same as class edit logic)
        if (!empty($validated['classes'])) {
            foreach ($validated['classes'] as $className) {
                // Extract the class name from "Name - Code" format
                $classNameOnly = explode(' - ', $className)[0] ?? $className;
                
                // Find the class and update its teacher_id
                SchoolClass::where('name', 'like', $classNameOnly)
                    ->update(['teacher_id' => $user->id]);
            }
        }

        // Send welcome email to teacher
        try {
            $emailContent = view('emails.teacher-welcome', [
                'teacher' => $userProfile,
                'teacherEmail' => $user->email,
                'temporaryPassword' => $validated['password'],
            ])->render();
            
            Mail::html($emailContent, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Welcome to ' . config('app.name') . ' - Teacher Account Created');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send teacher welcome email: ' . $e->getMessage(), ['exception' => $e]);
            // Continue with teacher creation even if email fails
        }

        return redirect()->route('teachers.index')
            ->with('success', __('Teacher created successfully. Welcome email has been sent.'));
    }

    /**
     * Display the specified teacher
     */
    public function show($id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        return view('teachers.show', compact('userProfile'));
    }

    /**
     * Show the form for editing the specified teacher
     */
    public function edit($id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        $subjects = Subject::active()->orderBy('name')->get();
        $classes = SchoolClass::active()->orderBy('name')->get();
        
        return view('teachers.edit', compact('userProfile', 'subjects', 'classes'));
    }

    /**
     * Update the specified teacher in storage
     */
    public function update(Request $request, $id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $userProfile->user_id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'qualification' => 'required|string',
            'subjects' => 'array',
            'classes' => 'array',
            'date_joined' => 'required|date',
            'profile_picture' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Update user
        $userProfile->user->update([
            'email' => $validated['email'],
            'name' => "{$validated['first_name']} {$validated['last_name']}",
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($userProfile->profile_picture) {
                Storage::disk('public')->delete($userProfile->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('teachers', 'public');
        }

        // Update user profile
        $userProfile->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'country' => $validated['country'],
            'qualification' => $validated['qualification'],
            'subjects' => $validated['subjects'] ?? [],
            'classes' => $validated['classes'] ?? [],
            'date_joined' => $validated['date_joined'],
            'profile_picture' => $validated['profile_picture'] ?? $userProfile->profile_picture,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', __('Teacher updated successfully.'));
    }

    /**
     * Remove the specified teacher from storage
     */
    public function destroy($id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        // Delete profile picture if exists
        if ($userProfile->profile_picture) {
            Storage::disk('public')->delete($userProfile->profile_picture);
        }

        // Delete user profile
        $userProfile->delete();

        // Delete user
        $userProfile->user->delete();

        return redirect()->route('teachers.index')
            ->with('success', __('Teacher deleted successfully.'));
    }

    /**
     * Assign subjects and classes to a teacher
     */
    public function assign($id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        $subjectsData = Subject::active()->orderBy('name')->get();
        $classesData = SchoolClass::active()->orderBy('name')->get();
        
        // Format classes as "name - class_code" for display
        $allClasses = $classesData->map(function($class) {
            return $class->name . ' - ' . $class->class_code;
        })->toArray();
        
        $allSubjects = $subjectsData->pluck('name')->toArray();
        
        $assignedSubjects = is_array($userProfile->subjects) ? $userProfile->subjects : [];
        $assignedClasses = is_array($userProfile->classes) ? $userProfile->classes : [];
        
        return view('teachers.assign', compact(
            'userProfile',
            'allSubjects',
            'allClasses',
            'assignedSubjects',
            'assignedClasses'
        ));
    }

    /**
     * Update teacher assignments
     */
    public function updateAssignments(Request $request, $id)
    {
        $userProfile = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->findOrFail($id);

        $validated = $request->validate([
            'subjects' => 'array',
            'classes' => 'array',
        ]);

        $userProfile->update([
            'subjects' => !empty($validated['subjects']) ? $validated['subjects'] : [],
            'classes' => !empty($validated['classes']) ? $validated['classes'] : [],
        ]);

        return redirect()->route('teachers.index')
            ->with('success', __('Teacher assignments updated successfully.'));
    }

    /**
     * Export teachers to CSV
     */
    public function exportCsv()
    {
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->with('user')->get();
        
        $filename = 'teachers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];
        
        $callback = function() use ($teachers) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Gender',
                'Date of Birth',
                'Address',
                'City',
                'State',
                'Country',
                'Qualification',
                'Date Joined',
                'Status',
            ]);
            
            // Data rows
            foreach ($teachers as $teacher) {
                fputcsv($file, [
                    $teacher->first_name,
                    $teacher->last_name,
                    $teacher->user->email,
                    $teacher->phone,
                    ucfirst($teacher->gender),
                    $teacher->date_of_birth?->format('Y-m-d'),
                    $teacher->address,
                    $teacher->city,
                    $teacher->state,
                    $teacher->country,
                    $teacher->qualification,
                    $teacher->date_joined?->format('Y-m-d'),
                    ucfirst($teacher->status),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

