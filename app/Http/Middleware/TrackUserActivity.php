<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Update waktu terakhir dilihat setiap kali user mengakses halaman apapun
            User::where('id', Auth::id())->update(['last_seen_at' => now()]);
        }
        return $next($request);
    }
}
