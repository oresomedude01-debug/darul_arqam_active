<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .credentials { background-color: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; border-radius: 4px; margin: 15px 0; }
        .credentials p { margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table td { border: 1px solid #ddd; padding: 10px; }
        table td:first-child { font-weight: bold; width: 30%; background-color: #f8f9fa; }
        .button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 15px 0; }
        .features { counter-reset: item; list-style-type: none; padding: 0; }
        .features li { counter-increment: item; margin-bottom: 12px; padding-left: 30px; position: relative; }
        .features li:before { content: counter(item); position: absolute; left: 0; width: 24px; height: 24px; background-color: #007bff; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-weight: bold; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Welcome to {{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong>,</p>
            
            <p>Welcome to <strong>{{ config('app.name') }}</strong>! We are delighted to have you join our educational team.</p>
            
            <div class="section">
                <h2>Your Account Information</h2>
                <p>Your teacher account has been created successfully. Please use the following credentials to access the school management system:</p>
                
                <table>
                    <tr>
                        <td>Email</td>
                        <td><code>{{ $teacherEmail }}</code></td>
                    </tr>
                    <tr>
                        <td>Temporary Password</td>
                        <td><code>{{ $temporaryPassword }}</code></td>
                    </tr>
                    <tr>
                        <td>School</td>
                        <td>{{ config('app.name') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="section">
                <h2>Important Next Steps</h2>
                
                <ol class="features">
                    <li><strong>Login to Your Dashboard:</strong> Visit the school portal and log in with your email and the temporary password provided above.</li>
                    <li><strong>Change Your Password:</strong> For security purposes, you will be prompted to change your password on first login.</li>
                    <li><strong>Complete Your Profile:</strong> Please update your profile information including contact details and professional qualifications.</li>
                    <li><strong>Review School Policies:</strong> Familiarize yourself with school policies, timetables, and procedures in the Staff Handbook section.</li>
                </ol>
            </div>
            
            <div class="section">
                <h2>Your Role & Responsibilities</h2>
                <p>As a member of our teaching staff, you have access to:</p>
                
                <ul class="features">
                    <li><strong>Class Management:</strong> Manage student attendance, grades, and performance</li>
                    <li><strong>Communication:</strong> Send messages to parents and students</li>
                    <li><strong>Reports:</strong> Generate student performance reports</li>
                    <li><strong>Calendar:</strong> View and manage academic events and holidays</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Log In to Your Account</a>
            </div>
            
            <div class="section">
                <h2>Need Help?</h2>
                <p>If you have any difficulty accessing your account or need technical assistance, please contact our IT support team at <strong>{{ config('mail.from.address') }}</strong>.</p>
            </div>
            
            <p>We look forward to working with you and wish you a fulfilling career at <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="footer">
                <p>Best regards,<br><strong>School Administration</strong><br>{{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
