<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ $schoolName }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .credentials { background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0; }
        .credentials p { margin: 8px 0; }
        .features { list-style: none; padding: 0; }
        .features li { padding: 8px 0; padding-left: 25px; position: relative; }
        .features li:before { content: "✓"; position: absolute; left: 0; color: #28a745; font-weight: bold; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; color: #007bff;">Welcome to {{ $schoolName }}</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $parentName }}</strong>,</p>
            
            <p>Your child <strong>{{ $studentName }}</strong> has been successfully enrolled in <strong>{{ $schoolName }}</strong>!</p>
            
            <div class="section">
                <h2>Your Parent Portal Account</h2>
                <p>An account has been created for you so you can manage your child's education and stay connected with the school.</p>
                
                <h3 style="font-size: 14px; margin: 15px 0 10px 0;">Login Credentials</h3>
                <div class="credentials">
                    <p><strong>Email:</strong> <code>{{ $parentEmail }}</code></p>
                    <p><strong>Temporary Password:</strong> <code>{{ $defaultPassword }}</code></p>
                </div>
            </div>
            
            <div class="section">
                <h2>Important Instructions</h2>
                <ol>
                    <li><strong>First Login:</strong> Go to <a href="{{ $loginUrl }}">{{ config('app.url') }}</a> and sign in with the credentials above</li>
                    <li><strong>Change Your Password:</strong> After your first login, you <strong>MUST</strong> change your password immediately for security</li>
                    <li><strong>Create a Strong Password:</strong> Use a combination of uppercase, lowercase, numbers, and special characters</li>
                    <li><strong>Keep it Safe:</strong> Never share your login credentials with anyone</li>
                </ol>
            </div>
            
            <div class="section">
                <h2>What You Can Do</h2>
                <p>Once logged in, you can:</p>
                <ul class="features">
                    <li>View your child's academic progress</li>
                    <li>Check attendance records</li>
                    <li>View grades and reports</li>
                    <li>Communicate with teachers</li>
                    <li>Pay school fees online</li>
                    <li>Update your profile information</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>Need Help?</h2>
                <p>If you have any questions or issues logging in, please contact the school:</p>
                <p>
                    📧 <strong>Email:</strong> {{ $schoolEmail }}<br>
                    📞 <strong>Phone:</strong> {{ $schoolPhone }}
                </p>
            </div>
            
            <div class="footer">
                <p>Best regards,<br><strong>{{ $schoolName }} Administration</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
