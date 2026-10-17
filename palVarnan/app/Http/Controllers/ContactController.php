<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display the Contact Us page.
     */
    public function index()
    {
        return view('pages.contact');
    }

    /**
     * Check if the outgoing SMTP host & port are reachable before attempting live dispatch.
     * Prevents browser form timeout in restricted local network/ISP environments.
     */
    protected function isSmtpReachable(): bool
    {
        if (config('mail.default') === 'log') {
            return true;
        }

        $host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $port = config('mail.mailers.smtp.port', 587);

        $socket = @fsockopen($host, (int) $port, $errno, $errstr, 1.2);
        if ($socket) {
            fclose($socket);
            return true;
        }

        return false;
    }

    /**
     * Store a newly submitted contact inquiry and dispatch dual HTML emails.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:25',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        $contact = Contact::create($validated);
        $adminEmail = env('ADMIN_EMAIL', 'sriramsri1234321@gmail.com');
        $canSendMail = $this->isSmtpReachable();

        if ($canSendMail) {
            // 1. Dispatch HTML Notification Email to Admin
            if (!empty($adminEmail)) {
                try {
                    Mail::send('emails.contact_admin', ['contact' => $contact], function ($message) use ($adminEmail, $contact) {
                        $message->to($adminEmail)
                                ->subject("🔔 New Customer Inquiry #{$contact->id}: [{$contact->subject}] - {$contact->name}");
                    });
                } catch (\Throwable $e) {
                    Log::warning('Admin contact inquiry email failed: ' . $e->getMessage());
                }
            }

            // 2. Dispatch HTML Confirmation Email to Customer
            if (!empty($contact->email)) {
                try {
                    Mail::send('emails.contact_customer', ['contact' => $contact], function ($message) use ($contact) {
                        $message->to($contact->email, $contact->name)
                                ->subject("Thank You for Contacting Pal Varnan [Ref #{$contact->id}]");
                    });
                } catch (\Throwable $e) {
                    Log::warning('Customer contact confirmation email failed: ' . $e->getMessage());
                }
            }
        } else {
            Log::info("SMTP server port currently unreachable on local network. Rendered email templates prepared for Admin ({$adminEmail}) and Customer ({$contact->email}).");
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for reaching out to Pal Varnan! Your message has been received and our team will contact you shortly.',
                'contact' => $contact
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for reaching out to Pal Varnan! Your message has been received.');
    }
}
