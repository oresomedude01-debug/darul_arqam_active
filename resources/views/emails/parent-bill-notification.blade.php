<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Bill(s) Created</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #222; font-size: 18px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .summary { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold; }
        table td { border: 1px solid #ddd; padding: 10px; }
        .amount-box { background-color: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; border-radius: 4px; margin: 15px 0; font-size: 16px; }
        .button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 15px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">New Bill(s) Created</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $parent->first_name }} {{ $parent->last_name }}</strong>,</p>
            
            <p>We are writing to inform you that a new bill has been created for your child/children at <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="section">
                <h2>Bill Summary</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Admission #</th>
                            <th>Class</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                        <tr>
                            <td>{{ $bill->student->first_name }} {{ $bill->student->last_name }}</td>
                            <td>{{ $bill->student->admission_number }}</td>
                            <td>{{ $bill->student->schoolClass?->name ?? 'N/A' }}</td>
                            <td>₦{{ number_format($bill->total_amount, 2) }}</td>
                            <td>{{ $bill->due_date?->format('M d, Y') ?? 'Not Set' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No bills</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="amount-box">
                <strong>Total Outstanding Amount:</strong> ₦{{ number_format($totalAmount, 2) }}<br>
                <strong>Number of Children with Bills:</strong> {{ $childrenCount }}
            </div>
            
            <div class="section">
                <h2>Next Steps</h2>
                <p>Please log in to your parent portal to:</p>
                <ul>
                    <li>View detailed bill information</li>
                    <li>Make payments online</li>
                    <li>Download bill receipts</li>
                    <li>Set up payment reminders</li>
                </ul>
                
                <a href="{{ route('parent-portal.index') }}" class="button">View Your Bills</a>
            </div>
            
            <p>If you have any questions regarding these bills, please contact us at <strong>{{ config('school.contact_email', config('mail.from.address')) }}</strong>.</p>
            
            <p>Thank you for your attention.</p>
            
            <div class="footer">
                <p>Best regards,<br><strong>{{ config('app.name') }} - Finance Department</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
