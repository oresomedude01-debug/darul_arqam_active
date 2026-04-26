@extends('layouts.spa')

@section('title', 'Payment Receipts')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Payment Receipts</h2>
            <p class="text-muted">View and manage all payment receipts</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number" class="form-control" 
                           value="{{ request('receipt_number') }}" placeholder="Search receipt...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student['id'] }}" {{ request('student_id') == $student['id'] ? 'selected' : '' }}>
                                {{ $student['first_name'] }} {{ $student['last_name'] }} ({{ $student['admission_number'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Receipts ({{ $receipts->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $receipt->receipt_number }}</span>
                            </td>
                            <td>
                                <strong>{{ $receipt->payment->student->full_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $receipt->payment->student->admission_number }}</small>
                            </td>
                            <td>
                                <strong>₦{{ number_format($receipt->payment->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method)) }}</span>
                            </td>
                            <td>
                                {{ $receipt->generated_at->format('M d, Y H:i') }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('payments.receipts.view', $receipt) }}" 
                                       class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.receipts.print', $receipt) }}" 
                                       target="_blank" class="btn btn-outline-info" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('payments.receipts.pdf', $receipt) }}" 
                                       target="_blank" class="btn btn-outline-danger" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox text-3xl mb-2 d-block"></i>
                                No receipts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">
            {{ $receipts->links() }}
        </div>
    </div>
</div>
@endsection
