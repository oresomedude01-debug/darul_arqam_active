<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\UserProfile;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Show all timetables for admin view
     */
    public function view()
    {
        $classes = SchoolClass::with([
            'timetables' => function($query) {
                $query->orderBy('period_number')
                      ->orderBy('day_of_week');
            },
            'timetables.subject',
            'timetables.teacher',
            'teacher'
        ])->orderBy('name')->get();

        return view('timetable.index', compact('classes'));
    }

    /**
     * Show the timetable management page for a class
     */
    public function index(SchoolClass $class)
    {
        $class->load([
            'timetables' => function($query) {
                $query->orderBy('period_number')
                      ->orderBy('day_of_week');
            },
            'timetables.subject',
            'timetables.teacher',
            'subjects'
        ]);

        // Get available subjects for this class
        $subjects = $class->subjects;

        // Get all active teachers (UserProfiles with teacher role)
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->where('status', 'active')->orderBy('first_name')->get();

        // Get school operating days
        $schoolSettings = SchoolSetting::first();
        $operatingDays = $schoolSettings?->getOperatingDays() ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Days of the week (lowercase for database storage)
        $days = array_map('strtolower', $operatingDays);

        return view('classes.timetable.index', compact('class', 'subjects', 'teachers', 'days', 'operatingDays'));
    }

    /**
     * Store a new timetable entry
     */
    public function store(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:user_profiles,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'period_number' => 'required|integer|min:1',
            'type' => 'required|in:class,break,lunch,assembly',
            'room_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Check if the day is a school operating day
        $schoolSettings = SchoolSetting::first();
        $dayName = ucfirst($validated['day_of_week']);
        $operatingDaysString = $schoolSettings?->getOperatingDaysString() ?? 'Monday-Friday';
        if (!$schoolSettings || !$schoolSettings->isOperatingDay($dayName)) {
            return redirect()
                ->route('classes.timetable.index', $class)
                ->with('error', "❌ Invalid Day: Timetable entries can only be created for school operating days ({$operatingDaysString}).");
        }

        // Check for overlapping periods in the same class
        $classConflict = $class->timetables()
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function($query) use ($validated) {
                // Check for time overlap
                $query->where(function($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->first();

        if ($classConflict) {
            $timeSlot = $classConflict->start_time . ' - ' . $classConflict->end_time;
            return redirect()
                ->route('classes.timetable.index', $class)
                ->with('error', "❌ Class Conflict: A period already exists in {$class->name} from {$timeSlot} on {$dayName}. Please select a different time slot.");
        }

        // Check for teacher scheduling conflicts (teacher cannot teach in multiple classes at the same time)
        if ($validated['teacher_id'] && $validated['type'] === 'class') {
            $teacherConflict = Timetable::where('teacher_id', $validated['teacher_id'])
                ->where('school_class_id', '!=', $class->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where('type', 'class')
                ->where(function($query) use ($validated) {
                    // Check if times overlap
                    $query->where('start_time', '<', $validated['end_time'])
                          ->where('end_time', '>', $validated['start_time']);
                })
                ->with('schoolClass', 'teacher')
                ->first();

            if ($teacherConflict) {
                $teacher = $validated['teacher_id'] ? \App\Models\UserProfile::find($validated['teacher_id']) : null;
                $conflictClass = $teacherConflict->schoolClass;
                $conflictTime = $teacherConflict->start_time . ' - ' . $teacherConflict->end_time;
                
                return redirect()
                    ->route('classes.timetable.index', $class)
                    ->with('error', "❌ Teacher Conflict: {$teacher->full_name} is already assigned to {$conflictClass->name} from {$conflictTime} on {$dayName}. Please assign a different teacher or change the time.");
            }
        }

        // All checks passed, create the entry
        $timetable = Timetable::create($validated + ['school_class_id' => $class->id]);

        return redirect()
            ->route('classes.timetable.index', $class)
            ->with('success', "✅ Success: Period created successfully for {$class->name} on {$dayName} from {$validated['start_time']} to {$validated['end_time']}!");
    }

    /**
     * Update a timetable entry
     */
    public function update(Request $request, SchoolClass $class, Timetable $timetable)
    {
        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:user_profiles,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'period_number' => 'required|integer|min:1',
            'type' => 'required|in:class,break,lunch,assembly',
            'room_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Check if the day is a school operating day
        $schoolSettings = SchoolSetting::first();
        $dayName = ucfirst($validated['day_of_week']);
        $operatingDaysString = $schoolSettings?->getOperatingDaysString() ?? 'Monday-Friday';
        if (!$schoolSettings || !$schoolSettings->isOperatingDay($dayName)) {
            return redirect()
                ->route('classes.timetable.index', $class)
                ->with('error', "❌ Invalid Day: Cannot update for non-operating days. Operating days: {$operatingDaysString}");
        }

        // Check for overlapping periods (excluding current entry)
        $classConflict = $class->timetables()
            ->where('id', '!=', $timetable->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function($query) use ($validated) {
                // Check for time overlap
                $query->where(function($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->first();

        if ($classConflict) {
            $timeSlot = $classConflict->start_time . ' - ' . $classConflict->end_time;
            return redirect()
                ->route('classes.timetable.index', $class)
                ->with('error', "❌ Class Conflict: Another period exists in {$class->name} from {$timeSlot} on {$dayName}.");
        }

        // Check for teacher scheduling conflicts (excluding current entry)
        if ($validated['teacher_id'] && $validated['type'] === 'class') {
            $teacherConflict = Timetable::where('teacher_id', $validated['teacher_id'])
                ->where('id', '!=', $timetable->id)
                ->where('school_class_id', '!=', $class->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where('type', 'class')
                ->where(function($query) use ($validated) {
                    // Check if times overlap
                    $query->where('start_time', '<', $validated['end_time'])
                          ->where('end_time', '>', $validated['start_time']);
                })
                ->with('schoolClass', 'teacher')
                ->first();

            if ($teacherConflict) {
                $teacher = $validated['teacher_id'] ? \App\Models\UserProfile::find($validated['teacher_id']) : null;
                $conflictClass = $teacherConflict->schoolClass;
                $conflictTime = $teacherConflict->start_time . ' - ' . $teacherConflict->end_time;
                
                return redirect()
                    ->route('classes.timetable.index', $class)
                    ->with('error', "❌ Teacher Conflict: {$teacher->full_name} is already assigned to {$conflictClass->name} from {$conflictTime} on {$dayName}.");
            }
        }

        $timetable->update($validated);

        return redirect()
            ->route('classes.timetable.index', $class)
            ->with('success', "✅ Success: Period updated successfully for {$class->name} on {$dayName} from {$validated['start_time']} to {$validated['end_time']}!");
    }

    /**
     * Delete a timetable entry
     */
    public function destroy(SchoolClass $class, Timetable $timetable)
    {
        $timetable->delete();

        return redirect()
            ->route('classes.timetable.index', $class)
            ->with('success', 'Timetable entry deleted successfully!');
    }

    /**
     * Bulk create timetable entries
     */
    public function bulkStore(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.subject_id' => 'nullable|exists:subjects,id',
            'entries.*.teacher_id' => 'nullable|exists:user_profiles,id',
            'entries.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'entries.*.start_time' => 'required|date_format:H:i',
            'entries.*.end_time' => 'required|date_format:H:i',
            'entries.*.period_number' => 'required|integer|min:1',
            'entries.*.type' => 'required|in:class,break,lunch,assembly',
        ]);

        $created = 0;
        foreach ($validated['entries'] as $entry) {
            // Check for duplicates
            $existing = $class->timetables()
                ->where('day_of_week', $entry['day_of_week'])
                ->where('start_time', $entry['start_time'])
                ->exists();

            if (!$existing) {
                $entry['school_class_id'] = $class->id;
                Timetable::create($entry);
                $created++;
            }
        }

        return redirect()
            ->route('classes.timetable.index', $class)
            ->with('success', "$created timetable entries created successfully!");
    }
}
