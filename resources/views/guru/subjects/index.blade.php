@extends('layouts.dashboard')

@section('page_title', 'Mata Pelajaran Diampu')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 mb-6">
    <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Mata Pelajaran & Kelas</h6>
    <p class="text-xs text-slate-400 font-medium mt-1">Pilih mata pelajaran di bawah ini untuk melihat daftar siswa atau mengelola aktivitas kelas.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($subjects as $subject)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-secondary transition-all duration-300 group flex flex-col h-full overflow-hidden">
        <div class="p-6 flex-1">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                <i class="bi bi-book-half"></i>
            </div>
            <h3 class="font-extrabold text-slate-800 text-lg mb-1">{{ $subject->nama_mapel }}</h3>
            <p class="font-bold text-secondary text-sm mb-3">Kelas: {{ $subject->studentClass->nama_kelas ?? '-' }}</p>
            <span class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 text-slate-500 font-bold text-[0.65rem] border border-slate-200 uppercase tracking-widest">
                <i class="bi bi-building mr-1.5"></i> {{ $subject->studentClass->unit->nama_unit ?? 'Unit Dihapus' }}
            </span>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/50 p-4">
            <a href="{{ route('guru.subjects.students', $subject->encrypted_id) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-secondary hover:text-white hover:border-secondary transition-colors text-decoration-none">
                <i class="bi bi-people-fill"></i> Lihat Daftar Siswa
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
            <i class="bi bi-journal-x"></i>
        </div>
        <h4 class="font-bold text-slate-700 text-lg mb-2">Belum Ada Mata Pelajaran</h4>
        <p class="text-slate-500 text-sm max-w-md mx-auto">Anda belum ditugaskan untuk mengampu mata pelajaran apapun. Silakan hubungi tim Kurikulum atau Administrator.</p>
    </div>
    @endforelse
</div>
@endsection