<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Student Enrollment</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background-color: #e7f5ff; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #1971c2; }
        .header h1 { margin: 0; color: #1971c2; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #1971c2; padding-bottom: 5px; }
        .student-card { background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #1971c2; margin: 15px 0; }
        .student-card p { margin: 10px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: bold; color: #666; }
        .detail-value { color: #333; }
        .button { display: inline-block; padding: 12px 30px; background-color: #1971c2; color: white; text-decoration: none; border-radius: 4px; margin: 15px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Student Enrollment Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $parentName }}</strong>,</p>
            
            <p>We are pleased to inform you that a new student has been successfully enrolled in your account at <strong>{{ $appName }}</strong>.</p>
            
            <div class="section">
                <h2>Enrollment Details</h2>
                
                <div class="student-card">
                    <div class="detail-row">
                        <span class="detail-label">Student Name:</span>
                        <span class="detail-value"><strong>{{ $studentName }}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Admission Number:</span>
                        <span class="detail-value"><code>{{ $admissionNumber }}</code></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Enrollment Date:</span>
                        <span class="detail-value">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>Next Steps</h2>
                
                <p>You can now:</p>
                <ul>
                    <li>View your child's enrollment details</li>
                    <li>Monitor academic progress and attendance</li>
                    <li>Access class information and timetables</li>
                    <li>Receive communications from teachers</li>
                    <li>View and pay bills when generated</li>
                </ul>
                
                <p style="text-align: center;">
                    <a href="{{ $portalUrl }}" class="button">Access Parent Portal</a>
                </p>
            </div>
            
            <p>If you have any questions about the enrollment or need any assistance, please don't hesitate to contact the school.</p>
            
            <div class="footer">
                <p>Best regards,<br><strong>{{ $appName }} Administration</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
