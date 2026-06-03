<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>@yield('page_title', 'Dashboard') | {{ $globalSettings['app_name'] ?? 'SILAMPU' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/app/' . ($globalSettings['app_favicon'] ?? 'favicon.ico')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans'] antialiased selection:bg-secondary selection:text-white">

    <div class="flex min-h-screen w-full relative">

        <div id="sidebarOverlay" class="fixed inset-0 bg-[#0B192C]/50 backdrop-blur-sm z-40 opacity-0 invisible transition-all duration-300 lg:hidden"></div>

        <aside id="sidebar" class="w-[280px] bg-primary text-white flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out border-r border-white/5">

            <div class="px-6 py-5 border-b border-slate-800 bg-[#06101E]/40 backdrop-blur-sm">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group text-decoration-none">
                    <div class="p-2 bg-slate-800/40 rounded-xl border border-white/5 transition-all duration-300 group-hover:border-secondary/30">
                        <img src="{{ asset('img/app/' . ($globalSettings['app_logo'] ?? 'logo.png')) }}" alt="Logo" class="h-8 w-auto object-contain">
                    </div>

                    @php
                    $nama_app = $globalSettings['app_name'] ?? 'SILAMPU';
                    $part1 = substr($nama_app, 0, 2);
                    $part2 = substr($nama_app, 2);
                    @endphp

                    <div class="flex flex-col justify-center">
                        <div class="flex items-center text-xl tracking-tight leading-none">
                            <span class="text-secondary font-black text-2xl tracking-tighter">{{ $part1 }}</span>
                            <span class="text-white font-bold ml-0.5">{{ $part2 }}</span>
                        </div>
                        <span class="text-[0.625rem] font-bold uppercase tracking-widest text-slate-400 mt-1.5 leading-none">Blended Learning</span>
                    </div>
                </a>
            </div>

            <ul class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">

                @if(auth()->user()->role === 'admin')
                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-2 mb-2 ml-3 block">Menu Utama</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-grid-1x2 text-lg mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Dashboard
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Data Master</li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.users.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-people text-lg mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Manajemen Pengguna
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.units.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.units.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-buildings text-lg mr-3 {{ request()->routeIs('admin.units.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Data Unit Sekolah
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.majors.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.majors.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-mortarboard text-lg mr-3 {{ request()->routeIs('admin.majors.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Jurusan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.classes.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.classes.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-door-open text-lg mr-3 {{ request()->routeIs('admin.classes.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Ruang Kelas
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.subjects.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.subjects.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-book text-lg mr-3 {{ request()->routeIs('admin.subjects.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Mata Pelajaran
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Sistem & Monitoring</li>
                <li>
                    <a href="{{ route('admin.monitoring.active_users') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.monitoring.active_users') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-broadcast text-lg mr-3 {{ request()->routeIs('admin.monitoring.active_users') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Pengguna Aktif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.monitoring.activity_logs') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.monitoring.activity_logs') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-activity text-lg mr-3 {{ request()->routeIs('admin.monitoring.activity_logs') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Log Aktivitas
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.monitoring.videos') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.monitoring.videos') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-camera-reels text-lg mr-3 {{ request()->routeIs('admin.monitoring.videos') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Monitoring Video
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-gear text-lg mr-3 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Konfigurasi Sistem
                    </a>
                </li>

                @elseif(auth()->user()->role === 'kurikulum')
                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-2 mb-2 ml-3 block">Menu Utama</li>
                <li>
                    <a href="{{ route('kurikulum.dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.dashboard') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-grid-1x2 text-lg mr-3 {{ request()->routeIs('kurikulum.dashboard') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Dashboard
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Struktur Akademik</li>
                <li>
                    <a href="{{ route('kurikulum.majors.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.majors.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-mortarboard text-lg mr-3 {{ request()->routeIs('kurikulum.majors.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Kelola Jurusan
                    </a>
                </li>
                <li>
                    <a href="{{ route('kurikulum.classes.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.classes.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-door-open text-lg mr-3 {{ request()->routeIs('kurikulum.classes.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Ruang Kelas
                    </a>
                </li>
                <li>
                    <a href="{{ route('kurikulum.subjects.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.subjects.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-book text-lg mr-3 {{ request()->routeIs('kurikulum.subjects.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Mata Pelajaran
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Monitoring Konten</li>
                <li>
                    <a href="{{ route('kurikulum.monitoring.videos') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.monitoring.videos') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-camera-reels text-lg mr-3 {{ request()->routeIs('kurikulum.monitoring.videos') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Monitoring Video
                    </a>
                </li>
                <li>
                    <a href="{{ route('kurikulum.monitoring.activity_logs') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('kurikulum.monitoring.activity_logs') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-activity text-lg mr-3 {{ request()->routeIs('kurikulum.monitoring.activity_logs') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Log Aktivitas
                    </a>
                </li>

                @elseif(auth()->user()->role === 'guru')
                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-2 mb-2 ml-3 block">Menu Utama</li>
                <li>
                    <a href="{{ route('guru.dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('guru.dashboard') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-grid-1x2 text-lg mr-3 {{ request()->routeIs('guru.dashboard') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Dashboard
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Akademik Kelas</li>
                <li>
                    <a href="{{ route('guru.subjects.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('guru.subjects.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-journal-bookmark-fill text-lg mr-3 {{ request()->routeIs('guru.subjects.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Mapel Diampu
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Produksi Konten</li>
                <li>
                    <a href="{{ route('guru.videos.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('guru.videos.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-camera-reels text-lg mr-3 {{ request()->routeIs('guru.videos.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Video Materi Saya
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Interaksi</li>
                <li>
                    <a href="{{ route('guru.forums.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('guru.forums.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-chat-square-text text-lg mr-3 {{ request()->routeIs('guru.forums.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Forum Diskusi
                    </a>
                </li>

                @elseif(auth()->user()->role === 'siswa')
                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-2 mb-2 ml-3 block">Menu Pelajar</li>
                <li>
                    <a href="{{ route('siswa.dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('siswa.dashboard') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-grid-1x2 text-lg mr-3 {{ request()->routeIs('siswa.dashboard') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Ruang Belajar
                    </a>
                </li>
                <li>
                    <a href="{{ route('siswa.videos.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('siswa.videos.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-collection-play text-lg mr-3 {{ request()->routeIs('siswa.videos.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Video Pembelajaran
                    </a>
                </li>

                <li class="text-[0.7rem] font-bold text-white/40 uppercase tracking-widest mt-6 mb-2 ml-3 block">Aktivitas Kelas</li>
                <li>
                    <a href="{{ route('siswa.forums.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('siswa.forums.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-chat-dots text-lg mr-3 {{ request()->routeIs('siswa.forums.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Forum Diskusi
                    </a>
                </li>
                <li>
                    <a href="{{ route('siswa.history.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-[0.95rem] font-medium {{ request()->routeIs('siswa.history.*') ? 'bg-secondary text-white shadow-lg shadow-secondary/30' : 'text-white/70 hover:bg-[#162a45] hover:text-white text-decoration-none' }}">
                        <i class="bi bi-bookmark-star text-lg mr-3 {{ request()->routeIs('siswa.history.*') ? 'text-white' : 'text-white/50 group-hover:text-white' }}"></i> Riwayat Tontonan
                    </a>
                </li>
                @endif

            </ul>

            <div class="p-5 border-t border-white/5">
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <a href="#" onclick="openLogoutModal(); event.preventDefault();" class="flex items-center px-4 py-3 text-red-500 rounded-xl hover:bg-red-500/10 transition-colors text-[0.95rem] font-medium text-decoration-none">
                        <i class="bi bi-box-arrow-right text-lg mr-3"></i> Keluar
                    </a>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 lg:ml-[280px]">

            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md px-5 lg:px-8 py-4 flex justify-between items-center border-b border-slate-200">
                <div class="flex items-center">
                    <button id="btnToggleSidebar" class="lg:hidden text-2xl text-primary p-1 mr-4 focus:outline-none rounded-md hover:bg-slate-100">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="hidden md:block m-0 font-bold text-lg text-slate-800">@yield('page_title', 'Dashboard')</h5>
                </div>

                <div class="relative" id="profileDropdownWrapper">
                    <div class="flex items-center gap-3 bg-white pl-4 pr-1.5 py-1.5 rounded-full border border-slate-200 shadow-sm cursor-pointer hover:border-secondary transition-colors" id="profileDropdownBtn">
                        <div class="hidden sm:block text-right">
                            <div class="font-bold text-sm leading-tight text-slate-800">{{ auth()->user()->nama_lengkap }}</div>
                            <div class="text-[0.65rem] text-slate-500 uppercase font-bold tracking-wider">{{ auth()->user()->role }}</div>
                        </div>

                        @if(auth()->user()->foto_profil && auth()->user()->foto_profil != 'default.jpg')
                        <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-9 h-9 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-sm object-cover">
                        @else
                        <div class="w-9 h-9 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-sm object-cover">
                            {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
                        </div>
                        @endif
                    </div>

                    <ul id="profileMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                        <li>
                            <!-- <a class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-secondary font-medium transition-colors text-decoration-none" href="#">
                                <i class="bi bi-person mr-2"></i> Profil Saya
                            </a> -->
                        </li>
                        <hr class="my-2 border-slate-100">
                        <li>
                            <a class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors text-decoration-none" href="#" onclick="openLogoutModal(); event.preventDefault();">
                                <i class="bi bi-box-arrow-right mr-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="p-5 lg:p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" id="toastContainer"></div>

    <div id="globalDeleteModal" class="fixed inset-0 z-[999] hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="deleteModalOverlay"></div>
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 p-6 text-center" id="deleteModalContent">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-red-100 animate-bounce" style="animation-duration: 2s;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h5 class="font-extrabold text-slate-800 text-lg mb-2">Konfirmasi Hapus Data</h5>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini akan terekam di log sistem dan data yang dihapus tidak dapat dikembalikan.</p>

            <div class="flex items-center gap-3 justify-center">
                <button type="button" id="btnCancelDelete" class="w-full px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">Batal</button>
                <button type="button" id="btnConfirmDelete" class="w-full px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-red-500 hover:bg-red-600 shadow-md shadow-red-500/20 transition-all">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div id="globalLogoutModal" class="fixed inset-0 z-[999] hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="logoutModalOverlay"></div>
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 p-6 text-center" id="logoutModalContent">
            <div class="w-16 h-16 bg-blue-50 text-secondary rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-blue-100">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <h5 class="font-extrabold text-slate-800 text-lg mb-2">Keluar dari Sistem?</h5>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">Sesi aktif Anda akan segera diakhiri. Pastikan semua pekerjaan Anda telah tersimpan dengan aman.</p>

            <div class="flex items-center gap-3 justify-center">
                <button type="button" id="btnCancelLogout" class="w-full px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">Batal</button>
                <button type="button" id="btnConfirmLogout" class="w-full px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-primary hover:bg-slate-800 shadow-md shadow-primary/20 transition-all">Keluar</button>
            </div>
        </div>
    </div>

    <script>
        const btnDropdown = document.getElementById('profileDropdownBtn');
        const menuDropdown = document.getElementById('profileMenu');

        btnDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            menuDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            if (!menuDropdown.classList.contains('hidden')) {
                menuDropdown.classList.add('hidden');
            }
        });

        const btnToggle = document.getElementById('btnToggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('opacity-0');
            overlay.classList.toggle('invisible');
        }

        if (btnToggle) btnToggle.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');

            const baseClass = "bg-white rounded-xl p-4 shadow-xl border-l-4 flex items-start gap-3 w-[350px] transform translate-x-[120%] transition-transform duration-300 pointer-events-auto";
            const typeClass = type === 'error' ? 'border-red-500' : 'border-secondary';
            toast.className = `${baseClass} ${typeClass}`;

            const iconClass = type === 'error' ? 'bi-x-circle-fill text-red-500' : 'bi-check-circle-fill text-secondary';

            toast.innerHTML = `
                <i class="bi ${iconClass} text-xl shrink-0 mt-0.5"></i>
                <div class="flex-grow">
                    <div class="font-bold text-[0.95rem] text-slate-900 mb-1">${title}</div>
                    <p class="text-[0.85rem] text-slate-500 m-0 leading-snug">${message}</p>
                </div>
                <button class="text-slate-400 hover:text-slate-600 transition-colors" onclick="this.parentElement.remove()">
                    <i class="bi bi-x text-xl"></i>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-[120%]');
                toast.classList.add('translate-x-0');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-[120%]');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        let formActiveToDelete = null;

        function openLogoutModal() {
            const modal = document.getElementById('globalLogoutModal');
            const overlay = document.getElementById('logoutModalOverlay');
            const content = document.getElementById('logoutModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeLogoutModal() {
            const modal = document.getElementById('globalLogoutModal');
            const overlay = document.getElementById('logoutModalOverlay');
            const content = document.getElementById('logoutModalContent');

            overlay.classList.add('opacity-0');
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        document.getElementById('btnCancelLogout').addEventListener('click', closeLogoutModal);
        document.getElementById('logoutModalOverlay').addEventListener('click', closeLogoutModal);
        document.getElementById('btnConfirmLogout').addEventListener('click', function() {
            document.getElementById('logoutForm').submit();
        });

        function openDeleteModal() {
            const modal = document.getElementById('globalDeleteModal');
            const overlay = document.getElementById('deleteModalOverlay');
            const content = document.getElementById('deleteModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                content.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('globalDeleteModal');
            const overlay = document.getElementById('deleteModalOverlay');
            const content = document.getElementById('deleteModalContent');

            overlay.classList.add('opacity-0');
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                formActiveToDelete = null;
            }, 300);
        }

        document.addEventListener('click', function(e) {
            const deleteButton = e.target.closest('button[type="submit"]') || e.target.closest('input[type="submit"]');

            if (deleteButton) {
                const form = deleteButton.closest('form');
                if (form) {
                    const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');

                    if (methodInput && !form.hasAttribute('data-verified-delete')) {
                        e.preventDefault();
                        e.stopPropagation();

                        formActiveToDelete = form;
                        openDeleteModal();
                    }
                }
            }
        });

        document.getElementById('btnCancelDelete').addEventListener('click', closeDeleteModal);
        document.getElementById('deleteModalOverlay').addEventListener('click', closeDeleteModal);
        document.getElementById('btnConfirmDelete').addEventListener('click', function() {
            if (formActiveToDelete) {
                formActiveToDelete.setAttribute('data-verified-delete', 'true');
                formActiveToDelete.submit();
            }
        });
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => showToast('success', 'Berhasil!', '{{ session("success") }}'));
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => showToast('error', 'Validasi Gagal!', '{{ $errors->first() }}'));
    </script>
    @endif

</body>

</html>