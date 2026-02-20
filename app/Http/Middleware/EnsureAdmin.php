<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Must be logged in and either a super-admin or a staff member with a role
        if (!$user || (!$user->is_admin && !$user->role_id)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}
