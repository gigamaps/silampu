@extends('layouts.dashboard')

@section('page_title', 'Detail Kelas')

@section('content')
@php
$prefix = auth()->user()->role === 'kurikulum' ? 'kurikulum' : 'admin';
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="md:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-1 rounded-md bg-secondary/10 text-secondary font-bold text-[0.65rem] uppercase tracking-widest">{{ $class->unit->nama_unit }}</span>
                <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-500 font-bold text-[0.65rem] uppercase tracking-widest">Tingkat {{ $class->tingkat_kelas }}</span>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800">{{ $class->nama_kelas }}</h2>
            <p class="text-slate-500 font-medium text-sm mt-1"><i class="bi bi-mortarboard mr-1"></i> {{ $class->major->nama_jurusan ?? 'Umum' }}</p>
        </div>
        <div class="mt-6">
            <button onclick="openModal('promoteModal')" class="px-5 py-2.5 bg-secondary text-white rounded-xl text-sm font-bold hover:bg-blue-600 transition-all shadow-md shadow-blue-500/20">
                <i class="bi bi-arrow-up-right-square mr-2"></i> Proses Naik Kelas
            </button>
        </div>
    </div>

    <div class="bg-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-200 flex flex-col justify-center">
        <p class="text-indigo-200 text-[0.7rem] font-bold uppercase tracking-widest mb-1">Total Siswa</p>
        <h3 class="text-5xl font-extrabold">{{ $students->count() }}</h3>
        <p class="text-indigo-200 text-xs mt-2 font-medium">Siswa terdaftar di rombel ini.</p>
    </div>
</div>

<form action="{{ route($prefix . '.classes.promote', Crypt::encryptString($class->id)) }}" method="POST" id="promoteForm">
    @csrf
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h6 class="m-0 font-bold text-slate-800">Daftar Anggota Kelas</h6>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-secondary">
                <label for="selectAll" class="text-sm font-bold text-slate-600 cursor-pointer">Pilih Semua</label>
            </div>
        </div>

        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr class="text-slate-400 text-[0.7rem] uppercase tracking-wider font-bold">
                    <th class="p-4 text-center w-16">Pilih</th>
                    <th class="p-4">Nama Lengkap</th>
                    <th class="p-4">NIS / NIP</th>
                    <th class="p-4">Username</th>
                    <th class="p-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($students as $student)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-center">
                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox w-4 h-4 rounded text-secondary">
                    </td>
                    <td class="p-4 font-bold text-slate-700">{{ $student->nama_lengkap }}</td>
                    <td class="p-4 font-medium text-slate-600">{{ $student->nis_np }}</td>
                    <td class="p-4 text-slate-500 font-medium">@ {{ $student->username }}</td>
                    <td class="p-4 text-center">
                        <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 font-bold text-[0.65rem] uppercase tracking-widest border border-emerald-100">Aktif</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-400 font-medium">Belum ada siswa di kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

<div id="promoteModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="promoteOverlay" onclick="closeModal('promoteModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-sm mx-4 relative z-10 p-6">
        <h5 class="font-bold text-lg mb-4">Pilih Kelas Tujuan</h5>
        <select id="target_class_id" name="target_class_id" form="promoteForm" class="w-full p-3 rounded-xl border border-slate-200 mb-6 text-sm" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($availableClasses as $c)
            <option value="{{ $c->id }}">{{ $c->nama_kelas }} (Tingkat {{ $c->tingkat_kelas }})</option>
            @endforeach
        </select>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeModal('promoteModal')" class="px-4 py-2 text-slate-600 font-bold text-sm">Batal</button>
            <button onclick="document.getElementById('promoteForm').submit()" class="px-4 py-2 bg-secondary text-white rounded-xl font-bold text-sm">Proses Pindah</button>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.getElementById(modalId).classList.add('flex');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(modalId).classList.remove('flex');
    }
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection