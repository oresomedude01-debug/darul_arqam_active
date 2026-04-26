@extends('layouts.spa')

@section('title', 'Academic Terms')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Academic Terms Management</h2>
            <p class="text-muted mb-0">Manage school academic terms and sessions</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddTermModal()">
            <i class="fas fa-plus"></i> Add New Term
        </button>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('calendar.terms') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="filterSession" class="form-label">Session</label>
                    <input type="text" class="form-control" id="filterSession" name="session"
                           value="{{ request('session') }}" placeholder="e.g., 2024/2025">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Status</label>
                    <select class="form-select" id="filterStatus" name="status">
                        <option value="">All Statuses</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('calendar.terms') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Terms Grid -->
    <div class="row">
        @forelse ($terms as $term)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100 term-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $term->name }}</h5>
                            <p class="text-muted mb-0 small">{{ $term->session }}</p>
                        </div>
                        <span class="badge badge-{{ $term->status_badge }}">{{ ucfirst($term->status) }}</span>
                    </div>

                    <div class="term-info">
                        <div class="info-item mb-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span class="ms-2">{{ $term->term }}</span>
                        </div>
                        <div class="info-item mb-2">
                            <i class="fas fa-clock text-success"></i>
                            <span class="ms-2">{{ $term->start_date->format('M d, Y') }} - {{ $term->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="info-item mb-2">
                            <i class="fas fa-hourglass-half text-warning"></i>
                            <span class="ms-2">{{ $term->duration_days }} days</span>
                        </div>
                        @if ($term->description)
                        <div class="info-item">
                            <i class="fas fa-info-circle text-info"></i>
                            <span class="ms-2 small">{{ Str::limit($term->description, 80) }}</span>
                        </div>
                        @endif
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar-check"></i>
                            {{ $term->events_count ?? 0 }} events
                        </small>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick='editTerm(@json($term))'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTerm({{ $term->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No academic terms found. Click "Add New Term" to create one.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($terms->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $terms->links() }}
    </div>
    @endif
</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav d-lg-none">
    <a href="{{ route('calendar.index') }}" class="nav-item">
        <i class="fas fa-calendar"></i>
        <span>Calendar</span>
    </a>
    <a href="{{ route('calendar.events.list') }}" class="nav-item">
        <i class="fas fa-list"></i>
        <span>Events</span>
    </a>
    <a href="{{ route('calendar.terms') }}" class="nav-item active">
        <i class="fas fa-graduation-cap"></i>
        <span>Terms</span>
    </a>
</div>

<!-- Add/Edit Term Modal -->
<div class="modal fade" id="termFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="termForm" onsubmit="submitTermForm(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="termFormTitle">Add Academic Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="termId" name="id">

                    <div class="mb-3">
                        <label for="termName" class="form-label">Term Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="termName" name="name"
                               placeholder="e.g., First Term 2024/2025" required>
                        <small class="text-muted">A descriptive name for the term</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="termSession" class="form-label">Academic Session <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="termSession" name="session"
                                   placeholder="e.g., 2024/2025" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="termTerm" class="form-label">Term <span class="text-danger">*</span></label>
                            <select class="form-select" id="termTerm" name="term" required>
                                <option value="">-- Select Term --</option>
                                <option value="First Term">First Term</option>
                                <option value="Second Term">Second Term</option>
                                <option value="Third Term">Third Term</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="termStartDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="termStartDate" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="termEndDate" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="termEndDate" name="end_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="termStatus" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="termStatus" name="status" required>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                        <small class="text-muted">Set the current status of this term</small>
                    </div>

                    <div class="mb-3">
                        <label for="termDescription" class="form-label">Description / Notes</label>
                        <textarea class="form-control" id="termDescription" name="description" rows="3"
                                  placeholder="Any additional notes about this term"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Each session can only have one term of each type (First, Second, or Third).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitTermBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="submitTermSpinner"></span>
                        Save Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .term-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid transparent;
    }

    .term-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .term-card .badge-info {
        background-color: #17a2b8;
        border-left-color: #17a2b8;
    }

    .term-card .badge-success {
        background-color: #28a745;
        border-left-color: #28a745;
    }

    .term-card .badge-secondary {
        background-color: #6c757d;
        border-left-color: #6c757d;
    }

    .term-info .info-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
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
        .term-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function openAddTermModal() {
        document.getElementById('termForm').reset();
        document.getElementById('termFormTitle').textContent = 'Add Academic Term';
        document.getElementById('termId').value = '';

        const modal = new bootstrap.Modal(document.getElementById('termFormModal'));
        modal.show();
    }

    function editTerm(term) {
        document.getElementById('termFormTitle').textContent = 'Edit Academic Term';
        document.getElementById('termId').value = term.id;
        document.getElementById('termName').value = term.name;
        document.getElementById('termSession').value = term.session;
        document.getElementById('termTerm').value = term.term;
        document.getElementById('termStartDate').value = term.start_date;
        document.getElementById('termEndDate').value = term.end_date;
        document.getElementById('termStatus').value = term.status;
        document.getElementById('termDescription').value = term.description || '';

        const modal = new bootstrap.Modal(document.getElementById('termFormModal'));
        modal.show();
    }

    function submitTermForm(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitTermBtn');
        const spinner = document.getElementById('submitTermSpinner');
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData(e.target);
        const termId = document.getElementById('termId').value;
        const url = termId ? `/calendar/terms/${termId}` : '/calendar/terms';
        const method = termId ? 'PUT' : 'POST';

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
            alert('An error occurred while saving the term');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function deleteTerm(termId) {
        if (!confirm('Are you sure you want to delete this academic term? This will also remove the term association from all events.')) {
            return;
        }

        fetch(`/calendar/terms/${termId}`, {
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
            alert('An error occurred while deleting the term');
        });
    }
</script>
@endpush
