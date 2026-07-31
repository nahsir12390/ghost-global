<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user()?->fresh();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        // Allow staff members to proceed
        if ($user->isStaff()) {
            return $next($request);
        }

        if ($user->isVendor() && !$user->isVendorVerified()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Your vendor account must be verified before you can publish or update products.');
        }

        return $next($request);
    }
}
