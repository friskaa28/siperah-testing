<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PengelolaAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || (!auth()->user()->isPengelola() && !auth()->user()->isAdmin())) {
            return redirect('/login')->withErrors(['error' => 'Anda harus login sebagai pengelola atau admin.']);
        }

        return $next($request);
    }
}
