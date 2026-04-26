<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 20px;
        }
        .content h2 {
            color: #667eea;
            font-size: 20px;
            margin-top: 0;
        }
        .content p {
            margin: 15px 0;
            color: #555;
        }
        .features {
            margin: 30px 0;
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 4px;
        }
        .features h3 {
            color: #667eea;
            margin-top: 0;
        }
        .features ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        .features li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            color: #888;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .cta-button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 600;
        }
        .status-box {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✉️ Email Configuration Test</h1>
        </div>

        <div class="content">
            <h2>Hello Administrator,</h2>

            <p>This is a test email from your <strong>{{ $schoolName }}</strong> School Management System.</p>

            <div class="status-box">
                <strong>✓ Success!</strong> Your email configuration is working correctly.
            </div>

            <p>This email confirms that:</p>

            <div class="features">
                <h3>Mail System Status</h3>
                <ul>
                    <li>SMTP connection is established</li>
                    <li>Authentication successful</li>
                    <li>Email delivery is functional</li>
                    <li>HTML formatting is supported</li>
                    <li>System can send notifications</li>
                </ul>
            </div>

            <h3>What This Means</h3>
            <p>Your school management system can now reliably send:</p>
            <ul>
                <li>User account notifications</li>
                <li>Password reset emails</li>
                <li>Billing and payment reminders</li>
                <li>Report cards and result notifications</li>
                <li>Administrative announcements</li>
                <li>Class schedules and updates</li>
            </ul>

            <h3>Recommended Next Steps</h3>
            <ol>
                <li>Send test emails to different email providers (Gmail, Outlook, Yahoo, etc.)</li>
                <li>Verify emails appear in correct folder (not spam)</li>
                <li>Review email formatting and styling</li>
                <li>Configure email templates for your institution</li>
                <li>Set up automated alerts and notifications</li>
            </ol>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 13px;">
                This is an automated test message. No action is required. If you did not initiate this test, please contact your system administrator.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                {{ $schoolName }} Management System
                <br>
                <span style="font-size: 11px;">Generated: {{ now()->format('Y-m-d H:i:s') }}</span>
            </p>
        </div>
    </div>
</body>
</html>
