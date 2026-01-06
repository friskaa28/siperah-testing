<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PeternakOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isPeternak()) {
            return redirect('/login')->withErrors(['error' => 'Anda harus login sebagai peternak.']);
        }

        return $next($request);
    }
}
