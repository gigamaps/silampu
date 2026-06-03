@extends('layouts.dashboard')

@section('page_title', 'Pengguna Aktif')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Pengguna Online</h6>
            <p class="text-xs text-slate-400 font-medium mt-1">Menampilkan aktivitas dalam 15 menit terakhir.</p>
        </div>
        <div class="flex items-center gap-3 self-end sm:self-auto">
            <div class="inline-flex items-center px-3 py-1.5 bg-slate-100 rounded-xl text-xs font-bold text-slate-600 border border-slate-200">
                <i class="bi bi-arrow-clockwise mr-1.5 animate-spin" style="animation-duration: 3s;"></i>
                Refresh dalam <span id="countdownTimer" class="text-secondary mx-1">30</span>s
            </div>

            <div class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full font-bold text-sm border border-emerald-100 shadow-sm">
                <span class="relative flex h-2.5 w-2.5 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                {{ $activeUsers->total() }} Online
            </div>
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route('admin.monitoring.active_users') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau username..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route('admin.monitoring.active_users') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Pengguna</th>
                    <th class="p-4 border-b border-slate-200">Role</th>
                    <th class="p-4 border-b border-slate-200">Unit / Kelas</th>
                    <th class="p-4 border-b border-slate-200">Perangkat / Browser</th>
                    <th class="p-4 border-b border-slate-200 text-center">Aktivitas Terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($activeUsers as $index => $user)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $activeUsers->firstItem() + $index }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="" class="w-9 h-9 rounded-full object-cover border border-slate-200 bg-slate-100" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=E2E8F0&color=475569'">
                            <div>
                                <span class="font-bold text-slate-800 block leading-tight">{{ $user->nama_lengkap }}</span>
                                <span class="text-xs font-medium text-slate-400"><i class="bi bi-at"></i>{{ $user->username }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        @if($user->role == 'admin') <span class="px-2.5 py-1 rounded-md bg-red-50 text-red-600 font-bold text-xs">Admin</span>
                        @elseif($user->role == 'kurikulum') <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-600 font-bold text-xs">Kurikulum</span>
                        @elseif($user->role == 'guru') <span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-600 font-bold text-xs">Guru</span>
                        @else <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 font-bold text-xs">Siswa</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($user->role == 'siswa')
                        <span class="font-bold text-slate-700 block">{{ $user->studentClass->nama_kelas ?? '-' }}</span>
                        <span class="text-xs text-slate-500 font-medium">{{ $user->studentClass->unit->nama_unit ?? '-' }}</span>
                        @elseif($user->role == 'kurikulum' && $user->units->isNotEmpty())
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wide bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $user->units->first()->nama_unit }}</span>
                        @else
                        <span class="text-slate-400 italic text-xs">-</span>
                        @endif
                    </td>

                    <td class="p-4">
                        @php
                        $ua = $user->user_agent ?? '';
                        $os = 'Unknown OS';
                        $browser = 'Unknown Browser';
                        $icon = 'bi-laptop';

                        // Deteksi OS
                        if (preg_match('/windows/i', $ua)) { $os = 'Windows'; $icon = 'bi-windows text-blue-500'; }
                        elseif (preg_match('/macintosh|mac os x/i', $ua)) { $os = 'Mac OS'; $icon = 'bi-apple text-slate-800'; }
                        elseif (preg_match('/android/i', $ua)) { $os = 'Android'; $icon = 'bi-android2 text-emerald-500'; }
                        elseif (preg_match('/iphone|ipad/i', $ua)) { $os = 'iOS'; $icon = 'bi-apple text-slate-800'; }
                        elseif (preg_match('/linux/i', $ua)) { $os = 'Linux'; $icon = 'bi-ubuntu text-orange-500'; }

                        // Deteksi Browser
                        if (preg_match('/chrome/i', $ua) && !preg_match('/edge|edg/i', $ua)) { $browser = 'Google Chrome'; }
                        elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) { $browser = 'Safari'; }
                        elseif (preg_match('/firefox/i', $ua)) { $browser = 'Mozilla Firefox'; }
                        elseif (preg_match('/edge|edg/i', $ua)) { $browser = 'Microsoft Edge'; }
                        @endphp

                        @if($ua)
                        <div class="flex items-center gap-2" title="{{ $ua }}">
                            <i class="bi {{ $icon }} text-base"></i>
                            <div>
                                <span class="font-semibold text-slate-700 block leading-tight text-xs">{{ $os }}</span>
                                <span class="text-[0.7rem] text-slate-400 font-medium">{{ $browser }}</span>
                            </div>
                        </div>
                        @else
                        <span class="text-slate-400 italic text-xs">Tidak ada data sesi</span>
                        @endif
                    </td>

                    <td class="p-4 text-center">
                        <span class="font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full text-xs border border-emerald-100 inline-flex items-center gap-1">
                            <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::parse($user->last_seen_at)->diffForHumans() }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400">
                        <i class="bi bi-broadcast text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Tidak ada pengguna lain yang sedang aktif saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $activeUsers->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 30;
        const timerElement = document.getElementById('countdownTimer');

        const countdown = setInterval(() => {
            timeLeft--;
            if (timerElement) {
                timerElement.textContent = timeLeft;
            }

            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = window.location.pathname + window.location.search;
            }
        }, 1000);
    });
</script>
@endsection