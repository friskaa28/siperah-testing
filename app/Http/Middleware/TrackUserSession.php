<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSession;
use Illuminate\Support\Str;

class TrackUserSession
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $sessionKey = 'kpi_session_id';

        if (!$request->session()->has($sessionKey)) {
            $token = \Illuminate\Support\Str::random(40);
            $session = UserSession::create([
                'user_id'       => $user->iduser,
                'login_at'      => now(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'session_token' => $token,
            ]);
            $request->session()->put($sessionKey, $session->id);
            $request->session()->put('kpi_session_token', $token);
        }

        return $next($request);
    }
}
