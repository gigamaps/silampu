@extends('layouts.dashboard')

@section('page_title', 'Ruang Belajar Siswa')

@section('content')
@php
$user = auth()->user()->load('studentClass.unit');
$class = $user->studentClass;
$hasKelas = !is_null($class);

if ($hasKelas) {
$countMapel = \App\Models\Subject::where('class_id', $class->id)->count();

$countVideo = \App\Models\Video::where(function($q) use ($class) {
$q->whereHas('subject', function($sub) use ($class) {
$sub->where('class_id', $class->id);
})->orWhere('target_tingkat', 'umum');
})->count();

$countForum = \App\Models\Forum::where('class_id', $class->id)->count();

$recentVideos = \App\Models\Video::with(['subject', 'uploader'])
->where(function($q) use ($class) {
$q->whereHas('subject', function($sub) use ($class) {
$sub->where('class_id', $class->id);
})->orWhere('target_tingkat', 'umum');
})
->latest()
->take(4)
->get();

$recentForums = \App\Models\Forum::with('teacher')
->where('class_id', $class->id)
->where('status', 'open')
->latest()
->take(4)
->get();
}
@endphp

@if(!$hasKelas)
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-4xl mb-5 shadow-inner">
        <i class="bi bi-person-bounding-box"></i>
    </div>
    <h2 class="text-xl font-extrabold text-slate-800 mb-2">Belum Terdaftar di Kelas</h2>
    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">Halo <b>{{ $user->nama_lengkap }}</b>! Saat ini akun Anda belum dimasukkan ke dalam ruang kelas. Silakan hubungi Wali Kelas atau Admin sekolah.</p>
</div>
@else
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-wider border border-blue-100">
                    <i class="bi bi-door-open-fill mr-1"></i> {{ $class->nama_kelas }}
                </span>
                <span class="px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-xs font-bold uppercase tracking-wider border border-slate-200">
                    <i class="bi bi-building mr-1"></i> {{ $class->unit->nama_unit ?? '-' }}
                </span>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 mb-2">Halo, {{ explode(' ', $user->nama_lengkap)[0] }}! 👋</h2>
            <p class="text-slate-500 text-sm font-medium m-0">Selamat datang di ruang belajarmu hari ini.</p>
        </div>

        <div class="shrink-0 w-full md:w-auto">
            <div class="flex items-center gap-4 px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-600">
                <i class="bi bi-check-circle-fill text-3xl"></i>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-500 m-0 mb-1">Status Kehadiran</p>
                    <p class="text-lg font-extrabold m-0 leading-none">Hadir Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0 border border-blue-100">
                <i class="bi bi-book-half"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider m-0 mb-1">Mata Pelajaran</p>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 leading-none">{{ $countMapel }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0 border border-purple-100">
                <i class="bi bi-camera-reels-fill"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider m-0 mb-1">Video Materi</p>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 leading-none">{{ $countVideo }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0 border border-amber-100">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider m-0 mb-1">Diskusi Kelas</p>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 leading-none">{{ $countForum }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <h3 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-play-circle-fill text-secondary mr-2"></i> Materi Terbaru</h3>
                <a href="{{ route('siswa.videos.index') }}" class="text-sm font-bold text-secondary hover:underline text-decoration-none">Lihat Semua</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($recentVideos as $vid)
                <a href="{{ route('videos.show', $vid->slug) }}" class="group block text-decoration-none">
                    <div class="bg-slate-100 rounded-t-xl overflow-hidden h-40 relative border border-b-0 border-slate-200">
                        <img src="https://img.youtube.com/vi/{{ $vid->youtube_id }}/mqdefault.jpg" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/30 transition-colors"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-12 h-12 bg-white text-secondary rounded-full flex items-center justify-center text-xl shadow-lg">
                                <i class="bi bi-play-fill ml-1"></i>
                            </div>
                        </div>
                        <span class="absolute bottom-2 right-2 bg-slate-900/80 text-white font-mono text-xs px-2 py-1 rounded font-bold">{{ $vid->durasi ?? '--:--' }}</span>
                    </div>
                    <div class="bg-white p-4 border border-t-0 border-slate-200 rounded-b-xl group-hover:border-secondary transition-colors h-[110px] flex flex-col">
                        <span class="text-xs font-bold text-secondary uppercase tracking-widest mb-1 block truncate">{{ $vid->subject->nama_mapel ?? 'Umum' }}</span>
                        <h4 class="font-bold text-slate-800 text-sm leading-snug mb-2 line-clamp-2">{{ $vid->judul }}</h4>
                        <div class="mt-auto flex items-center justify-between text-xs text-slate-500 font-medium">
                            <span class="truncate"><i class="bi bi-person-fill"></i> {{ explode(' ', $vid->uploader->nama_lengkap)[0] ?? 'Guru' }}</span>
                            <span class="shrink-0">{{ $vid->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full bg-slate-50 rounded-xl border border-dashed border-slate-200 p-8 text-center">
                    <i class="bi bi-camera-video text-3xl text-slate-400 mb-2 block"></i>
                    <p class="text-sm font-medium text-slate-500 m-0">Belum ada video materi baru.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <h3 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-chat-quote-fill text-amber-500 mr-2"></i> Diskusi Kelas</h3>
            </div>

            <div class="space-y-3">
                @forelse($recentForums as $forum)
                <a href="{{ route('siswa.forums.show', Crypt::encryptString($forum->id)) }}" class="block p-4 border border-slate-100 rounded-xl hover:bg-slate-50 hover:border-amber-200 transition-colors group text-decoration-none">
                    <h5 class="font-bold text-slate-800 text-sm mb-1 leading-snug group-hover:text-amber-600 truncate">{{ $forum->judul }}</h5>
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed m-0 mb-3">{{ $forum->deskripsi }}</p>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-widest pt-3 border-t border-slate-100">
                        <span class="text-slate-600"><i class="bi bi-person-circle"></i> {{ explode(' ', $forum->teacher->nama_lengkap)[0] }}</span>
                        <span>{{ $forum->created_at->format('d M') }}</span>
                    </div>
                </a>
                @empty
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <i class="bi bi-chat-square-dots text-3xl text-slate-400 mb-2 block"></i>
                    <p class="text-sm font-medium text-slate-500 m-0">Tidak ada diskusi aktif.</p>
                </div>
                @endforelse
            </div>

            @if($recentForums->count() > 0)
            <div class="mt-4 text-center">
                <a href="{{ route('siswa.forums.index') }}" class="text-sm font-bold text-slate-600 hover:text-amber-600 hover:underline text-decoration-none">Lihat Semua Topik</a>
            </div>
            @endif
        </div>

    </div>
</div>
@endif
@endsection