<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #2c5aa0;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background-color: white;
            border-left: 4px solid #2c5aa0;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        .detail-row {
            display: flex;
            margin: 8px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ New Student Enrollment Completed</h1>
        </div>

        <div class="content">
            <p>Dear School Administrator,</p>

            <p>A new student has successfully completed their enrollment process. Please find the details below:</p>

            <!-- Student Information -->
            <div class="section">
                <div class="section-title">Student Information</div>
                <div class="detail-row">
                    <span class="detail-label">Student Name:</span>
                    <span class="detail-value"><strong>{{ $studentName }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Admission Number:</span>
                    <span class="detail-value"><strong>{{ $admissionNumber }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Student Email:</span>
                    <span class="detail-value">{{ $studentEmail }}</span>
                </div>
                @if($dateOfBirth)
                <div class="detail-row">
                    <span class="detail-label">Date of Birth:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($dateOfBirth)->format('d M Y') }}</span>
                </div>
                @endif
                @if($gender)
                <div class="detail-row">
                    <span class="detail-label">Gender:</span>
                    <span class="detail-value">{{ ucfirst($gender) }}</span>
                </div>
                @endif
                @if($address)
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ $address }}</span>
                </div>
                @endif
            </div>

            <!-- Parent/Guardian Information -->
            <div class="section">
                <div class="section-title">Parent/Guardian Information</div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><strong>{{ $parentName }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $parentEmail }}</span>
                </div>
                @if(isset($enrollmentData['parent_phone']))
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $enrollmentData['parent_phone'] }}</span>
                </div>
                @endif
                @if(isset($enrollmentData['parent_occupation']))
                <div class="detail-row">
                    <span class="detail-label">Occupation:</span>
                    <span class="detail-value">{{ $enrollmentData['parent_occupation'] }}</span>
                </div>
                @endif
            </div>

            <!-- Action Required -->
            <div class="highlight">
                <strong>Action Required:</strong><br>
                Please review this enrollment and take any necessary administrative actions such as:
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Assigning the student to appropriate classes</li>
                    <li>Setting up payment arrangements</li>
                    <li>Scheduling orientation or assessment</li>
                    <li>Updating any school-specific information</li>
                </ul>
            </div>

            <p>This is an automated notification from the School Management System. Please do not reply to this email.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} School Management System | Darul Arqam</p>
        </div>
    </div>
</body>
</html>
