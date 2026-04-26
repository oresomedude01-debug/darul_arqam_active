@extends('layouts.spa')

@section('title', 'Events List')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">All Events</h2>
            <p class="text-muted mb-0">View and manage all calendar events</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddEventModal()">
            <i class="fas fa-plus"></i> Add Event
        </button>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('calendar.events.list') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="filterType" class="form-label">Event Type</label>
                    <select class="form-select" id="filterType" name="type">
                        <option value="">All Types</option>
                        <option value="term_start" {{ request('type') == 'term_start' ? 'selected' : '' }}>Term Start</option>
                        <option value="term_end" {{ request('type') == 'term_end' ? 'selected' : '' }}>Term End</option>
                        <option value="holiday" {{ request('type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                        <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="meeting" {{ request('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>Special Event</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterTerm" class="form-label">Academic Term</label>
                    <select class="form-select" id="filterTerm" name="term">
                        <option value="">All Terms</option>
                        @foreach ($terms ?? [] as $term)
                            <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterStartDate" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="filterStartDate" name="start_date"
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="filterEndDate" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="filterEndDate" name="end_date"
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('calendar.events.list') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Time</th>
                            <th>Term</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                        <tr>
                            <td>
                                <strong>{{ $event->title }}</strong>
                                @if ($event->description)
                                <br><small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $event->type_color }};">
                                    {{ $event->type_name }}
                                </span>
                            </td>
                            <td>{{ $event->start_date->format('M d, Y') }}</td>
                            <td>
                                @if ($event->end_date)
                                    {{ $event->end_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($event->start_time)
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                    @if ($event->end_time)
                                        - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                    @endif
                                @else
                                    <span class="text-muted">All day</span>
                                @endif
                            </td>
                            <td>
                                @if ($event->academicTerm)
                                    <small>{{ $event->academicTerm->name }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                            onclick='showEventDetails(@json($event))'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick='editEvent(@json($event))'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteEvent({{ $event->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No events found. Try adjusting your filters or add a new event.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="d-md-none">
        @forelse ($events as $event)
        <div class="card shadow-sm mb-3 event-card" onclick='showEventDetails(@json($event))'>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">{{ $event->title }}</h6>
                    <span class="badge" style="background-color: {{ $event->type_color }};">
                        {{ $event->type_name }}
                    </span>
                </div>

                @if ($event->description)
                <p class="text-muted small mb-2">{{ Str::limit($event->description, 80) }}</p>
                @endif

                <div class="event-meta small">
                    <div class="mb-1">
                        <i class="fas fa-calendar text-primary"></i>
                        {{ $event->start_date->format('M d, Y') }}
                        @if ($event->end_date && !$event->start_date->isSameDay($event->end_date))
                            - {{ $event->end_date->format('M d, Y') }}
                        @endif
                    </div>

                    @if ($event->start_time)
                    <div class="mb-1">
                        <i class="fas fa-clock text-success"></i>
                        {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                        @if ($event->end_time)
                            - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                        @endif
                    </div>
                    @endif

                    @if ($event->academicTerm)
                    <div class="mb-1">
                        <i class="fas fa-graduation-cap text-info"></i>
                        {{ $event->academicTerm->name }}
                    </div>
                    @endif
                </div>

                <hr class="my-2">

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick='event.stopPropagation(); editEvent(@json($event))'>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="event.stopPropagation(); deleteEvent({{ $event->id }})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No events found. Try adjusting your filters or add a new event.
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($events->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $events->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav d-lg-none">
    <a href="{{ route('calendar.index') }}" class="nav-item">
        <i class="fas fa-calendar"></i>
        <span>Calendar</span>
    </a>
    <a href="{{ route('calendar.events.list') }}" class="nav-item active">
        <i class="fas fa-list"></i>
        <span>Events</span>
    </a>
    <a href="{{ route('calendar.terms') }}" class="nav-item">
        <i class="fas fa-graduation-cap"></i>
        <span>Terms</span>
    </a>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventDetailsBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div class="modal fade" id="eventFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="eventForm" onsubmit="submitEventForm(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventFormTitle">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId" name="id">

                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="eventTitle" name="title" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventType" class="form-label">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="eventType" name="type" required>
                                <option value="special">Special Event</option>
                                <option value="term_start">Term Start</option>
                                <option value="term_end">Term End</option>
                                <option value="holiday">Holiday</option>
                                <option value="exam">Exam</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventColor" class="form-label">Custom Color (Optional)</label>
                            <input type="color" class="form-control form-control-color" id="eventColor" name="color">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventStartDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="eventStartDate" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="eventEndDate" name="end_date">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventStartTime" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="eventStartTime" name="start_time">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventEndTime" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="eventEndTime" name="end_time">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="eventAcademicTerm" class="form-label">Academic Term</label>
                        <select class="form-select" id="eventAcademicTerm" name="academic_term_id">
                            <option value="">-- Select Term --</option>
                            @foreach ($terms ?? [] as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitEventBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                        Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .event-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }

    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .event-meta {
        color: #6c757d;
    }

    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: white;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: space-around;
        padding: 8px 0;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }

    .mobile-bottom-nav .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #6c757d;
        font-size: 0.75rem;
        padding: 4px 12px;
        transition: color 0.2s;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 1.25rem;
        margin-bottom: 2px;
    }

    .mobile-bottom-nav .nav-item.active {
        color: #007bff;
    }

    @media (max-width: 768px) {
        body {
            padding-bottom: 60px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentEventId = null;

    function showEventDetails(event) {
        const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
        currentEventId = event.id;

        const startDate = new Date(event.start_date);
        const endDate = event.end_date ? new Date(event.end_date) : null;

        let dateDisplay = startDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        if (endDate && endDate.getTime() !== startDate.getTime()) {
            dateDisplay += ' - ' + endDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        let timeDisplay = '';
        if (event.start_time) {
            timeDisplay = '<div class="mb-2"><strong>Time:</strong> ' + event.start_time;
            if (event.end_time) {
                timeDisplay += ' - ' + event.end_time;
            }
            timeDisplay += '</div>';
        }

        const html = `
            <div class="mb-3">
                <span class="badge" style="background-color: ${event.type_color};">${event.type_name}</span>
            </div>
            <div class="mb-2">
                <strong>Date:</strong> ${dateDisplay}
            </div>
            ${timeDisplay}
            ${event.description ? '<div class="mb-2"><strong>Description:</strong><br>' + event.description + '</div>' : ''}
            ${event.academic_term ? '<div class="mb-2"><strong>Term:</strong> ' + event.academic_term.name + '</div>' : ''}
        `;

        document.getElementById('eventDetailsTitle').textContent = event.title;
        document.getElementById('eventDetailsBody').innerHTML = html;
        modal.show();
    }

    function openAddEventModal() {
        currentEventId = null;
        document.getElementById('eventForm').reset();
        document.getElementById('eventFormTitle').textContent = 'Add Event';
        document.getElementById('eventId').value = '';

        const modal = new bootstrap.Modal(document.getElementById('eventFormModal'));
        modal.show();
    }

    function editEvent(event) {
        currentEventId = event.id;
        document.getElementById('eventFormTitle').textContent = 'Edit Event';
        document.getElementById('eventId').value = event.id;
        document.getElementById('eventTitle').value = event.title;
        document.getElementById('eventType').value = event.type;
        document.getElementById('eventStartDate').value = event.start_date;
        document.getElementById('eventEndDate').value = event.end_date || '';
        document.getElementById('eventStartTime').value = event.start_time || '';
        document.getElementById('eventEndTime').value = event.end_time || '';
        document.getElementById('eventDescription').value = event.description || '';
        document.getElementById('eventColor').value = event.color || '#6b7280';
        document.getElementById('eventAcademicTerm').value = event.academic_term_id || '';

        const modal = new bootstrap.Modal(document.getElementById('eventFormModal'));
        modal.show();
    }

    function submitEventForm(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitEventBtn');
        const spinner = document.getElementById('submitSpinner');
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData(e.target);
        const eventId = document.getElementById('eventId').value;
        const url = eventId ? `/calendar/events/${eventId}` : '/calendar/events';
        const method = eventId ? 'PUT' : 'POST';

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the event');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function deleteEvent(eventId) {
        if (!confirm('Are you sure you want to delete this event?')) {
            return;
        }

        fetch(`/calendar/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the event');
        });
    }
</script>
@endpush
