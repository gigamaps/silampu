@extends('layouts.dashboard')

@section('page_title', 'Statistik Video')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.videos.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-secondary transition-colors text-decoration-none">
        <i class="bi bi-arrow-left-short text-xl mr-1"></i> Kembali ke Daftar Video
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-center">
        <h2 class="text-xl font-extrabold text-slate-800 mb-2">{{ $video->judul }}</h2>
        <p class="text-sm text-slate-500 font-medium"><i class="bi bi-person-video mr-1"></i> Kelas: {{ $class->nama_kelas }} • Mata Pelajaran: {{ $video->subject->nama_mapel }}</p>
    </div>

    <div class="bg-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-200 flex items-center justify-between">
        <div>
            <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Tingkat Penyelesaian</p>
            <h3 class="text-3xl font-extrabold">
                {{ $stats->where('raw_finished', true)->count() }} <span class="text-lg text-indigo-300 font-medium">/ {{ $stats->count() }} Siswa</span>
            </h3>
        </div>
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl">
            <i class="bi bi-pie-chart-fill"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h6 class="m-0 font-bold text-slate-800">Daftar Progres Siswa</h6>
    </div>
    <table class="w-full text-left">
        <thead class="bg-slate-50">
            <tr class="text-slate-400 text-[0.7rem] uppercase tracking-wider font-bold">
                <th class="p-4">Nama Siswa</th>
                <th class="p-4">NIS</th>
                <th class="p-4 text-center">Status</th>
                <th class="p-4">Terakhir Diakses</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($stats as $s)
            <tr class="hover:bg-slate-50">
                <td class="p-4 font-bold text-slate-700">{{ $s['nama'] }}</td>
                <td class="p-4 text-slate-600">{{ $s['nis'] }}</td>
                <td class="p-4 text-center">
                    @if($s['status'] == 'Tuntas')
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[0.65rem]"><i class="bi bi-check-circle-fill mr-1"></i> Tuntas</span>
                    @elseif($s['status'] == 'Progres')
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[0.65rem]"><i class="bi bi-clock-fill mr-1"></i> Progres</span>
                    @else
                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-[0.65rem]"><i class="bi bi-dash-circle mr-1"></i> Belum Menonton</span>
                    @endif
                </td>
                <td class="p-4 text-xs text-slate-500">{{ $s['last_seen'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection