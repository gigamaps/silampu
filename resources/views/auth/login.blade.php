<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Sistem - {{ $globalSettings['app_name'] ?? 'SILAMPU' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/app/' . ($globalSettings['app_favicon'] ?? 'favicon.ico')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-primary via-[#0f243e] to-[#1e3a8a] text-slate-900 font-['Plus_Jakarta_Sans'] antialiased min-h-screen flex flex-col justify-center items-center p-4 relative overflow-hidden">

    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-80 h-80 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 space-y-6">

        <div class="text-center space-y-3">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group text-decoration-none justify-center">
                <div class="p-2 bg-slate-800/40 rounded-xl border border-white/5 shadow-lg">
                    <img src="{{ asset('img/app/' . ($globalSettings['app_logo'] ?? 'logo.png')) }}" alt="Logo" class="h-8 w-auto object-contain">
                </div>
                @php
                $nama_app = $globalSettings['app_name'] ?? 'SILAMPU';
                $part1 = substr($nama_app, 0, 2);
                $part2 = substr($nama_app, 2);
                @endphp
                <div class="flex items-center text-2xl tracking-tight">
                    <span class="text-secondary font-black text-3xl tracking-tighter">{{ $part1 }}</span>
                    <span class="text-white font-bold ml-0.5">{{ $part2 }}</span>
                </div>
            </a>
            <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Portal Blended Learning Terintegrasi</p>
        </div>

        <div class="bg-white/95 backdrop-blur-md rounded-2xl border border-white/10 shadow-2xl p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Selamat Datang Kembali</h2>
                <p class="text-xs text-slate-400 font-medium mt-1">Silakan masukkan akun kredensial Anda untuk mengakses sistem dashboard.</p>
            </div>

            @if (session('status'))
            <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs font-bold text-emerald-600">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Username / ID Akun</label>
                    <div class="relative">
                        <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Contoh: admin / nis_np" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('username') border-red-300 focus:ring-red-200 focus:border-red-400 @else border-slate-200 focus:ring-secondary/20 focus:border-secondary @enderror text-sm font-medium focus:ring-2 transition-all text-slate-800" required autofocus autocomplete="username">
                        <i class="bi bi-person absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                    </div>
                    @error('username')
                    <p class="text-xs font-semibold text-red-500 mt-1.5 flex items-center gap-1"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kata Sandi (Password)</label>
                        @if (Route::has('password.request'))
                        <!-- <a href="{{ route('password.request') }}" class="text-xs font-bold text-secondary hover:underline text-decoration-none">Lupa Password?</a> -->
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 rounded-xl border @error('password') border-red-300 focus:ring-red-200 focus:border-red-400 @else border-slate-200 focus:ring-secondary/20 focus:border-secondary @enderror text-sm font-medium focus:ring-2 transition-all text-slate-800" required autocomplete="current-password">
                        <i class="bi bi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>

                        <button type="button" id="btnTogglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1 transition-colors">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-xs font-semibold text-red-500 mt-1.5 flex items-center gap-1"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-secondary focus:ring-secondary/20 shadow-sm w-4 h-4">
                        <span class="ms-2 text-xs text-slate-500 font-semibold">Ingat Sesi Saya</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 bg-secondary hover:bg-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-secondary/20 transition-all gap-2">
                        <i class="bi bi-box-arrow-in-right text-base leading-none"></i> Login
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center text-xs font-medium text-slate-400/80">
            &copy; {{ date('Y') }} {{ $globalSettings['nama_yayasan'] ?? 'Cakrawala Foundation' }}.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnTogglePassword = document.getElementById('btnTogglePassword');
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (btnTogglePassword && passwordInput && passwordIcon) {
                btnTogglePassword.addEventListener('click', function() {
                    // Ambil tipe input saat ini, lalu balikkan kondisinya
                    const currentType = passwordInput.getAttribute('type');
                    const targetType = currentType === 'password' ? 'text' : 'password';

                    passwordInput.setAttribute('type', targetType);

                    // Ganti bentuk ikon mata (eye / eye-slash) sesuai tipe input
                    if (targetType === 'text') {
                        passwordIcon.className = 'bi bi-eye-slash';
                    } else {
                        passwordIcon.className = 'bi bi-eye';
                    }
                });
            }
        });
    </script>
</body>

</html>