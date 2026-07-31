<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterSubscriptionConfirmation;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscribe extends Component
{
    public $newsletter_email = '';
    public $success = false;
    public $error = '';
    public $message = '';

    protected $rules = [
        'newsletter_email' => 'required|email:rfc,dns|max:255',
    ];

    protected $messages = [
        'newsletter_email.required' => 'Please enter your email address.',
        'newsletter_email.email' => 'Please enter a valid email address.',
        'newsletter_email.max' => 'Email address is too long.',
    ];

    public function subscribe()
    {
        $this->validate();

        $email = strtolower(trim($this->newsletter_email));

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber && $subscriber->is_active) {
            $this->error = 'You are already subscribed to our newsletter.';
            $this->success = false;
            $this->message = '';
            return;
        }

        if ($subscriber && !$subscriber->is_active) {
            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
            ]);
            
            $this->success = true;
            $this->error = '';
            $this->message = 'Welcome back! Your newsletter subscription is active again.';
            $this->newsletter_email = '';
            
            // Send confirmation email for reactivation
            Mail::send(new NewsletterSubscriptionConfirmation($subscriber, isReactivation: true));
            
            // Flash message for session persistence
            session()->flash('success', 'Welcome back. Your newsletter subscription is active again. A confirmation email has been sent.');
            return;
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        $this->success = true;
        $this->error = '';
        $this->message = 'Subscription successful! You will receive updates on new arrivals and offers.';
        $this->newsletter_email = '';
        
        // Send confirmation email
        Mail::send(new NewsletterSubscriptionConfirmation($subscriber));
        
        // Flash message for session persistence
        session()->flash('success', 'Subscription successful. Please check your email for confirmation.');
    }

    public function render()
    {
        return view('livewire.newsletter-subscribe');
    }
}