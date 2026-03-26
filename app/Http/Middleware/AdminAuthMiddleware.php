<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login.page')->with('error_message', 'Invalid email or password');
        }

        if (Auth::guard('admin')->user()->status === 'inactive') {
            Auth::guard('admin')->logout();

            return redirect()->route('admin.login.page')->with('error_message', 'Your account is inactive.');
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
