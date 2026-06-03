<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('page_title', 'SILAMPU') - {{ $globalSettings['app_name'] ?? 'Blended Learning' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/app/' . ($globalSettings['app_favicon'] ?? 'favicon.ico')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans'] antialiased min-h-screen flex flex-col selection:bg-secondary selection:text-white">

    <!-- NAVBAR WELCOME -->
    <nav class="bg-primary border-b border-white/5 py-4 sticky top-0 z-50 backdrop-blur-md bg-primary/95">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group text-decoration-none">
                <div class="p-2 bg-slate-800/40 rounded-xl border border-white/5 transition-all duration-300 group-hover:border-secondary/30">
                    <img src="{{ asset('img/app/' . ($globalSettings['app_logo'] ?? 'logo.png')) }}" alt="Logo" class="h-7 w-auto object-contain">
                </div>
                @php
                $nama_app = $globalSettings['app_name'] ?? 'SILAMPU';
                $part1 = substr($nama_app, 0, 2);
                $part2 = substr($nama_app, 2);
                @endphp
                <div class="flex items-center text-xl tracking-tight">
                    <span class="text-secondary font-black text-2xl tracking-tighter">{{ $part1 }}</span>
                    <span class="text-white font-bold ml-0.5">{{ $part2 }}</span>
                </div>
            </a>

            <!-- DESKTOP MENU DENGAN ACTIVE CLASS -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ url('/') }}" class="text-sm font-semibold transition-colors text-decoration-none {{ request()->is('/') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                    Beranda
                </a>
                <a href="{{ route('katalog.index') }}" class="text-sm font-semibold transition-colors text-decoration-none {{ request()->routeIs('katalog.*') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                    Katalog Materi
                </a>
                <a href="{{ route('tentang') }}" class="text-sm font-semibold transition-colors text-decoration-none {{ request()->routeIs('tentang') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                    Tentang Platform
                </a>
            </div>

            <div class="hidden md:block">
                @auth
                @php
                $dashboardRoute = match(auth()->user()->role) {
                'admin' => route('admin.dashboard'),
                'kurikulum' => route('kurikulum.dashboard'),
                'guru' => route('guru.dashboard'),
                'siswa' => route('siswa.dashboard'),
                default => url('/'),
                };
                @endphp
                <a href="{{ $dashboardRoute }}" class="inline-flex items-center px-5 py-2.5 bg-secondary text-white font-bold text-sm rounded-xl hover:bg-blue-600 shadow-md shadow-secondary/20 transition-all text-decoration-none">
                    <i class="bi bi-grid-1x2 mr-2"></i> Panel Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 bg-secondary text-white font-bold text-sm rounded-xl hover:bg-blue-600 shadow-md shadow-secondary/20 transition-all text-decoration-none">
                    Login <i class="bi bi-arrow-right-short text-lg ml-1.5"></i>
                </a>
                @endauth
            </div>

            <button id="btnToggleMenu" class="md:hidden text-white text-2xl focus:outline-none p-1 hover:bg-slate-800 rounded-lg transition-colors">
                <i class="bi bi-list" id="menuIcon"></i>
            </button>
        </div>

        <!-- MOBILE MENU DENGAN ACTIVE CLASS -->
        <div id="mobileMenu" class="hidden bg-primary border-t border-white/5 px-6 py-4 space-y-3 md:hidden transition-all duration-300">
            <a href="{{ url('/') }}" class="block text-sm font-semibold py-2 text-decoration-none {{ request()->is('/') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                Beranda
            </a>
            <a href="{{ route('katalog.index') }}" class="block text-sm font-semibold py-2 text-decoration-none {{ request()->routeIs('katalog.*') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                Katalog Materi
            </a>
            <a href="{{ route('tentang') }}" class="block text-sm font-semibold py-2 text-decoration-none {{ request()->routeIs('tentang') ? 'text-secondary' : 'text-slate-400 hover:text-white' }}">
                Tentang Platform
            </a>
            <hr class="border-white/5 my-2">
            @auth
            <a href="{{ $dashboardRoute }}" class="block text-center w-full py-2.5 bg-secondary text-white font-bold text-sm rounded-xl text-decoration-none">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="block text-center w-full py-2.5 bg-secondary text-white font-bold text-sm rounded-xl text-decoration-none">Masuk Sistem</a>
            @endauth
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 flex flex-col w-full">
        @yield('content')
    </main>

    <!-- FOOTER WELCOME -->
    <footer class="bg-[#060D1A] text-slate-500 py-8 border-t border-white/5 mt-auto">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-medium">
                <div class="text-center md:text-left">
                    &copy; {{ date('Y') }} <span class="text-slate-400 font-semibold">{{ $globalSettings['nama_yayasan'] ?? 'Cakrawala Foundation' }}</span>. Hak Cipta Dilindungi.
                </div>
                <div class="flex items-center gap-6 justify-center">
                    <a href="#" class="hover:text-secondary transition-colors text-decoration-none">Pusat Bantuan</a>
                    <a href="#" class="hover:text-secondary transition-colors text-decoration-none">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-secondary transition-colors text-decoration-none">Ketentuan Layanan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const btnToggle = document.getElementById('btnToggleMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIcon = document.getElementById('menuIcon');

        if (btnToggle && mobileMenu) {
            btnToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                if (mobileMenu.classList.contains('hidden')) {
                    menuIcon.className = 'bi bi-list';
                } else {
                    menuIcon.className = 'bi bi-x-lg text-xl';
                }
            });
        }
    </script>
</body>

</html>