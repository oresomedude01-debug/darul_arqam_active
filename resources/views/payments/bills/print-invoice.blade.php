<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $bill->student->first_name }} {{ $bill->student->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-box h3 {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .detail-box p {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .table thead {
            background-color: #f8f9fa;
        }
        .table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .table tr:last-child td {
            border-bottom: 2px solid #333;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-box label {
            font-size: 12px;
            color: #666;
            display: block;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .summary-box .amount {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="print-btn">
            <button onclick="window.print()">Print Invoice</button>
        </div>

        <div class="header">
            <h1>INVOICE</h1>
            <p>Student Billing Statement</p>
        </div>

        <div class="invoice-details">
            <div class="detail-box">
                <h3>Student Information</h3>
                <p><strong>Name:</strong> {{ $bill->student->first_name }} {{ $bill->student->last_name }}</p>
                <p><strong>Admission No:</strong> {{ $bill->student->admission_number }}</p>
                <p><strong>Class:</strong> {{ $bill->schoolClass->name ?? 'N/A' }}</p>
                <p><strong>Session:</strong> {{ $bill->academicSession->session }}</p>
            </div>
            <div class="detail-box">
                <h3>Billing Information</h3>
                <p><strong>Invoice Date:</strong> {{ now()->format('d M Y') }}</p>
                <p><strong>Generated:</strong> {{ $bill->created_at->format('d M Y H:i') }}</p>
                @if($bill->status === 'paid')
                <span class="status-badge status-paid">✓ PAID</span>
                @else
                <span class="status-badge status-pending">⚠ PENDING</span>
                @endif
            </div>
        </div>

        <h2 style="font-size: 16px; margin-bottom: 15px;">Fee Breakdown</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Fee Item</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->billItems as $item)
                <tr>
                    <td>{{ $item->feeItem->name ?? 'Unknown Fee' }}</td>
                    <td style="text-align: right;">₦{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td>TOTAL</td>
                    <td style="text-align: right;">₦{{ number_format($bill->billItems->sum('amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-box">
                <label>Total Due</label>
                <div class="amount">₦{{ number_format($bill->total_amount, 2) }}</div>
            </div>
            <div class="summary-box">
                <label>Amount Paid</label>
                <div class="amount">₦{{ number_format($bill->paid_amount, 2) }}</div>
            </div>
            <div class="summary-box">
                <label>Balance Due</label>
                <div class="amount" style="@if($bill->balance_due > 0) color: #dc3545; @else color: #28a745; @endif">
                    ₦{{ number_format($bill->balance_due, 2) }}
                </div>
            </div>
        </div>

        @if($bill->notes)
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <strong>Notes:</strong>
            <p>{{ $bill->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>This is an automated invoice. Please retain for your records.</p>
            <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
