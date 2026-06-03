@extends('layouts.public')

@section('page_title', 'Beranda')

@section('content')
<div class="bg-gradient-to-br from-primary via-[#0f243e] to-[#1e3a8a] text-white py-16 lg:py-28 relative overflow-hidden flex flex-col items-center w-full">
    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-12 lg:gap-8">
            <div class="text-center lg:text-left space-y-6 max-w-xl mx-auto lg:mx-0">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs font-bold uppercase tracking-widest text-secondary shadow-inner">
                    <i class="bi bi-rocket-takeoff-fill text-sm"></i> Tahun Ajaran {{ $globalSettings['tahun_ajaran'] ?? '2026/2027' }}
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-none text-white">
                    Sistem Layanan Mutu Pendidikan Terpadu
                </h1>
                <p class="text-sm sm:text-base text-slate-300 font-medium leading-relaxed">
                    Platform Blended Learning interaktif modern untuk menunjang efektivitas aktivitas belajar mengajar, manajemen kurikulum, video interaktif, serta pemantauan logistik akademik secara transparan.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start pt-2">
                    <a href="{{ route('katalog.index') }}" class="px-6 py-3 bg-secondary hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-secondary/20 hover:shadow-xl transition-all duration-200 text-sm text-center text-decoration-none">
                        <i class="bi bi-compass mr-1.5"></i> Jelajahi Materi
                    </a>
                    <a href="{{ route('tentang') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-slate-200 hover:text-white font-bold rounded-xl border border-white/10 transition-all duration-200 text-sm text-center text-decoration-none">
                        Pelajari Fitur Platform
                    </a>
                </div>
            </div>

            <div class="hidden lg:block relative justify-self-center w-full max-w-md">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl relative transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-400 inline-block shadow-sm"></span>
                            <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block shadow-sm"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block shadow-sm"></span>
                        </div>
                        <span class="text-[0.65rem] font-mono tracking-widest text-slate-400 uppercase">Live Analytics Monitor</span>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center"><i class="bi bi-broadcast"></i></div>
                            <div class="flex-1">
                                <div class="h-2 bg-white/20 rounded w-1/3 mb-1.5"></div>
                                <div class="h-1.5 bg-white/10 rounded w-2/3"></div>
                            </div>
                            <span class="text-[0.65rem] font-bold text-emerald-400">99+ Active</span>
                        </div>
                        <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center"><i class="bi bi-camera-reels"></i></div>
                            <div class="flex-1">
                                <div class="h-2 bg-white/20 rounded w-1/2 mb-1.5"></div>
                                <div class="h-1.5 bg-white/10 rounded w-1/3"></div>
                            </div>
                            <span class="text-[0.65rem] font-bold text-secondary">VOD Ready</span>
                        </div>
                        <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50/10 text-amber-400 flex items-center justify-center"><i class="bi bi-chat-left-text"></i></div>
                            <div class="flex-1">
                                <div class="h-2 bg-white/20 rounded w-1/4 mb-1.5"></div>
                                <div class="h-1.5 bg-white/10 rounded w-1/2"></div>
                            </div>
                            <span class="text-[0.65rem] font-bold text-amber-400">Forums</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white border-b border-slate-200 w-full py-8 md:py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x divide-slate-100">
            <div class="space-y-1">
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ \App\Models\Video::count() }}+</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Video Pembelajaran</p>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ \App\Models\Subject::count() }}+</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mata Pelajaran</p>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ \App\Models\User::where('role', 'guru')->count() }}+</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Guru Pengajar</p>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ \App\Models\User::where('role', 'siswa')->count() }}+</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Siswa Terdaftar</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-slate-50 w-full py-16 lg:py-24">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="space-y-2 max-w-2xl">
                <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight">Materi Pembelajaran Terbaru</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Tingkatkan pemahaman Anda dengan menyimak rangkuman video interaktif terbaru yang baru saja diunggah oleh bapak/ibu guru hari ini.</p>
            </div>
            <a href="{{ route('katalog.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-100 hover:text-secondary shadow-sm transition-all whitespace-nowrap text-decoration-none">
                Lihat Semua Materi <i class="bi bi-arrow-right-short text-lg ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($latestVideos as $video)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden group flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <a href="{{ route('videos.show', $video->slug ?? $video->id) }}" class="relative w-full aspect-[16/9] bg-slate-100 overflow-hidden block">
                    <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" alt="Cover Video" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/40 transition-colors duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="w-12 h-12 bg-white/95 backdrop-blur-sm text-secondary rounded-full flex items-center justify-center text-xl shadow-xl transform scale-75 group-hover:scale-100 transition-transform duration-300">
                            <i class="bi bi-play-fill ml-1"></i>
                        </div>
                    </div>

                    <span class="absolute bottom-2 right-2 bg-slate-900/80 text-white font-mono text-[0.65rem] px-1.5 py-0.5 rounded font-bold tracking-wider">
                        {{ $video->durasi ?? '--:--' }}
                    </span>
                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md font-bold text-[0.6rem] uppercase tracking-wide border shadow-sm {{ $video->tipe_video == 'pembelajaran' ? 'bg-blue-500 text-white border-blue-600' : 'bg-purple-600 text-white border-purple-700' }}">
                        {{ $video->tipe_video == 'pembelajaran' ? 'Materi' : 'Podcast' }}
                    </span>
                </a>

                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <span class="text-[0.7rem] font-bold text-slate-400 block mb-1 uppercase tracking-wide">
                            {{ $video->subject->nama_mapel ?? 'Materi Umum' }} • Kls {{ $video->target_tingkat }}
                        </span>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors" title="{{ $video->judul }}">
                            <a href="{{ route('videos.show', $video->slug ?? $video->id) }}" class="text-inherit text-decoration-none">
                                {{ $video->judul }}
                            </a>
                        </h3>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-medium">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="bi bi-person text-slate-400 text-sm"></i>
                            <span class="truncate font-semibold text-slate-600" title="{{ $video->uploader->nama_lengkap ?? 'Guru' }}">{{ $video->uploader->nama_lengkap ?? 'Guru' }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-1">
                            <i class="bi bi-eye"></i> {{ number_format($video->views) }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-slate-400 bg-white/50 rounded-2xl border-2 border-dashed border-slate-200">
                <i class="bi bi-camera-video-off text-4xl mb-3 block text-slate-300"></i>
                <p class="font-bold text-slate-600">Belum Ada Materi Tersedia</p>
                <p class="text-xs text-slate-400 mt-1">Video pembelajaran yang dipublikasikan akan muncul di sini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="w-full bg-white py-16 lg:py-24 border-y border-slate-200 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

    <div class="container mx-auto px-6 text-center relative z-10 space-y-6 max-w-3xl">
        <div class="w-20 h-20 mx-auto bg-blue-50 rounded-full flex items-center justify-center text-secondary text-3xl mb-4 border border-blue-100 shadow-sm">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-800 tracking-tight leading-tight">Siap Memulai Proses Belajar Anda Hari Ini?</h2>
        <p class="text-base text-slate-500 font-medium leading-relaxed">Bergabunglah ke dalam kelas interaktif. Diskusikan mata pelajaran di ruang forum, akses rangkuman, dan evaluasi hasil belajar secara mandiri.</p>

        <div class="pt-4">
            @auth
            <a href="{{ $dashboardRoute ?? url('/dashboard') }}" class="inline-flex items-center px-8 py-3.5 bg-secondary text-white font-bold rounded-xl hover:bg-blue-600 shadow-xl shadow-secondary/30 transition-all hover:-translate-y-1 text-decoration-none">
                Masuk ke Ruang Kelas Anda <i class="bi bi-box-arrow-in-right ml-2 text-lg"></i>
            </a>
            @else
            <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3.5 bg-secondary text-white font-bold rounded-xl hover:bg-blue-600 shadow-xl shadow-secondary/30 transition-all hover:-translate-y-1 text-decoration-none">
                Login ke Akun Anda <i class="bi bi-box-arrow-in-right ml-2 text-lg"></i>
            </a>
            <p class="text-xs text-slate-400 mt-4 font-medium">*Akun siswa diberikan oleh Administrator Sekolah masing-masing.</p>
            @endauth
        </div>
    </div>
</div>
@endsection