<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffOnly
{
    /**
     * Handle an incoming request - only allow staff members
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow admins and staff members
        if ($user->isAdmin() || $user->isStaff()) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Staff access only.');
    }
}
