<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .receipt-number {
            font-size: 14px;
            color: #666;
            font-family: monospace;
        }
        .receipt-section {
            margin-bottom: 25px;
        }
        .receipt-section-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px dotted #ddd;
        }
        .receipt-row.total {
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 16px;
            padding: 12px 0;
        }
        .receipt-label {
            color: #666;
        }
        .receipt-value {
            text-align: right;
            font-weight: 500;
        }
        .amount-large {
            font-size: 28px;
            color: #28a745;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="print-button no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="btn btn-secondary" onclick="window.history.back()">
            Back
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">#{{ $receipt->receipt_number }}</div>
        </div>

        <!-- School Info -->
        <div class="receipt-section">
            <div class="receipt-section-title">Institution</div>
            <div class="receipt-row">
                <span class="receipt-label">School:</span>
                <span class="receipt-value">{{ $settings->school_name ?? config('app.name', 'School System') }}</span>
            </div>
            @if($settings && $settings->address)
                <div class="receipt-row">
                    <span class="receipt-label">Address:</span>
                    <span class="receipt-value">{{ $settings->address }}</span>
                </div>
            @endif
        </div>

        <!-- Student Info -->
        <div class="receipt-section">
            <div class="receipt-section-title">Student Information</div>
            <div class="receipt-row">
                <span class="receipt-label">Name:</span>
                <span class="receipt-value">{{ $receipt->payment->student->full_name }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Admission Number:</span>
                <span class="receipt-value">{{ $receipt->payment->student->admission_number }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Class:</span>
                <span class="receipt-value">{{ $receipt->payment->student->schoolClass?->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Items Breakdown -->
        @if($receipt->payment->studentBill && $receipt->payment->studentBill->billItems->count() > 0)
            <div class="receipt-section">
                <div class="receipt-section-title">Payment For</div>
                @foreach($receipt->payment->studentBill->billItems as $item)
                    <div class="receipt-row">
                        <span class="receipt-label">{{ $item->feeItem?->name ?? $item->name ?? 'Fee Item' }}</span>
                        <span class="receipt-value">₦{{ number_format($item->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Payment Details -->
        <div class="receipt-section">
            <div class="receipt-section-title">Payment Details</div>
            <div class="receipt-row">
                <span class="receipt-label">Payment Method:</span>
                <span class="receipt-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method)) }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Payment Date:</span>
                <span class="receipt-value">{{ $receipt->payment->payment_date?->format('M d, Y H:i A') ?? 'N/A' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Generated:</span>
                <span class="receipt-value">{{ $receipt->generated_at->format('M d, Y H:i A') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Reference:</span>
                <span class="receipt-value">{{ $receipt->payment->reference_number ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="receipt-section">
            <div class="amount-large">₦{{ number_format($receipt->payment->amount, 2) }}</div>
            <div class="receipt-row total">
                <span class="receipt-label">TOTAL PAID:</span>
                <span class="receipt-value">₦{{ number_format($receipt->payment->amount, 2) }}</span>
            </div>
        </div>

        <!-- Status -->
        <div class="receipt-section">
            <div style="text-align: center;">
                <span class="status-badge">✓ PAYMENT COMPLETED</span>
            </div>
        </div>

        @if($receipt->notes)
            <div class="receipt-section">
                <div class="receipt-section-title">Notes</div>
                <p style="font-size: 13px; margin: 0;">{{ $receipt->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer-text">
            <p style="margin: 0 0 5px 0;">This is an official receipt issued by the institution.</p>
            <p style="margin: 0;">Please keep this receipt for your records.</p>
            <p style="margin: 10px 0 0 0; color: #ccc;">{{ $receipt->generated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
