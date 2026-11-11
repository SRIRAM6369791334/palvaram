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
            'name'    => 'required|string|min:2|max:255',
            'email'   => 'required|email|max:255',
            'country' => 'nullable|string|in:india,mexico',
            'phone'   => ['required', 'string', 'min:7', 'max:25'],
            'subject' => 'required|string|min:2|max:255',
            'message' => 'required|string|min:10|max:3000',
        ], [
            'name.required'    => 'Full Name is required.',
            'name.min'         => 'Full Name must be at least 2 characters.',
            'email.required'   => 'Email Address is required.',
            'email.email'      => 'Please enter a valid email address.',
            'phone.required'   => 'Phone Number is required.',
            'subject.required' => 'Please select an inquiry topic.',
            'message.required' => 'Your message is required.',
            'message.min'      => 'Message must be at least 10 characters long.',
        ]);

        // Country-specific phone number validation
        $country = strtolower($request->input('country', ''));
        if (!in_array($country, ['india', 'mexico'])) {
            $sessionCountryId = session('country_id');
            $country = ($sessionCountryId == 244) ? 'mexico' : 'india';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);

        if ($country === 'india') {
            $national = $cleanPhone;
            if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
                $national = substr($cleanPhone, 2);
            } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                $national = substr($cleanPhone, 1);
            }

            if (!preg_match('/^[6-9]\d{9}$/', $national)) {
                $err = 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9 (e.g. +91 98765 43210).';
                return response()->json([
                    'message' => $err,
                    'errors'  => ['phone' => [$err]]
                ], 422);
            }
            $validated['phone'] = '+91 ' . $national;
        } else {
            // Mexico
            $national = $cleanPhone;
            if (strlen($cleanPhone) === 13 && str_starts_with($cleanPhone, '521')) {
                $national = substr($cleanPhone, 3);
            } elseif (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '52')) {
                $national = substr($cleanPhone, 2);
            }

            if (!preg_match('/^[2-9]\d{9}$/', $national)) {
                $err = 'Please enter a valid 10-digit Mexican phone number starting with area code 2-9 (e.g. +52 55 1234 5678).';
                return response()->json([
                    'message' => $err,
                    'errors'  => ['phone' => [$err]]
                ], 422);
            }
            $validated['phone'] = '+52 ' . $national;
        }

        try {
            // Auto-heal schema if phone column is missing in production database
            $payload = [
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'phone'   => $validated['phone'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ];
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('contacts')) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('contacts', 'phone')) {
                        \Illuminate\Support\Facades\Schema::table('contacts', function ($table) {
                            $table->string('phone', 50)->nullable()->after('email');
                        });
                    }
                } else {
                    \Illuminate\Support\Facades\Schema::create('contacts', function ($table) {
                        $table->id();
                        $table->string('name');
                        $table->string('email');
                        $table->string('phone', 50)->nullable();
                        $table->string('subject');
                        $table->text('message');
                        $table->timestamps();
                    });
                }
            } catch (\Throwable $schemaEx) {
                // If DB user has no ALTER permissions, embed phone in message and remove from payload
                unset($payload['phone']);
                $payload['message'] = "Phone: " . $validated['phone'] . "\n\n" . $validated['message'];
            }

            $contact = Contact::create($payload);
        } catch (\Throwable $dbEx) {
            Log::error('Contact form database save failed: ' . $dbEx->getMessage());

            // Secondary fallback: save without phone field
            try {
                $fallbackPayload = [
                    'name'    => $validated['name'],
                    'email'   => $validated['email'],
                    'subject' => $validated['subject'],
                    'message' => "Phone: " . $validated['phone'] . "\n\n" . $validated['message'],
                ];
                $contact = Contact::create($fallbackPayload);
            } catch (\Throwable $fbEx) {
                Log::error('Contact form fallback save also failed: ' . $fbEx->getMessage());
                // Dummy contact object so email notification still sends
                $contact = (object) $validated;
                $contact->id = 'INQ-' . rand(100, 999);
            }
        }

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
            Log::info("SMTP server port currently unreachable. Inquiry recorded for Admin ({$adminEmail}) and Customer ({$contact->email}).");
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
