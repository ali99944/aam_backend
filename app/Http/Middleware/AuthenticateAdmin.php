<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // Use 'admin' as the default guard for this middleware
        $guard = 'user';

        if (!Auth::guard($guard)->check()) {
            // If the user is not authenticated with the 'admin' guard,
            // redirect them to the admin login page.
            return redirect()->route('admin.login'); // <<< Ensure this route name is correct
        }

        // If authenticated, allow the request to proceed.
        return $next($request);
    }
}