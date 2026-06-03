@extends('layouts.dashboard')

@section('page_title', 'Dashboard Kurikulum')

@section('content')
@php
// 1. Ambil ID Unit secara aman dari tabel pivot unit_user
$myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');

// 2. Ambil Nama Unit Sekolah
$myUnitName = \DB::table('units')->where('id', $myUnitId)->value('nama_unit') ?? 'Unit Sekolah';

// 3. Hitung Guru yang terdaftar di unit yang sama via tabel pivot
$countGuru = \DB::table('users')
->join('unit_user', 'users.id', '=', 'unit_user.user_id')
->where('users.role', 'guru')
->where('unit_user.unit_id', $myUnitId)
->whereNull('users.deleted_at')
->count();

// 4. Hitung Kelas di unit terkait
$countKelas = \DB::table('classes')
->where('unit_id', $myUnitId)
->whereNull('deleted_at')
->count();

// 5. Hitung Mapel yang terhubung ke unit ini melalui perantara kelas
$countMapel = \DB::table('subjects')
->join('classes', 'subjects.class_id', '=', 'classes.id')
->where('classes.unit_id', $myUnitId)
->whereNull('subjects.deleted_at')
->count();

// 6. Hitung Video Konten di unit terkait
$countVideo = \DB::table('videos')
->where('unit_id', $myUnitId)
->whereNull('deleted_at')
->count();
@endphp

<div class="space-y-6">

    <div class="bg-gradient-to-r from-primary to-[#1e3a8a] rounded-2xl p-6 sm:p-8 shadow-sm relative overflow-hidden border border-white/5">
        <div class="absolute top-0 right-0 -mt-6 -mr-6 w-36 h-36 bg-secondary/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-32 w-28 h-28 bg-purple-500/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 space-y-2">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[0.7rem] font-bold uppercase tracking-widest text-secondary shadow-inner">
                    <i class="bi bi-award-fill"></i> Manajemen Mutu & Akademik
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-[0.7rem] font-bold uppercase tracking-widest text-emerald-400">
                    <i class="bi bi-building"></i> {{ $myUnitName }}
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                Selamat Datang, {{ auth()->user()->nama_lengkap }}!
                <i class="bi bi-patch-check-fill text-yellow-400 text-3xl"></i>
            </h2>
            <p class="text-slate-300 font-medium text-sm max-w-xl">Pusat pemantauan capaian kurikulum, peninjauan konten pembelajaran, serta pengelolaan mata pelajaran khusus untuk wilayah unit kerja Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0 border border-blue-100">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Guru Pengampu</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countGuru }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0 border border-amber-100">
                <i class="bi bi-door-open-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Kelas</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countKelas }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0 border border-purple-100">
                <i class="bi bi-book-half"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Mata Pelajaran</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countMapel }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0 border border-emerald-100">
                <i class="bi bi-camera-reels-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Konten Video / VOD</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countVideo }}</h3>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base mb-4">Akses Cepat Akademik</h3>
                <div class="space-y-3">
                    <a href="{{ route('kurikulum.subjects.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <i class="bi bi-bookmark-plus-fill"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs m-0 group-hover:text-purple-600 transition-colors">Kelola Mata Pelajaran</p>
                            <p class="text-[0.7rem] text-slate-400 m-0 mt-0.5">Atur mapel unit Anda</p>
                        </div>
                    </a>

                    <a href="{{ route('kurikulum.monitoring.videos') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs m-0 group-hover:text-emerald-600 transition-colors">Pantau Konten Video</p>
                            <p class="text-[0.7rem] text-slate-400 m-0 mt-0.5">Tinjau VOD & tayangan unit</p>
                        </div>
                    </a>

                    <a href="{{ route('kurikulum.monitoring.activity_logs') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs m-0 group-hover:text-blue-600 transition-colors">Audit Log Aktivitas</p>
                            <p class="text-[0.7rem] text-slate-400 m-0 mt-0.5">Periksa riwayat tindakan sistem</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-[0.7rem] text-slate-400 font-medium mt-6">
                <i class="bi bi-info-circle-fill text-blue-500 mr-1"></i> Mode multi-school aktif. Anda hanya dapat melihat data akademik yang terdaftar di unit sekolah Anda.
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 text-base">Konten Video Terkini Unit</h3>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">5 Teratas</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[0.7rem]">
                            <th class="pb-3 font-bold">Materi / Judul</th>
                            <th class="pb-3 font-bold">Mata Pelajaran</th>
                            <th class="pb-3 font-bold">Pengunggah</th>
                            <th class="pb-3 font-bold text-center">Tayangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @php
                        // Tabel videos memiliki kolom unit_id langsung, jadi ini aman menggunakan Eloquent
                        $recentVideos = \App\Models\Video::with(['uploader', 'subject'])
                        ->where('unit_id', $myUnitId)
                        ->latest()
                        ->take(5)
                        ->get();
                        @endphp

                        @forelse($recentVideos as $video)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 font-bold text-slate-800 max-w-[180px] truncate" title="{{ $video->judul }}">
                                {{ $video->judul }}
                            </td>
                            <td class="py-3 text-slate-500 font-medium">
                                {{ $video->subject->nama_mapel ?? 'Materi Umum' }}
                            </td>
                            <td class="py-3 font-medium text-slate-600">
                                {{ $video->uploader->nama_lengkap ?? 'Guru' }}
                            </td>
                            <td class="py-3 text-center font-bold text-blue-600">
                                {{ number_format($video->views) }} x
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 font-medium">Belum ada video pembelajaran yang diunggah oleh guru dari unit sekolah Anda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection