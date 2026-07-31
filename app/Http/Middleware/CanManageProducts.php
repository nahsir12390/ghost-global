<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageProducts
{
    /**
     * Handle an incoming request - only allow those who can manage products
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow admins and product managers
        if ($user->isAdmin() || $user->isProductManager()) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Product management permission required.');
    }
}
