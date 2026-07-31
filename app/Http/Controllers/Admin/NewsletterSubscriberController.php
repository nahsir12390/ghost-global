<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewsletterCampaignMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of newsletter subscribers
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        // Search by email
        if ($request->has('search') && $request->search) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Filter by subscription status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['email', 'is_active', 'subscribed_at', 'created_at'])) {
            $query->orderBy($sortField, $sortDir);
        }

        $subscribers = $query->paginate(50);

        return view('admin.newsletter-subscribers.index', [
            'subscribers' => $subscribers,
            'totalSubscribers' => NewsletterSubscriber::count(),
            'activeSubscribers' => NewsletterSubscriber::where('is_active', true)->count(),
            'inactiveSubscribers' => NewsletterSubscriber::where('is_active', false)->count(),
        ]);
    }

    /**
     * Toggle subscriber status
     */
    public function toggleStatus(NewsletterSubscriber $subscriber)
    {
        $subscriber->update([
            'is_active' => !$subscriber->is_active,
        ]);

        return back()->with('success', 'Subscriber status updated successfully.');
    }

    /**
     * Delete a subscriber
     */
    public function destroy(NewsletterSubscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return back()->with('success', "Subscriber {$email} has been deleted.");
    }

    /**
     * Delete multiple subscribers
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:newsletter_subscribers,id',
        ]);

        NewsletterSubscriber::whereIn('id', $ids['ids'])->delete();

        return back()->with('success', count($ids['ids']) . ' subscriber(s) deleted successfully.');
    }

    /**
     * Export subscribers to CSV
     */
    public function export()
    {
        $subscribers = NewsletterSubscriber::all();

        $csvFileName = 'newsletter_subscribers_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$csvFileName}",
        ];

        $columns = ['Email', 'Status', 'Subscribed At', 'Created At'];
        $callback = function () use ($subscribers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->is_active ? 'Active' : 'Inactive',
                    $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send a newsletter campaign to all active subscribers.
     */
    public function sendNewsletter(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:20000',
        ], [
            'subject.required' => 'Please enter an email subject.',
            'message.required' => 'Please enter the newsletter message.',
        ]);

        $activeSubscribersQuery = NewsletterSubscriber::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->orderBy('id');

        $activeSubscribersCount = (clone $activeSubscribersQuery)->count();

        if ($activeSubscribersCount === 0) {
            return back()
                ->withInput()
                ->with('error', 'There are no active subscribers to send this newsletter to.');
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $sentCount = 0;
        $failedCount = 0;

        $activeSubscribersQuery->chunkById(25, function ($subscribers) use ($validated, &$sentCount, &$failedCount) {
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email)->send(
                        new AdminNewsletterCampaignMail(
                            emailSubject: $validated['subject'],
                            messageBody: $validated['message']
                        )
                    );

                    $sentCount++;
                } catch (\Throwable $exception) {
                    report($exception);
                    $failedCount++;
                }
            }
        });

        $statusMessage = "Newsletter sent to {$sentCount} active subscriber(s).";

        if ($failedCount > 0) {
            $statusMessage .= " {$failedCount} email(s) could not be delivered.";
        }

        return back()->with('success', $statusMessage);
    }
}



