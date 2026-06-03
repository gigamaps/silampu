@extends('layouts.dashboard')

@section('page_title', 'Diskusi Pelajaran')

@section('content')
@if(!$class)
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-4xl mb-5 shadow-inner border-4 border-white">
        <i class="bi bi-person-bounding-box"></i>
    </div>
    <h2 class="text-xl font-extrabold text-slate-800 mb-2">Akses Terbatas</h2>
    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">Anda belum terdaftar di ruang kelas mana pun. Fitur diskusi belum tersedia.</p>
</div>
@else

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-center relative overflow-hidden shadow-sm">
        <div class="absolute right-0 top-0 w-32 h-32 bg-amber-500/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
        <h2 class="text-2xl font-extrabold text-slate-800 mb-1 relative z-10 flex items-center gap-2">
            <i class="bi bi-chat-quote-fill text-amber-500"></i> Ruang Diskusi
        </h2>
        <p class="text-sm text-slate-500 font-medium m-0 relative z-10">Aktiflah bertanya dan berdiskusi bersama pengajar di kelas <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700 font-bold">{{ $class->nama_kelas }}</span>.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center gap-5 shadow-sm">
        <div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-3xl shrink-0 border border-amber-100">
            <i class="bi bi-chat-right-text"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest m-0 mb-1">Topik Aktif</p>
            <h4 class="text-3xl font-extrabold text-slate-800 m-0 leading-none">{{ $stats['open'] }} <span class="text-sm text-slate-400 font-medium">/ {{ $stats['total'] }} Total</span></h4>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('siswa.forums.index') }}" class="flex flex-col sm:flex-row justify-between items-center gap-4">

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:block">Tampilkan</span>
            <select name="per_page" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary font-bold cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
            </select>
        </div>

        <div class="relative w-full sm:w-80">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul diskusi..." class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
            @if(request('search'))
            <a href="{{ route('siswa.forums.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors">
                <i class="bi bi-x-circle-fill"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<div class="space-y-4">
    @forelse($forums as $forum)
    <a href="{{ route('siswa.forums.show', Crypt::encryptString($forum->id)) }}" class="group block bg-white rounded-2xl border {{ $forum->status === 'open' ? 'border-slate-200 hover:border-secondary' : 'border-slate-200 bg-slate-50/50' }} shadow-sm p-5 transition-all hover:shadow-md text-decoration-none relative overflow-hidden">

        <div class="flex flex-col md:flex-row gap-5 items-start md:items-center">

            <div class="flex items-center gap-3 shrink-0">
                <div class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center font-extrabold text-lg uppercase shadow-inner">
                    {{ substr($forum->teacher->nama_lengkap, 0, 1) }}
                </div>
                <div class="md:hidden">
                    <h6 class="text-sm font-bold text-slate-800 m-0 leading-tight">{{ $forum->teacher->nama_lengkap }}</h6>
                    <p class="text-[0.65rem] font-bold text-secondary uppercase tracking-widest m-0">Pengajar</p>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @if($forum->status === 'open')
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[0.65rem] font-extrabold uppercase tracking-widest border border-emerald-100"><i class="bi bi-unlock-fill mr-1"></i> Terbuka</span>
                    @else
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-md text-[0.65rem] font-extrabold uppercase tracking-widest border border-slate-200"><i class="bi bi-lock-fill mr-1"></i> Ditutup</span>
                    @endif
                    <span class="text-[0.7rem] text-slate-400 font-bold"><i class="bi bi-clock-history mr-1"></i> {{ $forum->created_at->diffForHumans() }}</span>
                </div>

                <h4 class="text-lg font-bold text-slate-800 mb-1.5 leading-snug group-hover:text-secondary transition-colors truncate">{{ $forum->judul }}</h4>
                <p class="text-sm text-slate-500 line-clamp-1 m-0 mb-3">{{ $forum->deskripsi }}</p>

                <div class="hidden md:flex items-center gap-3 text-xs">
                    <span class="font-bold text-slate-600 flex items-center gap-1.5"><i class="bi bi-person-fill text-slate-400"></i> {{ $forum->teacher->nama_lengkap }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-bold text-slate-400 uppercase tracking-widest">{{ $class->unit->nama_unit ?? '-' }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between w-full md:w-auto md:flex-col md:justify-center md:pl-6 md:border-l border-slate-100 gap-2 shrink-0">
                <div class="text-left md:text-center">
                    <div class="text-2xl font-black text-slate-800 leading-none">{{ $forum->replies_count }}</div>
                    <div class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mt-1">Balasan</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-secondary group-hover:text-white transition-all shadow-sm">
                    <i class="bi bi-arrow-right-short text-2xl"></i>
                </div>
            </div>

        </div>
    </a>
    @empty
    <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 border border-slate-100">
            <i class="bi bi-chat-square-dots"></i>
        </div>
        <h5 class="font-extrabold text-slate-700 mb-1">Belum Ada Topik</h5>
        <p class="text-sm font-medium text-slate-500 m-0">Pengajarmu belum memulai diskusi untuk kelas ini.</p>
    </div>
    @endforelse
</div>

@if($forums->hasPages())
<div class="mt-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex justify-center">
    {{ $forums->onEachSide(1)->links('partials.pagination') }}
</div>
@endif

@endif
@endsection