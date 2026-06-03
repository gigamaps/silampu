@extends('layouts.dashboard')

@section('page_title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-secondary/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-20 w-24 h-24 bg-blue-400/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-2">Selamat Datang, {{ auth()->user()->nama_lengkap }}! 👋</h2>
            <p class="text-slate-500 font-medium">Ini adalah pusat kendali administrator sistem Blended Learning {{ $globalSettings['app_name'] ?? 'SILAMPU' }}.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Siswa</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ \App\Models\User::where('role', 'siswa')->count() }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0">
                <i class="bi bi-person-video3"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Guru</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ \App\Models\User::where('role', 'guru')->count() }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0">
                <i class="bi bi-door-open-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas Aktif</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ \App\Models\Classes::count() }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0">
                <i class="bi bi-buildings-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Unit Sekolah</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ \App\Models\Unit::count() }}</h3>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800">Akses Cepat</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm m-0 group-hover:text-blue-600 transition-colors">Tambah Pengguna</p>
                        <p class="text-xs text-slate-500 m-0">Kelola data siswa & guru</p>
                    </div>
                </a>

                <a href="{{ route('admin.classes.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i class="bi bi-journal-plus"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm m-0 group-hover:text-amber-600 transition-colors">Kelola Kelas</p>
                        <p class="text-xs text-slate-500 m-0">Atur ruang kelas & rombel</p>
                    </div>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-slate-600 group-hover:text-white transition-colors">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm m-0 group-hover:text-slate-700 transition-colors">Pengaturan Sistem</p>
                        <p class="text-xs text-slate-500 m-0">Konfigurasi web SILAMPU</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800">Aktivitas Terakhir Sistem</h3>
                <a href="{{ route('admin.monitoring.activity_logs') }}" class="text-sm font-semibold text-secondary hover:text-blue-700">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[0.7rem]">
                            <th class="pb-3 font-bold">Waktu</th>
                            <th class="pb-3 font-bold">Pengguna</th>
                            <th class="pb-3 font-bold">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                        // Mengambil 5 log terakhir langsung dari DB
                        $recentLogs = \App\Models\ActivityLog::with('user')->latest()->take(5)->get();
                        @endphp

                        @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                        {{ substr($log->user->nama_lengkap ?? '?', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-slate-700">{{ $log->user->nama_lengkap ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $log->description }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400">Belum ada aktivitas tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection