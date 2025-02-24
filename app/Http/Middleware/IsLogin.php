<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IsLogin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->expectsJson()) {
            // Use ONLY Sanctum for API authentication
            if (Auth::guard('sanctum')->check() && !$request->user()) {
                return response()->json(['error' => 'You are unauthorized to access the API.'], 401);
            }

            return $next($request);
        }

        // Web authentication (session-based)
        if (session('userID')) {
            return $next($request);
        }

        return redirect('/login');
    }

}
