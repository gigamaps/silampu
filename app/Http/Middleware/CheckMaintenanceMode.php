<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting; // Pastikan model Setting di-import

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil status maintenance dari database
        $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value');

        // Jika maintenance mode AKTIF (bernilai 1)
        if ($maintenanceMode == '1') {

            // 1. Pengecualian: Biarkan halaman Login dan aksi Logout tetap bisa diakses
            if ($request->is('login') || $request->routeIs('login') || $request->routeIs('logout')) {
                return $next($request);
            }

            // 2. Pengecualian: Jika dia sudah login DAN rolenya adalah 'admin', biarkan masuk!
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            // 3. Jika bukan login dan bukan admin, tampilkan halaman 503 Maintenance
            return response()->view('errors.503', [], 503);
        }

        // Jika maintenance mode mati (0), jalankan web secara normal
        return $next($request);
    }
}
