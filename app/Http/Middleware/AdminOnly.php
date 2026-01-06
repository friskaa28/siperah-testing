<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/dashboard-pengelola')->withErrors(['error' => 'Hanya Admin Kantor yang dapat mengakses halaman ini.']);
        }

        return $next($request);
    }
}
