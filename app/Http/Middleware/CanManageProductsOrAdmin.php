<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageProductsOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Allow admins
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Allow staff with product manager permission
        if ($user->isStaff() && $user->isProductManager()) {
            return $next($request);
        }

        abort(403, 'You do not have permission to manage products.');
    }
}
