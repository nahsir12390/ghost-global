<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageOrders
{
    /**
     * Handle an incoming request - only allow those who can manage orders
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow admins and order managers
        if ($user->isAdmin() || $user->isOrderManager()) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Order management permission required.');
    }
}
