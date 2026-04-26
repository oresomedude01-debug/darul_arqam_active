<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display the calendar with events
     */
    public function index(Request $request)
    {
        // Auto-acknowledge term beginning and end dates
        $this->autoAcknowledgeTermDates();
        
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Validate month and year
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2020 || $year > 2100) {
            $year = now()->year;
        }
        
        // Get date range for the month
        $firstDay = Carbon::create($year, $month, 1);
        $startDate = $firstDay->copy()->startOfMonth();
        $endDate = $firstDay->copy()->endOfMonth();
        
        // Get events for the month
        $events = Event::inDateRange($startDate, $endDate)
            ->orderBy('start_date')
            ->get();
        
        // Get upcoming events (next 7 days)
        $upcomingEvents = Event::where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->limit(5)
            ->get();
        
        // Calculate navigation dates
        $prevDate = $firstDay->copy()->subMonth();
        $nextDate = $firstDay->copy()->addMonth();
        
        // Get data for view
        $academicTerms = AcademicTerm::orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->orderBy('name')->get();
        
        // Build calendar grid
        $calendar = $this->buildCalendar($year, $month, $events);
        
        return view('calendar.index', compact(
            'calendar',
            'events',
            'upcomingEvents',
            'year',
            'month',
            'prevDate',
            'nextDate',
            'academicTerms',
            'classes'
        ));
    }

    /**
     * Show create event form
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('create-event')) {
            abort(403, 'Unauthorized');
        }

        $academicTerms = AcademicTerm::orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->orderBy('name')->get();
        
        return view('calendar.create', compact('academicTerms', 'classes'));
    }

    /**
     * Store a new event
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create-event')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:holiday,exam,break,meeting,celebration,term_begin,term_end,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'affected_classes' => 'nullable|array',
            'affected_classes.*' => 'integer|exists:school_classes,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
        ]);

        $validated['created_by'] = auth()->id();

        Event::create($validated);

        return redirect()->route('calendar.index')
            ->with('success', 'Event created successfully!');
    }

    /**
     * Show edit event form
     */
    public function edit(Event $event)
    {
        if (!auth()->user()->hasPermission('edit-event')) {
            abort(403, 'Unauthorized');
        }

        $academicTerms = AcademicTerm::orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->orderBy('name')->get();
        
        return view('calendar.edit', compact('event', 'academicTerms', 'classes'));
    }

    /**
     * Update an event
     */
    public function update(Request $request, Event $event)
    {
        if (!auth()->user()->hasPermission('edit-event')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:holiday,exam,break,meeting,celebration,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'affected_classes' => 'nullable|array',
            'affected_classes.*' => 'integer|exists:school_classes,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
        ]);

        $event->update($validated);

        return redirect()->route('calendar.index')
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Delete an event
     */
    public function destroy(Event $event)
    {
        if (!auth()->user()->hasPermission('delete-event')) {
            abort(403, 'Unauthorized');
        }

        $event->delete();

        return redirect()->route('calendar.index')
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Build calendar grid for the month
     */
    private function buildCalendar($year, $month, $events)
    {
        $firstDay = Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startingDayOfWeek = $firstDay->dayOfWeek; // 0 = Sunday, 6 = Saturday

        $calendar = [];
        $day = 1;

        // Fill weeks with days
        for ($week = 0; $week < 6; $week++) {
            $weekDays = [];
            
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                if ($week === 0 && $dayOfWeek < $startingDayOfWeek) {
                    // Empty cells before the month starts
                    $weekDays[] = ['date' => null, 'events' => []];
                } elseif ($day > $daysInMonth) {
                    // Empty cells after the month ends
                    $weekDays[] = ['date' => null, 'events' => []];
                } else {
                    $date = Carbon::create($year, $month, $day);
                    $dayEvents = $events->filter(function ($event) use ($date) {
                        return $event->start_date->lte($date) && $event->end_date->gte($date);
                    })->values();
                    
                    $weekDays[] = [
                        'date' => $date,
                        'events' => $dayEvents,
                    ];
                    $day++;
                }
            }
            
            $calendar[] = $weekDays;
            
            if ($day > $daysInMonth) {
                break;
            }
        }

        return $calendar;
    }

    /**
     * Auto-acknowledge term beginning and ending dates
     * Creates calendar events for all academic terms
     */
    private function autoAcknowledgeTermDates()
    {
        $terms = AcademicTerm::all();

        foreach ($terms as $term) {
            // Create event for term beginning
            $beginningEventExists = Event::where('academic_term_id', $term->id)
                ->whereIn('type', ['term_begin'])
                ->where('start_date', $term->start_date)
                ->exists();

            if (!$beginningEventExists) {
                Event::create([
                    'title' => "Term Begins: {$term->name}",
                    'type' => 'term_begin',
                    'start_date' => $term->start_date,
                    'end_date' => $term->start_date,
                    'description' => "Academic term '{$term->name}' officially begins",
                    'color' => '#10b981', // Green
                    'academic_term_id' => $term->id,
                    'is_auto_acknowledged' => true,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            // Create event for term ending
            $endingEventExists = Event::where('academic_term_id', $term->id)
                ->whereIn('type', ['term_end'])
                ->where('start_date', $term->end_date)
                ->exists();

            if (!$endingEventExists) {
                Event::create([
                    'title' => "Term Ends: {$term->name}",
                    'type' => 'term_end',
                    'start_date' => $term->end_date,
                    'end_date' => $term->end_date,
                    'description' => "Academic term '{$term->name}' officially ends",
                    'color' => '#dc2626', // Dark Red
                    'academic_term_id' => $term->id,
                    'is_auto_acknowledged' => true,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }
    }
}
