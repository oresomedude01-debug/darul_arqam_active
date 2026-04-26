<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    /**
     * Show the form for managing subject assignments for a class
     */
    public function index(SchoolClass $class)
    {
        $class->load('subjects');

        // Get all active subjects
        $availableSubjects = Subject::active()
            ->whereNotIn('id', $class->subjects->pluck('id'))
            ->orderBy('name')
            ->get();

        // Get all active teachers (UserProfiles with teacher role)
        $teachers = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'teacher');
            });
        })->where('status', 'active')->orderBy('first_name')->get();

        return view('classes.subjects.index', compact('class', 'availableSubjects', 'teachers'));
    }

    /**
     * Attach a subject to a class
     */
    public function store(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:user_profiles,id',
            'periods_per_week' => 'required|integer|min:1|max:20',
        ]);

        // Check if subject is already assigned
        if ($class->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return redirect()
                ->route('classes.subjects.index', $class)
                ->with('error', 'This subject is already assigned to this class.');
        }

        // Attach the subject with pivot data
        $class->subjects()->attach($validated['subject_id'], [
            'teacher_id' => $validated['teacher_id'],
            'periods_per_week' => $validated['periods_per_week'],
        ]);

        $subject = Subject::find($validated['subject_id']);

        return redirect()
            ->route('classes.subjects.index', $class)
            ->with('success', $subject->name . ' has been assigned to ' . $class->full_name);
    }

    /**
     * Update a subject assignment
     */
    public function update(Request $request, SchoolClass $class, Subject $subject)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:user_profiles,id',
            'periods_per_week' => 'required|integer|min:1|max:20',
        ]);

        // Update the pivot data
        $class->subjects()->updateExistingPivot($subject->id, [
            'teacher_id' => $validated['teacher_id'],
            'periods_per_week' => $validated['periods_per_week'],
        ]);

        return redirect()
            ->route('classes.subjects.index', $class)
            ->with('success', 'Subject assignment updated successfully!');
    }

    /**
     * Remove a subject from a class
     */
    public function destroy(SchoolClass $class, Subject $subject)
    {
        $class->subjects()->detach($subject->id);

        return redirect()
            ->route('classes.subjects.index', $class)
            ->with('success', $subject->name . ' has been removed from ' . $class->full_name);
    }
}
