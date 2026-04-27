<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMailController extends Controller
{
    /**
     * Show the send mail form.
     */
    public function index()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        
        return view('admin.send-mail.index', compact('users'));
    }

    /**
     * Send an email to selected user(s).
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message_body' => 'required|string',
            'priority' => 'nullable|in:normal,high',
        ]);

        $recipients = User::whereIn('id', $validated['recipients'])->get();
        $subject = $validated['subject'];
        $messageBody = $validated['message_body'];
        $priority = $validated['priority'] ?? 'normal';

        $sent = 0;
        $failed = 0;
        $failedEmails = [];

        foreach ($recipients as $user) {
            try {
                Mail::html(
                    view('admin.send-mail.email-template', [
                        'userName' => $user->name,
                        'messageBody' => $messageBody,
                        'schoolName' => config('app.name', 'Darul Arqam School'),
                    ])->render(),
                    function ($mail) use ($user, $subject, $priority) {
                        $mail->to($user->email)
                             ->subject($subject);
                        
                        if ($priority === 'high') {
                            $mail->priority(1);
                        }
                    }
                );
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $failedEmails[] = $user->email;
                Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }

        $message = "Email sent successfully to {$sent} recipient(s).";
        if ($failed > 0) {
            $message .= " Failed to send to {$failed} recipient(s): " . implode(', ', $failedEmails);
            return back()->with('warning', $message);
        }

        return back()->with('success', $message);
    }
}
