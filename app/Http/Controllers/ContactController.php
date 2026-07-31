<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactSubmissionMail;
use App\Mail\ContactConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:5000',
            ], [
                'name.required' => 'Please provide your full name.',
                'name.max' => 'Name must not exceed 255 characters.',
                'email.required' => 'Please provide your email address.',
                'email.email' => 'Please provide a valid email address.',
                'subject.required' => 'Please provide a subject for your message.',
                'subject.max' => 'Subject must not exceed 255 characters.',
                'message.required' => 'Please provide a message.',
                'message.min' => 'Message must be at least 10 characters.',
                'message.max' => 'Message must not exceed 5000 characters.',
            ]);

            // Get admin email from settings
            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));
            $siteName = SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));

            // Validate admin email
            if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid admin email in settings: ' . $adminEmail);
                $adminEmail = config('mail.from.address');
            }

            Log::info('Contact form submission', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'admin_email' => $adminEmail,
            ]);

            // Send email to admin with retry logic for rate limiting
            $maxRetries = 3;
            $retryDelay = 2000; // 2 seconds between retries
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    Mail::to($adminEmail)->send(new ContactSubmissionMail(
                        name: $validated['name'],
                        email: $validated['email'],
                        subject: $validated['subject'],
                        message: $validated['message'],
                        siteName: $siteName
                    ));
                    Log::info('Admin notification sent successfully');
                    break;
                } catch (\Symfony\Component\Mailer\Exception\UnexpectedResponseException $e) {
                    if ($attempt < $maxRetries && strpos($e->getMessage(), '550') !== false) {
                        Log::warning("Email rate limit hit, retrying... (Attempt $attempt/$maxRetries)");
                        usleep($retryDelay * 1000);
                    } else {
                        throw $e;
                    }
                }
            }

            // Send confirmation email to user with significant delay to avoid rate limits
            // Only attempt after admin email succeeds
            try {
                usleep(3000000); // 3 second delay before sending user confirmation
                Mail::to($validated['email'])->send(new ContactConfirmationMail(
                    name: $validated['name'],
                    siteName: $siteName
                ));
                Log::info('User confirmation sent successfully');
            } catch (\Exception $e) {
                Log::warning('User confirmation email failed: ' . $e->getMessage());
                // Don't throw - user will still see success message even if confirmation email fails
            }

            return redirect()->route('contact')->with('success', 'Thank you for contacting us! We will get back to you shortly.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Contact form validation failed: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('contact')->with('error', 'There was an error sending your message. Please try again later.');
        }
    }
}
