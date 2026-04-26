<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Attendance;
use App\Models\AcademicSession;
use App\Models\TimeTable;
use App\Models\AttendanceComplaint;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->profile;
        
        // Check if student profile exists
        if (!$student) {
            abort(403, 'Student profile not found. Please contact administration.');
        }
        
        $currentSession = AcademicSession::where('is_active', true)->first();
        
        $averageScore = Result::where('student_id', $student->id)
            ->where('academic_session_id', $currentSession?->id)
            ->avg('total_score');

        $passCount = Result::where('student_id', $student->id)
            ->where('academic_session_id', $currentSession?->id)
            ->where('total_score', '>=', 50)
            ->count();

        $failedCount = Result::where('student_id', $student->id)
            ->where('academic_session_id', $currentSession?->id)
            ->where('total_score', '<', 50)
            ->count();

        $recentResults = Result::where('student_id', $student->id)
            ->where('academic_session_id', $currentSession?->id)
            ->with('subject', 'academicSession', 'academicTerm')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($result) {
                return [
                    'subject' => $result->subject->name ?? 'N/A',
                    'score' => $result->total_score,
                    'grade' => $this->calculateGrade($result->total_score),
                    'term' => $result->academicTerm?->name ?? 'N/A',
                    'date' => $result->created_at->format('M d, Y'),
                ];
            });

        // Calculate attendance percentage
        $totalAttendanceDays = Attendance::where('user_profile_id', $student->id)->count();
        $presentDays = Attendance::where('user_profile_id', $student->id)
            ->where('status', 'present')
            ->count();
        $attendancePercentage = $totalAttendanceDays > 0 ? round(($presentDays / $totalAttendanceDays) * 100) : 0;

        $stats = [
            ['icon' => 'fa-door-open', 'label' => 'Class', 'value' => $student->schoolClass?->name ?? 'N/A', 'color' => 'indigo'],
            ['icon' => 'fa-book', 'label' => 'Subjects', 'value' => Result::where('student_id', $student->id)->distinct('subject_id')->count(), 'color' => 'purple'],
            ['icon' => 'fa-chart-line', 'label' => 'Average Score', 'value' => round($averageScore ?? 0, 1), 'color' => 'pink'],
            ['icon' => 'fa-check', 'label' => 'Passed', 'value' => $passCount, 'color' => 'green'],
            ['icon' => 'fa-times', 'label' => 'Failed', 'value' => $failedCount, 'color' => 'red'],
        ];

        $dashboardData = [
            'stats' => $stats,
            'recentResults' => $recentResults,
            'passedCount' => $passCount,
            'failedCount' => $failedCount,
            'averageScore' => $averageScore,
            'attendancePercentage' => $attendancePercentage,
        ];

        return view('student-portal.dashboard', compact('dashboardData', 'student', 'user'));
    }

    public function timetable()
    {
        $user = Auth::user();
        $student = $user->profile;

        // Check if student has a class assigned
        if (!$student->school_class_id) {
            return view('student-portal.timetable', [
                'timetable' => [],
                'student' => $student,
            ]);
        }

        $timetables = TimeTable::where('school_class_id', $student->school_class_id)
            ->with('subject', 'teacher')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Group timetables by day of week (day_of_week is stored as string: 'Monday', 'Tuesday', etc.)
        $timetable = [];
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
            $timetable[$day] = $timetables->filter(function ($t) use ($day) {
                return strtolower($t->day_of_week) === strtolower($day);
            })->values();
        }

        return view('student-portal.timetable', compact('timetable', 'student'));
    }

    public function calendar()
    {
        $currentSession = AcademicSession::where('is_active', true)->first();
        
        // Get events for active academic terms in the current session
        $events = \App\Models\Event::whereHas('academicTerm', function ($query) use ($currentSession) {
            $query->where('academic_session_id', $currentSession?->id);
        })->orderBy('start_date')->get();

        return view('student-portal.calendar', compact('events', 'currentSession'));
    }

    public function attendance()
    {
        $user = Auth::user();
        $student = $user->profile;

        $attendance = Attendance::where('user_profile_id', $student->id)
            ->with('academicTerm')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $attendancePercentage = $student->attendancePercentage();

        // Calculate session stats
        $sessionStats = [
            'present' => Attendance::where('user_profile_id', $student->id)->where('status', 'present')->count(),
            'absent' => Attendance::where('user_profile_id', $student->id)->where('status', 'absent')->count(),
            'leave' => Attendance::where('user_profile_id', $student->id)->where('status', 'excused')->count(),
            'late' => Attendance::where('user_profile_id', $student->id)->where('status', 'late')->count(),
        ];

        return view('student-portal.attendance', compact('attendance', 'attendancePercentage', 'sessionStats'));
    }

    public function results(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $student = $user->profile;

        $query = Result::where('student_id', $student->id)
            ->with('subject', 'academicSession', 'academicTerm');

        // Filter by session
        $selectedSession = null;
        if ($request->has('session') && $request->session) {
            $query->where('academic_session_id', $request->session);
            $selectedSession = $request->session;
        }

        // Filter by term
        $selectedTerm = null;
        if ($request->has('term') && $request->term) {
            $query->where('academic_term_id', $request->term);
            $selectedTerm = $request->term;
        }

        $results = $query->paginate(20);

        $sessions = AcademicSession::all();
        $terms = \App\Models\AcademicTerm::all();

        return view('student-portal.results', compact('results', 'sessions', 'terms', 'selectedSession', 'selectedTerm'));
    }

    public function profile()
    {
        $user = Auth::user();
        $student = $user->profile;

        return view('student-portal.profile', compact('student', 'user'));
    }

    public function updateProfile()
    {
        $user = Auth::user();
        $student = $user->profile;

        // Update logic here

        return redirect()->route('student-portal.profile')->with('success', 'Profile updated successfully');
    }

    public function fileAttendanceComplaint()
    {
        $user = Auth::user();
        $student = $user->profile;

        $recentAbsences = Attendance::where('user_profile_id', $student->id)
            ->where('status', 'absent')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('student-portal.attendance-complaint', compact('recentAbsences'));
    }

    public function storeAttendanceComplaint(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $student = $user->profile;

        $validated = $request->validate([
            'attendance_id' => 'required|exists:attendance,id',
            'reason' => 'required|string|max:500',
        ]);

        AttendanceComplaint::create([
            'attendance_id' => $validated['attendance_id'],
            'student_id' => $student->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('student-portal.attendance')
            ->with('success', 'Attendance complaint filed successfully');
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('student-portal.notifications', compact('notifications'));
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        if ($score >= 50) return 'E';
        return 'F';
    }
}
