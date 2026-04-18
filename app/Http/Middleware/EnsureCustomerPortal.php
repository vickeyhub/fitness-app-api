<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block staff (admin) from member app routes — they use /admin and /login.
 */
class EnsureCustomerPortal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
