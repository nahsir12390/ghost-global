<?php

namespace App\Observers;

use App\Mail\WelcomeUserMail;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            if ($user->email && config('mail.default') !== 'log') {
                Mail::to($user->email)->send(new WelcomeUserMail($user));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send welcome email: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // If email changed, update newsletter subscriber
        if ($user->isDirty('email')) {
            try {
                $oldEmail = strtolower(trim($user->getOriginal('email')));
                $newEmail = strtolower(trim($user->email));

                $oldSubscriber = NewsletterSubscriber::where('email', $oldEmail)->first();
                if ($oldSubscriber) {
                    $oldSubscriber->update(['email' => $newEmail]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to update newsletter subscriber email: '.$e->getMessage(), [
                    'user_id' => $user->id,
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Optionally handle user deletion - you can choose to deactivate or remove newsletter subscription
        // For now, we'll just deactivate the subscriber instead of deleting
        try {
            $email = strtolower(trim($user->email));
            $subscriber = NewsletterSubscriber::where('email', $email)->first();

            if ($subscriber) {
                $subscriber->update(['is_active' => false]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to deactivate newsletter subscriber on user deletion: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
    }
}
