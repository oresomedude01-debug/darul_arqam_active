<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Enrollment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table td { border: 1px solid #ddd; padding: 10px; }
        table td:first-child { font-weight: bold; width: 40%; background-color: #f8f9fa; }
        .details-box { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 15px 0; }
        .next-steps { counter-reset: item; list-style-type: none; padding: 0; }
        .next-steps li { counter-increment: item; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd; }
        .next-steps li:last-child { border-bottom: none; }
        .next-steps li:before { content: counter(item); display: inline-block; width: 24px; height: 24px; background-color: #007bff; color: white; border-radius: 50%; text-align: center; line-height: 24px; margin-right: 10px; font-weight: bold; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Student Enrollment Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $parent->first_name }} {{ $parent->last_name }}</strong>,</p>
            
            <p>We are pleased to confirm that your child has been successfully enrolled at <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="section">
                <h2>Enrollment Details</h2>
                
                <table>
                    <tr>
                        <td>Student Name</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    </tr>
                    <tr>
                        <td>Admission Number</td>
                        <td>{{ $admissionNumber }}</td>
                    </tr>
                    <tr>
                        <td>Date of Birth</td>
                        <td>{{ $student->date_of_birth?->format('d M Y') ?? 'Not Provided' }}</td>
                    </tr>
                    <tr>
                        <td>Class</td>
                        <td>{{ $student->schoolClass?->name ?? 'To be assigned' }}</td>
                    </tr>
                    <tr>
                        <td>Enrollment Date</td>
                        <td>{{ now()->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="section">
                <h2>What's Next?</h2>
                
                <ol class="next-steps">
                    <li>
                        <strong>Portal Access:</strong> You now have access to the Parent Portal where you can:
                        <ul style="margin-top: 8px;">
                            <li>View student progress and attendance</li>
                            <li>Monitor grades and performance</li>
                            <li>Receive school communications</li>
                            <li>Manage bills and payments</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Important Documents:</strong> Please ensure you provide:
                        <ul style="margin-top: 8px;">
                            <li>Birth certificate or national ID</li>
                            <li>Medical records and vaccination proof</li>
                            <li>Previous school records (if transferring)</li>
                        </ul>
                    </li>
                    <li>
                        <strong>School Handbook:</strong> Review the school handbook for policies regarding:
                        <ul style="margin-top: 8px;">
                            <li>School hours and holidays</li>
                            <li>Dress code</li>
                            <li>Code of conduct</li>
                            <li>Fees and payment terms</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Fee Structure:</strong> Bills for the current academic session will be generated shortly. You can view and pay bills through the Parent Portal.
                    </li>
                </ol>
            </div>
            
            <div class="section">
                <h2>School Information</h2>
                
                <div class="details-box">
                    <p><strong>Email:</strong> {{ config('mail.from.address') }}</p>
                    <p><strong>Portal:</strong> <a href="{{ route('parent-portal.index') }}">Access Parent Portal</a></p>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('parent-portal.index') }}" class="button">Access Parent Portal</a>
            </div>
            
            <p>If you have any questions regarding your child's enrollment or need any assistance, please do not hesitate to contact us.</p>
            
            <p>We look forward to supporting your child's educational journey at <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="footer">
                <p>Best regards,<br><strong>School Administration</strong><br>{{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
