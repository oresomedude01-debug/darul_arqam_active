<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; padding: 28px 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p { margin: 6px 0 0; font-size: 13px; opacity: 0.85; }
        .body { padding: 32px; color: #334155; line-height: 1.7; font-size: 15px; }
        .greeting { font-size: 17px; font-weight: 600; color: #1e293b; margin-bottom: 16px; }
        .message-content { white-space: pre-wrap; word-wrap: break-word; background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #0284c7; margin: 16px 0; }
        .footer { padding: 20px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; }
        .footer a { color: #0284c7; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $schoolName }}</h1>
            <p>Official Communication</p>
        </div>
        <div class="body">
            <p class="greeting">Dear {{ $userName }},</p>
            <div class="message-content">{!! nl2br(e($messageBody)) !!}</div>
            <p style="margin-top: 24px; color: #64748b; font-size: 13px;">
                This is an official email from {{ $schoolName }}. Please do not reply directly to this email.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
