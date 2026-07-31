<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Mail\VendorVerificationReceivedMail;
use App\Mail\VendorVerificationSubmittedMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VendorUpgradeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isVendor()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Your account is already registered as a vendor.');
        }

        return view('vendor-upgrade.create', ['user' => $user]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isVendor()) {
            return redirect()->route('admin.dashboard');
        }

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'store_description' => ['nullable', 'string', 'max:1000'],
            'store_whatsapp' => ['nullable', 'string', 'max:30'],
            'store_instagram' => ['nullable', 'string', 'max:255'],
            'store_facebook' => ['nullable', 'string', 'max:255'],
            'store_website' => ['nullable', 'url', 'max:255'],
            'verification_email' => ['required', 'email', 'max:255'],
            'verification_phone' => ['required', 'string', 'max:20'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'min:10', 'max:34'],
        ]);

        $user->update([
            'role' => 'vendor',
            'store_name' => $validated['store_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'store_description' => $validated['store_description'] ?? null,
            'store_whatsapp' => $validated['store_whatsapp'] ?? null,
            'store_instagram' => $validated['store_instagram'] ?? null,
            'store_facebook' => $validated['store_facebook'] ?? null,
            'store_website' => $validated['store_website'] ?? null,
            'verification_email' => $validated['verification_email'],
            'verification_phone' => $validated['verification_phone'],
            'verification_nin' => null,
            'bank_name' => $validated['bank_name'],
            'bank_account_name' => $validated['bank_account_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_verification_status' => 'pending',
            'verification_status' => 'pending',
            'verification_submitted_at' => now(),
            'verification_notes' => null,
            'verified_at' => null,
            'vendor_is_active' => true,
        ]);

        $this->notifyStakeholders($user->fresh());

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Your vendor request has been submitted. Admin will review and approve it.');
    }

    private function notifyStakeholders(User $vendor): void
    {
        try {
            $adminEmails = User::where('is_admin', true)->pluck('email')->filter()->all();

            if (empty($adminEmails)) {
                $fallbackEmail = SettingsHelper::get('site_email', config('mail.from.address'));
                $adminEmails = $fallbackEmail ? [$fallbackEmail] : [];
            }

            if (! empty($adminEmails)) {
                Mail::to($adminEmails)->send(new VendorVerificationSubmittedMail($vendor));
            }

            if ($vendor->email) {
                Mail::to($vendor->email)->send(new VendorVerificationReceivedMail($vendor));
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to send vendor upgrade request notification.', [
                'vendor_id' => $vendor->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
