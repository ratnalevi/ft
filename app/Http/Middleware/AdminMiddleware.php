<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and is an admin
        {
            $adminAccess = session('admin_access');
            if ($adminAccess != 1) {
                return redirect('/home');
            }
            return $next($request);
        }
    }
}
