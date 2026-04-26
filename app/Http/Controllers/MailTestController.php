<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    /**
     * Show mail test form
     */
    public function index()
    {
        return view('mail-test.index');
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'recipient_email' => 'required|email',
            'email_type' => 'required|in:simple,html',
        ]);

        try {
            $recipientEmail = $validated['recipient_email'];
            $emailType = $validated['email_type'];

            if ($emailType === 'simple') {
                // Send simple text email
                Mail::raw('This is a test email from Darul Arqam School Management System. If you received this, your mail configuration is working correctly!', function ($message) use ($recipientEmail) {
                    $message->to($recipientEmail)
                        ->subject('Test Email - Darul Arqam School');
                });
            } else {
                // Send HTML email
                Mail::html(view('mail-test.template', ['schoolName' => 'Darul Arqam School'])->render(), function ($message) use ($recipientEmail) {
                    $message->to($recipientEmail)
                        ->subject('Test Email - Darul Arqam School');
                });
            }

            return back()->with('success', "Test email sent successfully to {$recipientEmail}! Please check your inbox or spam folder.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending test email: ' . $e->getMessage());
        }
    }

    /**
     * Show mail configuration
     */
    public function showConfig()
    {
        $config = [
            'MAIL_DRIVER' => config('mail.driver'),
            'MAIL_HOST' => config('mail.host'),
            'MAIL_PORT' => config('mail.port'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_ENCRYPTION' => config('mail.encryption'),
        ];

        // Hide sensitive info
        $displayConfig = $config;
        if ($displayConfig['MAIL_HOST']) {
            $displayConfig['MAIL_HOST'] = str_repeat('*', strlen($config['MAIL_HOST']) - 3) . substr($config['MAIL_HOST'], -3);
        }

        return view('mail-test.config', ['config' => $displayConfig, 'driver' => config('mail.driver')]);
    }

    /**
     * Test SMTP connection
     */
    public function testConnection()
    {
        header('Content-Type: application/json');
        
        try {
            $driver = config('mail.driver');

            // Check if driver is set
            if (!$driver) {
                return response()->json(['status' => 'error', 'message' => 'Mail driver not configured'], 400);
            }

            // For SMTP, try basic connection test
            if ($driver === 'smtp') {
                $host = config('mail.host');
                $port = config('mail.port');

                if (!$host || !$port) {
                    return response()->json(['status' => 'error', 'message' => 'SMTP host or port not configured'], 400);
                }

                // Try to connect to SMTP server
                $connection = @fsockopen($host, $port, $errno, $errstr, 5);

                if ($connection) {
                    fclose($connection);
                    return response()->json(['status' => 'success', 'message' => "SMTP connection successful to {$host}:{$port}"]);
                } else {
                    return response()->json(['status' => 'error', 'message' => "Cannot connect to {$host}:{$port}. Error: {$errstr}"], 400);
                }
            }

            return response()->json(['status' => 'info', 'message' => "Driver is set to '{$driver}'. Send a test email to verify configuration."]);
        } catch (\Throwable $e) {
            \Log::error('Mail test connection error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
