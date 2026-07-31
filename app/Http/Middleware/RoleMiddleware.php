<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }

            if ($role === 'vendor' && $user->isVendor()) {
                return $next($request);
            }

            if ($role === 'customer' && $user->isCustomer()) {
                return $next($request);
            }

            // Allow staff members to access routes based on their permissions
            if ($role === 'staff' && $user->isStaff()) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}
