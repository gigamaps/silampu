@extends('layouts.dashboard')

@section('page_title', 'Daftar Siswa Kelas')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.subjects.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-secondary transition-colors text-decoration-none">
        <i class="bi bi-arrow-left-short text-xl mr-1"></i> Kembali ke Daftar Mapel
    </a>
</div>

<div class="bg-primary bg-gradient-to-r from-primary to-[#1e3a8a] rounded-2xl p-6 sm:p-8 shadow-sm relative overflow-hidden border border-white/10 mb-6">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10">
        <span class="inline-flex items-center px-3 py-1 bg-white/20 border border-white/20 rounded-full text-[0.7rem] font-bold uppercase tracking-widest text-white shadow-inner mb-3">
            <i class="bi bi-book-half mr-2"></i> {{ $subject->nama_mapel }}
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-1">{{ $class->nama_kelas }}</h2>
        <p class="text-slate-200 font-medium text-sm m-0"><i class="bi bi-building mr-1"></i> {{ $class->unit->nama_unit ?? '-' }} • Total {{ $students->count() }} Siswa terdaftar</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex justify-between items-center">
        <h6 class="m-0 font-bold text-lg text-slate-800">Laporan Keaktifan Siswa</h6>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.7rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Profil Siswa</th>
                    <th class="p-4 border-b border-slate-200">Identitas Akun</th>
                    <th class="p-4 border-b border-slate-200 text-center">Progres Materi</th>
                    <th class="p-4 border-b border-slate-200 text-center">Keaktifan Forum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($students as $index => $student)

                @php
                // 1. Hitung jumlah video yang TUNTAS ditonton siswa pada mata pelajaran ini
                $videoSelesai = \App\Models\WatchProgress::where('user_id', $student->id)
                ->where('is_finished', true)
                ->whereHas('video', function($q) use ($subject) {
                $q->where('subject_id', $subject->id);
                })->count();

                // 2. Hitung jumlah interaksi/balasan siswa pada forum di kelas ini
                $totalInteraksi = \App\Models\ForumReply::where('user_id', $student->id)
                ->whereHas('forum', function($q) use ($class) {
                $q->where('class_id', $class->id);
                })->count();
                @endphp

                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-xs uppercase shrink-0 shadow-inner">
                                {{ substr($student->nama_lengkap, 0, 1) }}
                            </div>
                            <span class="font-bold text-slate-800">{{ $student->nama_lengkap }}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-600">{{ $student->nis_np }}</div>
                        <div class="text-[0.7rem] font-medium text-slate-400 mt-0.5"><i class="bi bi-at mr-0.5"></i>{{ $student->username }}</div>
                    </td>
                    <td class="p-4 text-center">
                        @if($videoSelesai > 0)
                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-blue-50 text-blue-600 font-bold text-[0.7rem] border border-blue-100">
                            <i class="bi bi-play-btn-fill mr-1.5"></i> {{ $videoSelesai }} Tuntas
                        </span>
                        @else
                        <span class="text-slate-300 font-medium text-xs italic">Belum ada progres</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($totalInteraksi > 0)
                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-amber-50 text-amber-600 font-bold text-[0.7rem] border border-amber-100">
                            <i class="bi bi-chat-dots-fill mr-1.5"></i> {{ $totalInteraksi }} Interaksi
                        </span>
                        @else
                        <span class="text-slate-300 font-medium text-xs italic">Pasif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-400">
                        <i class="bi bi-person-x text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Belum ada siswa yang terdaftar di kelas ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection