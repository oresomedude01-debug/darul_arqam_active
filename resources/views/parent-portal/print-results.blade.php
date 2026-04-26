<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - {{ $child->first_name }} {{ $child->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 15px 0;
        }
        
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .results-table thead {
            background: linear-gradient(to right, #2563eb, #1d4ed8);
            color: white;
        }
        
        .results-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        .results-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        
        .results-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .results-table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .score-cell {
            font-weight: 600;
            color: #2563eb;
        }
        
        .grade-excellent {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
        }
        
        .grade-good {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
        }
        
        .grade-fair {
            background-color: #fed7aa;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
        }
        
        .grade-poor {
            background-color: #fecaca;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 30px 0;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-card.average {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .summary-card.highest {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        
        .summary-card.lowest {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .summary-card.total {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        
        .summary-card.highest,
        .summary-card.lowest,
        .summary-card.total {
            color: #1f2937;
        }
        
        .summary-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        
        .summary-value {
            font-size: 28px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2563eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .signature {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100%;
                padding: 20px;
            }
            .print-button {
                display: none;
            }
        }
        
        .print-button {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="print-button" onclick="window.print()">🖨️ Print Results</button>
        
        <div class="header">
            <div class="school-name">DARUL ARQAM SCHOOL</div>
            <div class="report-title">Academic Report Card</div>
        </div>
        
        <div class="student-info">
            <div>
                <div class="info-row">
                    <span class="info-label">Student Name:</span>
                    <span class="info-value">{{ $child->first_name }} {{ $child->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Student ID:</span>
                    <span class="info-value">{{ $child->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $child->schoolClass->name ?? 'N/A' }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Session:</span>
                    <span class="info-value">{{ $selectedSession->name ?? 'Current' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Term:</span>
                    <span class="info-value">{{ $selectedTerm->name ?? 'Current' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Print Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="summary">
            <div class="summary-card average">
                <div class="summary-label">Average Score</div>
                <div class="summary-value">{{ number_format($averageScore, 1) }}%</div>
            </div>
            <div class="summary-card highest">
                <div class="summary-label">Highest Score</div>
                <div class="summary-value">{{ $highestScore }}%</div>
            </div>
            <div class="summary-card lowest">
                <div class="summary-label">Lowest Score</div>
                <div class="summary-value">{{ $lowestScore }}%</div>
            </div>
            <div class="summary-card total">
                <div class="summary-label">Total Subjects</div>
                <div class="summary-value">{{ $grades->count() }}</div>
            </div>
        </div>
        
        <!-- Results Table -->
        <table class="results-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Score (%)</th>
                    <th>Grade</th>
                    <th>Remark</th>
                    <th>Teacher</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $index => $grade)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $grade->subject ? $grade->subject->name : 'Unknown' }}</td>
                        <td class="score-cell">{{ round($grade->total_score, 2) }}%</td>
                        <td>
                            <span class="@if($grade->total_score >= 70) grade-excellent @elseif($grade->total_score >= 60) grade-good @elseif($grade->total_score >= 50) grade-fair @else grade-poor @endif">
                                {{ $grade->grade ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ $grade->remark ?? 'Good' }}</td>
                        <td>{{ $grade->teacher ? ($grade->teacher->user->first_name ?? 'N/A') : 'N/A' }} {{ $grade->teacher ? ($grade->teacher->user->last_name ?? '') : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Signature Section -->
        <div class="signature">
            <div>
                <div class="signature-line">Principal Signature</div>
            </div>
            <div>
                <div class="signature-line">Class Teacher Signature</div>
            </div>
            <div>
                <div class="signature-line">Parent/Guardian Signature</div>
            </div>
        </div>
        
        <div class="footer">
            <p>This report is confidential and intended for the use of parents/guardians only.</p>
            <p>Generated on {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>
