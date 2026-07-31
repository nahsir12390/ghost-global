<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var array<int, string>
     */
    private array $providers = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        try {
            return Socialite::driver($provider)->redirect();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('status', ucfirst($provider).' login is not configured yet.');
        }
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('status', 'Social login could not be completed. Please try again.');
        }

        if (blank($socialUser->getEmail())) {
            return redirect()
                ->route('login')
                ->with('status', ucfirst($provider).' did not return an email address. Please use email registration.');
        }

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        if ($user) {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_avatar' => $socialUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: Str::before($socialUser->getEmail(), '@'),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'customer',
                'referral_code' => User::generateUniqueReferralCode(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(),
            ]);

            app(ReferralService::class)->assignReferrer($user);
        }

        Auth::login($user, true);

        return redirect()->intended(route($user->dashboardRouteName()));
    }
}
