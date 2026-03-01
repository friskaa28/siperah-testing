<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AnalyticsOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAnalytics()) {
            // Redirect to appropriate dashboard if authenticated with another role
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->isPeternak()) {
                    return redirect('/dashboard-peternak');
                }
                return redirect('/dashboard-pengelola');
            }
            return redirect('/login')->withErrors(['error' => 'Anda harus login sebagai Tim Analytics.']);
        }

        return $next($request);
    }
}
