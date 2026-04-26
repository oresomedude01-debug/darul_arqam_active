<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
        }
        .receipt-title {
            font-size: 32px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .receipt-number {
            font-size: 18px;
            color: #666;
            font-family: monospace;
            letter-spacing: 2px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #333;
            font-weight: bold;
            margin-bottom: 12px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            line-height: 1.8;
            border-bottom: 1px dotted #ddd;
            padding: 6px 0;
        }
        .info-row.total {
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 16px;
            padding: 12px 0;
            margin-top: 10px;
        }
        .label {
            color: #555;
            font-weight: 600;
        }
        .value {
            text-align: right;
        }
        .amount-box {
            text-align: center;
            font-size: 48px;
            color: #28a745;
            font-weight: bold;
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #28a745;
            border-radius: 8px;
            background: #f0fff4;
        }
        .status {
            text-align: center;
            font-size: 14px;
            color: white;
            background: #28a745;
            padding: 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            width: 100%;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            text-align: center;
            width: 45%;
        }
        .line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 20mm;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">#{{ $receipt->receipt_number }}</div>
        </div>

        <!-- Institution Info -->
        <div class="section">
            <div class="section-title">Institution Details</div>
            <div class="info-row">
                <span class="label">School/Organization:</span>
                <span class="value">{{ $settings->school_name ?? config('app.name', 'Educational Institution') }}</span>
            </div>
            @if($settings && $settings->address)
                <div class="info-row">
                    <span class="label">Address:</span>
                    <span class="value">{{ $settings->address }}</span>
                </div>
            @endif
            @if($settings && $settings->phone)
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $settings->phone }}</span>
                </div>
            @endif
        </div>

        <!-- Student Information -->
        <div class="section">
            <div class="section-title">Student Information</div>
            <div class="info-row">
                <span class="label">Name:</span>
                <span class="value">{{ $receipt->payment->student->first_name }} {{ $receipt->payment->student->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Admission Number:</span>
                <span class="value">{{ $receipt->payment->student->admission_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">{{ $receipt->payment->student->email }}</span>
            </div>
        </div>

        <!-- Items Breakdown -->
        @if($receipt->payment->studentBill && $receipt->payment->studentBill->billItems->count() > 0)
            <div class="section">
                <div class="section-title">Payment For (Items)</div>
                @foreach($receipt->payment->studentBill->billItems as $item)
                    <div class="info-row">
                        <span class="label">{{ $item->name }}</span>
                        <span class="value">₦{{ number_format($item->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ $receipt->payment_method }}</span>
            </div>
            <div class="info-row">
                <span class="label">Receipt Date:</span>
                <span class="value">{{ $receipt->receipt_date->format('d M Y \a\t H:i A') }}</span>
            </div>
            @if($receipt->payment->transaction_reference)
                <div class="info-row">
                    <span class="label">Transaction Reference:</span>
                    <span class="value">{{ $receipt->payment->transaction_reference }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="label">Issued By:</span>
                <span class="value">{{ $receipt->issued_by }}</span>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="amount-box">
            ₦{{ number_format($receipt->amount, 2) }}
        </div>

        <!-- Total -->
        <div class="section">
            <div class="info-row total">
                <span class="label">TOTAL PAYMENT:</span>
                <span class="value">₦{{ number_format($receipt->amount, 2) }}</span>
            </div>
        </div>

        <!-- Status -->
        <div class="status">✓ PAYMENT RECEIVED AND PROCESSED</div>

        @if($receipt->notes)
            <div class="section">
                <div class="section-title">Notes</div>
                <p style="font-size: 12px; line-height: 1.6;">{{ $receipt->notes }}</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-line">
                <div class="line"></div>
                <p style="margin-top: 5px; font-size: 11px;">Authorized Signatory</p>
            </div>
            <div class="signature-line">
                <p style="font-size: 11px;">Date: {{ $receipt->receipt_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an official receipt issued by the institution</p>
            <p>Please retain this receipt for your records</p>
            <p style="margin-top: 10px; color: #ccc;">Generated: {{ $receipt->receipt_date->format('Y-m-d H:i:s') }}</p>
            <p style="color: #ccc;">Receipt System v1.0</p>
        </div>
    </div>
</body>
</html>
