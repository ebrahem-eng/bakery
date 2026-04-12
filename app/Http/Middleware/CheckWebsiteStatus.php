<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWebsiteStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If website is disabled AND user is NOT an authenticated admin AND request is not for admin panel
        if (Setting::get('is_website_disabled') === '1' && ! auth()->guard('admin')->check() && ! $request->is('admin*')) {
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
