@extends('layouts.public')

@section('page_title', 'Katalog Materi')

@section('content')
<header class="bg-gradient-to-b from-primary to-[#0f243e] text-white py-12 border-b border-white/5 w-full">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight mb-2">Katalog Materi Pembelajaran</h1>
        <p class="text-slate-400 text-sm font-medium max-w-md mx-auto">Akses video edukasi interaktif dan podcast pilihan dari seluruh unit sekolah binaan yayasan.</p>
    </div>
</header>

<div class="container mx-auto px-6 py-8 flex-1 w-full">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-8">
        <form method="GET" action="{{ route('katalog.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="md:col-span-1">
                <label class="block text-[0.7rem] font-bold uppercase tracking-wider text-slate-400 mb-1">Tipe Konten</label>
                <select name="tipe_video" onchange="this.form.submit()" class="w-full border-slate-200 rounded-xl text-sm focus:ring-secondary focus:border-secondary py-2.5">
                    <option value="">Semua Tipe</option>
                    <option value="pembelajaran" {{ request('tipe_video') == 'pembelajaran' ? 'selected' : '' }}>Video Pembelajaran</option>
                    <option value="podcast" {{ request('tipe_video') == 'podcast' ? 'selected' : '' }}>Podcast</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[0.7rem] font-bold uppercase tracking-wider text-slate-400 mb-1">Pencarian Universal</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul materi, mata pelajaran, tingkat kelas, atau unit sekolah..." class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    @if(request('search') || request('tipe_video'))
                    <a href="{{ route('katalog.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 text-sm" title="Reset Filter"><i class="bi bi-x-circle-fill"></i></a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($videos as $video)
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

            <a href="{{ route('videos.show', $video->slug ?? $video->id) }}" class="block bg-slate-50 hover:bg-secondary text-center py-2.5 text-xs font-bold text-slate-600 hover:text-white border-t border-slate-100 transition-colors text-decoration-none">
                <i class="bi bi-play-circle-fill mr-1"></i> Mulai Belajar
            </a>
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
            <i class="bi bi-camera-video-off text-5xl mb-3 block text-slate-300"></i>
            <p class="font-bold text-slate-700">Video Tidak Ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Silakan coba kata kunci lain atau ubah filter tipe konten.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $videos->links('partials.pagination') }}
    </div>
</div>
@endsection