@extends('layouts.dashboard')

@section('page_title', 'Dashboard Guru')

@section('content')
@php
$user = auth()->user();

// 1. Ambil daftar unit tempat guru ini mengajar
$myUnits = $user->units->isNotEmpty() ? $user->units->pluck('nama_unit')->implode(', ') : 'Belum ditempatkan';

// 2. Hitung jumlah Mata Pelajaran yang diampu
$countMapel = \DB::table('subject_user')->where('user_id', $user->id)->count();

// 3. Hitung jumlah Kelas unik yang diajar
$countKelas = \DB::table('subjects')
->join('subject_user', 'subjects.id', '=', 'subject_user.subject_id')
->where('subject_user.user_id', $user->id)
->distinct('subjects.class_id')
->count('subjects.class_id');

// 4. Hitung jumlah Video Materi yang sudah diunggah
$countVideo = \App\Models\Video::where('uploader_id', $user->id)->count();

// 5. PERBAIKAN: Hitung jumlah Forum Diskusi yang dibuat guru ini
$countForum = \App\Models\Forum::where('guru_id', $user->id)->count();
@endphp

<div class="space-y-6">

    <div class="bg-gradient-to-r from-indigo-600 to-blue-800 rounded-2xl p-6 sm:p-8 shadow-sm relative overflow-hidden border border-white/5">
        <div class="absolute top-0 right-0 -mt-6 -mr-6 w-36 h-36 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-32 w-28 h-28 bg-cyan-400/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 space-y-2">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 border border-white/20 rounded-full text-[0.7rem] font-bold uppercase tracking-widest text-blue-100 shadow-inner">
                    <i class="bi bi-person-workspace"></i> Ruang Pendidik
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-[0.7rem] font-bold uppercase tracking-widest text-emerald-300">
                    <i class="bi bi-pin-map-fill"></i> {{ $myUnits }}
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                Halo, {{ $user->nama_lengkap }}!
                <i class="bi bi-brightness-high-fill text-yellow-300 text-3xl animate-pulse"></i>
            </h2>
            <p class="text-indigo-100 font-medium text-sm max-w-xl">Kelola materi kelas Anda, bagikan video pembelajaran interaktif, dan pantau perkembangan diskusi siswa dari satu tempat.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0 border border-purple-100">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Mapel Diampu</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countMapel }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0 border border-blue-100">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas Aktif</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countKelas }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0 border border-emerald-100">
                <i class="bi bi-play-btn-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Video Diunggah</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countVideo }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0 border border-amber-100">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
            <div>
                <p class="text-[0.75rem] font-bold text-slate-400 uppercase tracking-widest mb-1">Ruang Diskusi</p>
                <h3 class="text-2xl font-extrabold text-slate-800 leading-none">{{ $countForum }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base mb-4">Akses Cepat Pengajar</h3>
                <div class="space-y-3">
                    <a href="{{ route('guru.videos.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs m-0 group-hover:text-emerald-600 transition-colors">Unggah Video Materi</p>
                            <p class="text-[0.7rem] text-slate-400 m-0 mt-0.5">Tambah bahan ajar baru</p>
                        </div>
                    </a>

                    <a href="{{ route('guru.forums.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors group text-decoration-none">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <i class="bi bi-chat-left-dots-fill"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs m-0 group-hover:text-amber-600 transition-colors">Kelola Forum Diskusi</p>
                            <p class="text-[0.7rem] text-slate-400 m-0 mt-0.5">Tinjau Q&A siswa hari ini</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100 text-[0.7rem] text-indigo-500 font-medium mt-6">
                <i class="bi bi-info-circle-fill mr-1"></i> Data di atas dihitung khusus dari mata pelajaran yang ditugaskan kepada Anda.
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 text-base">Riwayat Unggahan Video Anda</h3>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terbaru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[0.7rem]">
                            <th class="pb-3 font-bold">Judul Materi</th>
                            <th class="pb-3 font-bold">Target Kelas</th>
                            <th class="pb-3 font-bold text-center">Status</th>
                            <th class="pb-3 font-bold text-center">Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @php
                        $myRecentVideos = \App\Models\Video::with('subject.studentClass')
                        ->where('uploader_id', $user->id)
                        ->latest()
                        ->take(5)
                        ->get();
                        @endphp

                        @forelse($myRecentVideos as $vid)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 font-bold text-slate-800 max-w-[200px] truncate" title="{{ $vid->judul }}">
                                {{ $vid->judul }}
                                <div class="text-[0.65rem] text-slate-400 font-medium mt-0.5">{{ $vid->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 text-slate-500 font-medium">
                                {{ $vid->subject->studentClass->nama_kelas ?? 'Umum' }}
                            </td>
                            <td class="py-3 text-center">
                                @if($vid->status == 'public')
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[0.65rem]">Publik</span>
                                @else
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold text-[0.65rem]">Privat</span>
                                @endif
                            </td>
                            <td class="py-3 text-center font-bold text-blue-600">
                                {{ number_format($vid->views) }} <i class="bi bi-eye ml-0.5 text-slate-400"></i>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 font-medium">
                                Anda belum mengunggah video pembelajaran apapun.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection