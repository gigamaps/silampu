@extends('layouts.dashboard')

@section('page_title', 'Video Pembelajaran')

@section('content')
@if(!$class)
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-4xl mb-5 shadow-inner border-4 border-white">
        <i class="bi bi-person-bounding-box"></i>
    </div>
    <h2 class="text-xl font-extrabold text-slate-800 mb-2">Akses Dibatasi</h2>
    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">Anda belum terdaftar di ruang kelas mana pun. Perpustakaan video belum tersedia untuk Anda.</p>
</div>
@else

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="flex-1">
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="bi bi-collection-play-fill text-secondary"></i> Pustaka Materi
            </h2>
            <p class="text-sm text-slate-500 font-medium m-0 mt-1 max-w-xl">Jelajahi video pembelajaran yang telah disediakan khusus untuk <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $class->nama_kelas }}</span>.</p>
        </div>

        <form action="{{ route('siswa.videos.index') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative w-full sm:w-64">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                @if(request('search'))
                <a href="{{ route('siswa.videos.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>

            <select name="subject_id" onchange="this.form.submit()" class="w-full sm:w-56 px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary cursor-pointer">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($subjects as $mapel)
                <option value="{{ $mapel->id }}" {{ request('subject_id') == $mapel->id ? 'selected' : '' }}>
                    {{ $mapel->nama_mapel }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($videos as $vid)
    @php
    // 1. Cek status tuntas
    $isFinished = in_array($vid->id, $watchedVideoIds ?? []);

    // 2. Kalkulasi Progress Bar (Mengubah Durasi menjadi Detik)
    $lastPos = $watchProgresses[$vid->id] ?? 0; // Membutuhkan variabel $watchProgresses dari Controller
    $percentage = 0;

    if ($isFinished) {
    $percentage = 100;
    } elseif ($lastPos > 0 && !empty($vid->durasi)) {
    $timeParts = explode(':', $vid->durasi);
    $totalSeconds = 0;

    // Konversi format HH:MM:SS atau MM:SS ke Total Detik
    if (count($timeParts) == 3) {
    $totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];
    } elseif (count($timeParts) == 2) {
    $totalSeconds = ($timeParts[0] * 60) + $timeParts[1];
    }

    // Menghitung persentase
    if ($totalSeconds > 0) {
    $percentage = min(100, round(($lastPos / $totalSeconds) * 100));
    }
    }
    @endphp

    <a href="{{ route('videos.show', $vid->slug) }}" class="group bg-white rounded-2xl border {{ $isFinished ? 'border-emerald-200 shadow-emerald-100' : 'border-slate-200' }} shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all text-decoration-none flex flex-col h-full relative">

        <div class="relative aspect-video bg-slate-100 overflow-hidden border-b border-slate-100">
            <img src="https://img.youtube.com/vi/{{ $vid->youtube_id }}/mqdefault.jpg" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 {{ $isFinished ? 'opacity-90 grayscale-[20%]' : '' }}">
            <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/40 transition-colors duration-300"></div>

            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="w-14 h-14 bg-white/95 backdrop-blur-sm text-secondary rounded-full flex items-center justify-center text-2xl shadow-xl transform scale-75 group-hover:scale-100 transition-transform duration-300">
                    <i class="bi bi-play-fill ml-1"></i>
                </div>
            </div>

            <!-- Posisi Durasi dinaikkan sedikit agar tidak tertutup Progress Bar -->
            <span class="absolute bottom-3 right-2 bg-black/70 backdrop-blur-md text-white font-mono text-[0.65rem] px-2 py-1 rounded-md font-bold">{{ $vid->durasi ?? '--:--' }}</span>

            <span class="absolute top-2 left-2 bg-white/95 backdrop-blur-md text-slate-700 text-[0.65rem] px-2 py-1 rounded-md font-extrabold uppercase tracking-widest shadow-sm truncate max-w-[80%] border border-white/50">
                {{ $vid->subject->nama_mapel ?? 'Materi Umum' }}
            </span>

            <!-- FITUR BARU: PROGRESS BAR -->
            @if($percentage > 0)
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-slate-800/50 backdrop-blur-sm z-10">
                <!-- Jika 100% jadi Hijau, Jika belum jadi Merah khas YouTube -->
                <div class="h-full {{ $percentage == 100 ? 'bg-emerald-500' : 'bg-red-600' }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
            </div>
            @endif
        </div>

        <div class="p-5 flex-1 flex flex-col relative">
            @if($isFinished)
            <div class="absolute -top-3 right-4 bg-emerald-500 text-white text-[0.6rem] font-extrabold px-2 py-1 rounded-full border-2 border-white shadow-sm flex items-center gap-1 uppercase tracking-widest z-10">
                <i class="bi bi-check2-all text-xs"></i> Tuntas
            </div>
            @endif

            <h4 class="font-bold text-slate-800 text-sm leading-relaxed mb-1.5 line-clamp-2 group-hover:text-secondary transition-colors">{{ $vid->judul }}</h4>

            <div class="flex items-center justify-between mt-1 mb-4">
                <p class="text-[0.7rem] text-slate-400 font-medium line-clamp-1 m-0"><i class="bi bi-calendar3 mr-1"></i> {{ $vid->created_at->format('d M Y') }}</p>
                <!-- Menampilkan Teks Persentase -->
                @if($percentage > 0 && $percentage < 100)
                    <p class="text-[0.65rem] text-red-600 font-bold m-0 bg-red-50 px-1.5 py-0.5 rounded">Ditonton {{ $percentage }}%</p>
                    @endif
            </div>

            <div class="mt-auto flex items-center justify-between text-xs text-slate-500 font-medium pt-4 border-t border-slate-100">
                <span class="flex items-center gap-2 truncate">
                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="bi bi-person-fill"></i></div>
                    <span class="truncate">{{ explode(' ', $vid->uploader->nama_lengkap)[0] ?? 'Guru' }}</span>
                </span>
                <span class="shrink-0 text-[0.65rem] flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-md"><i class="bi bi-eye"></i> {{ number_format($vid->views) }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
            <i class="bi bi-camera-reels"></i>
        </div>
        <h5 class="font-extrabold text-slate-700 mb-1">Tidak Ada Materi</h5>
        <p class="text-sm font-medium text-slate-500 m-0">Belum ada video pembelajaran yang sesuai dengan kriteria pencarian.</p>
    </div>
    @endforelse
</div>

@if($videos->hasPages())
<div class="mt-8 bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex justify-center">
    {{ $videos->links('partials.pagination') }}
</div>
@endif

@endif
@endsection