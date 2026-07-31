<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterSubscriptionConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'newsletter_email' => 'required|email:rfc,dns|max:255',
        ], [
            'newsletter_email.required' => 'Please enter your email address.',
            'newsletter_email.email' => 'Please enter a valid email address.',
        ]);

        $email = strtolower(trim($validated['newsletter_email']));

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber && $subscriber->is_active) {
            return back()->with('success', 'You are already subscribed to our newsletter.');
        }

        if ($subscriber && !$subscriber->is_active) {
            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
            ]);

            // Send reactivation email
            Mail::send(new NewsletterSubscriptionConfirmation($subscriber, isReactivation: true));

            return back()->with('success', 'Welcome back. Your newsletter subscription is active again. A confirmation email has been sent.');
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        // Send confirmation email
        Mail::send(new NewsletterSubscriptionConfirmation($subscriber));

        return back()->with('success', 'Subscription successful. Please check your email for confirmation.');
    }
}
