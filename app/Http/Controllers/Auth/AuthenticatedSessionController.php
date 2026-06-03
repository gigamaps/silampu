<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\ActivityLogger; // Import Helper Log

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // 1. Ambil data user yang baru login
        $user = $request->user();

        // 2. Catat riwayat login ke log aktivitas sistem
        ActivityLogger::log('login', 'Berhasil masuk ke dalam sistem platform');

        // 3. Arahkan ke rute dashboard masing-masing dengan membawa pesan flash sukses
        return match ($user->role) {
            'admin'     => redirect()->intended(route('admin.dashboard'))->with('success', "Selamat datang kembali, {$user->nama_lengkap}! 👋"),
            'kurikulum' => redirect()->intended(route('kurikulum.dashboard'))->with('success', "Selamat datang kembali, {$user->nama_lengkap}! 👋"),
            'guru'      => redirect()->intended(route('guru.dashboard'))->with('success', "Selamat datang kembali, {$user->nama_lengkap}! 👋"),
            'siswa'     => redirect()->intended(route('siswa.dashboard'))->with('success', "Selamat datang kembali, {$user->nama_lengkap}! 👋"),
            default     => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Catat aktivitas sebelum logout dihancurkan
        if (Auth::check()) {
            ActivityLogger::log('logout', 'Keluar dari sistem platform');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
