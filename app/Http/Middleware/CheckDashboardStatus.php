<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::get('is_dashboard_disabled') === '1') {
            $user = Auth::guard('admin')->user();
            
            // If they are logged in but not Super Admin
            if ($user && ! $user->hasRole('Super Admin')) {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login.page')->with('error_message', __('The dashboard is currently disabled for maintenance. Only Super Administrators can access it.'));
            }
        }

        return $next($request);
    }
}
