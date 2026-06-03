@extends('layouts.dashboard')

@section('page_title', 'Log Aktivitas Sistem')

@section('content')
@php
$prefix = auth()->user()->role === 'kurikulum' ? 'kurikulum' : 'admin';
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Riwayat Aktivitas Pengguna</h6>
            <p class="text-xs text-slate-400 font-medium mt-1">Merekam semua tindakan penting di dalam sistem secara real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
            <div class="inline-flex items-center px-3 py-1.5 bg-slate-100 rounded-xl text-xs font-bold text-slate-600 border border-slate-200">
                <i class="bi bi-arrow-clockwise mr-1.5 animate-spin" style="animation-duration: 3s;"></i>
                Refresh dalam <span id="countdownTimer" class="text-secondary mx-1">30</span>s
            </div>

            @if(auth()->user()->role === 'admin')
            <form action="{{ route('admin.monitoring.activity_logs.clear') }}" method="POST" class="inline m-0" onsubmit="return confirm('Tindakan ini akan menghapus semua riwayat aktivitas yang umurnya lebih dari 30 hari. Yakin ingin melanjutkan?');">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl hover:bg-amber-600 hover:text-white font-semibold text-sm transition-colors">
                    <i class="bi bi-stars mr-2"></i> Bersihkan Log Lama (>30 Hari)
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route($prefix . '.monitoring.activity_logs') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' || !request()->has('per_page') ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, atau nama..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route($prefix . '.monitoring.activity_logs') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-150 w-44">Waktu</th>
                    <th class="p-4 border-b border-slate-200 w-56">Pengguna</th>
                    <th class="p-4 border-b border-slate-200 w-48">Aksi (Kode)</th>
                    <th class="p-4 border-b border-slate-250">Deskripsi Detail</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 whitespace-nowrap">
                        <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                        <div class="text-xs text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB</div>
                    </td>
                    <td class="p-4">
                        @if($log->user)
                        <span class="font-bold text-slate-700 block leading-tight">{{ $log->user->nama_lengkap }}</span>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $log->user->role }}</span>
                        @else
                        <span class="text-slate-400 italic text-xs">Sistem / Akun Terhapus</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 font-mono text-xs font-bold border border-slate-200 uppercase tracking-wide">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="p-4 text-slate-600 font-medium leading-relaxed max-w-sm sm:max-w-md">
                        {{ $log->description }}
                    </td>
                    <td class="p-4 text-center text-slate-400 font-mono text-xs tracking-tight">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-400">
                        <i class="bi bi-clipboard-x text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Belum ada aktivitas log yang terekam atau tidak cocok.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $logs->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 30; // Siklus hitung mundur 30 detik
        const timerElement = document.getElementById('countdownTimer');

        const countdown = setInterval(() => {
            timeLeft--;
            if (timerElement) {
                timerElement.textContent = timeLeft;
            }

            if (timeLeft <= 0) {
                clearInterval(countdown);
                // Muat ulang halaman dengan mempertahankan query parameter (pencarian & halaman aktif)
                window.location.href = window.location.pathname + window.location.search;
            }
        }, 1000);
    });
</script>
@endsection