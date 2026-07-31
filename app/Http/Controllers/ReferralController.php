<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;

class ReferralController extends Controller
{
    public function capture(string $code, ReferralService $referralService): RedirectResponse
    {
        $matched = $referralService->rememberReferralCode($code);

        return redirect()
            ->route('register')
            ->with($matched ? 'success' : 'error', $matched
                ? 'Referral applied successfully. Create your account to continue.'
                : 'That referral link is no longer valid.');
    }
}
