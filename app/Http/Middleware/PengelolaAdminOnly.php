<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PengelolaAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || (!auth()->user()->isPengelola() && !auth()->user()->isAdmin() && !auth()->user()->isSubPenampung())) {
            return redirect('/login')->withErrors(['error' => 'Anda harus login sebagai pengelola, admin, atau sub-penampung.']);
        }

        return $next($request);
    }
}
