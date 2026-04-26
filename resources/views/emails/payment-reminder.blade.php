<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Reminder - Outstanding Bills</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background-color: #fff3cd; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #ffc107; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .summary { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold; }
        table td { border: 1px solid #ddd; padding: 10px; }
        .amount-box { background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 4px; margin: 15px 0; font-size: 16px; }
        .button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 15px 0; }
        .payment-methods { list-style: none; padding: 0; }
        .payment-methods li { padding: 8px 0; padding-left: 25px; position: relative; }
        .payment-methods li:before { content: "✓"; position: absolute; left: 0; color: #28a745; font-weight: bold; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Payment Reminder - Outstanding Bills</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $parent->name }}</strong>,</p>
            
            <p>This is a friendly reminder that there are outstanding bills pending payment for your child/children at <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="section">
                <h2>Outstanding Bills Summary</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Admission #</th>
                            <th>Class</th>
                            <th>Balance Due</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                        <tr>
                            <td>{{ $bill->student->first_name }} {{ $bill->student->last_name }}</td>
                            <td>{{ $bill->student->admission_number }}</td>
                            <td>{{ $bill->student->schoolClass?->name ?? 'N/A' }}</td>
                            <td>₦{{ number_format($bill->balance_due, 2) }}</td>
                            <td>{{ $bill->due_date?->format('M d, Y') ?? 'Not Set' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No outstanding bills</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="amount-box">
                <strong>Total Amount Due: ₦{{ number_format($totalDue, 2) }}</strong>
            </div>
            
            <div class="section">
                <h2>Payment Options</h2>
                <p>You can make payments through:</p>
                <ul class="payment-methods">
                    <li>Online payment gateway (Paystack, etc.)</li>
                    <li>Bank transfer</li>
                    <li>Cash payment at school office</li>
                    <li>Cheque payment</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>Take Action Now</h2>
                <a href="{{ route('parent-portal.bills') }}" class="button">Pay Your Bills Online</a>
            </div>
            
            <div class="section">
                <h2>Need Help?</h2>
                <p>If you have questions about your bills or need assistance with payment, please contact our finance department at <strong>{{ config('school.contact_email', config('mail.from.address')) }}</strong>.</p>
                <p>We appreciate your prompt attention to this matter.</p>
            </div>
            
            <div class="footer">
                <p>Best regards,<br><strong>{{ config('app.name') }} - Finance Department</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
