@extends('layouts.dashboard')

@section('page_title', 'Kelola Jurusan')

@section('content')
@php
// Deteksi prefix rute berdasarkan role user secara dinamis
$prefix = auth()->user()->role === 'kurikulum' ? 'kurikulum' : 'admin';
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Jurusan</h6>
            @if(auth()->user()->role === 'kurikulum')
            <p class="text-xs text-slate-400 font-medium mt-1">Mengelola program studi khusus pada unit <span class="font-bold text-primary">{{ $units->first()->nama_unit ?? '' }}</span></p>
            @endif
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <a href="{{ route($prefix . '.majors.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors text-sm font-semibold text-decoration-none">
                <i class="bi bi-file-earmark-excel mr-2"></i> Export
            </a>
            <button onclick="openModal('addMajorModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Jurusan
            </button>
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route($prefix . '.majors.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jurusan atau unit..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route($prefix . '.majors.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Unit Sekolah</th>
                    <th class="p-4 border-b border-slate-200">Nama Jurusan</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($majors as $index => $major)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $majors->firstItem() + $index }}</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                            {{ $major->unit->nama_unit ?? 'Unit Dihapus' }}
                        </span>
                    </td>
                    <td class="p-4 font-bold text-slate-800">{{ $major->nama_jurusan }}</td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editMajor({{ $major->id }})" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Jurusan">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route($prefix . '.majors.destroy', $major->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Jurusan">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center text-slate-400">
                        <i class="bi bi-mortarboard text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Data jurusan tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $majors->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addMajorModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addMajorOverlay" onclick="closeModal('addMajorModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="addMajorContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Tambah Jurusan Baru</h5>
            <button onclick="closeModal('addMajorModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form action="{{ route($prefix . '.majors.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Sekolah <span class="text-red-500">*</span></label>
                    @if(auth()->user()->role === 'kurikulum')
                    <input type="hidden" name="unit_id" value="{{ $units->first()->id ?? '' }}">
                    <div class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl border border-slate-200">
                        <i class="bi bi-building mr-2 text-slate-400"></i> {{ $units->first()->nama_unit ?? '' }}
                    </div>
                    @else
                    <select name="unit_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addMajorModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Jurusan</button>
            </div>
        </form>
    </div>
</div>

<div id="editMajorModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editMajorOverlay" onclick="closeModal('editMajorModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="editMajorContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Edit Jurusan</h5>
            <button type="button" onclick="closeModal('editMajorModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form id="editMajorForm" method="POST">
            @csrf @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Sekolah <span class="text-red-500">*</span></label>
                    @if(auth()->user()->role === 'kurikulum')
                    <input type="hidden" name="unit_id" id="edit_unit_id">
                    <div class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl border border-slate-200">
                        <i class="bi bi-building mr-2 text-slate-400"></i> {{ $units->first()->nama_unit ?? '' }}
                    </div>
                    @else
                    <select name="unit_id" id="edit_unit_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_jurusan" id="edit_nama_jurusan" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editMajorModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addMajorModal' ? 'addMajorOverlay' : 'editMajorOverlay');
        const content = document.getElementById(modalId === 'addMajorModal' ? 'addMajorContent' : 'editMajorContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addMajorModal' ? 'addMajorOverlay' : 'editMajorOverlay');
        const content = document.getElementById(modalId === 'addMajorModal' ? 'addMajorContent' : 'editMajorContent');

        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function editMajor(majorId) {
        // Menggunakan variabel dinamis $prefix dari server-side
        fetch(`/{{ $prefix }}/majors/${majorId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_unit_id').value = data.major.unit_id;
                document.getElementById('edit_nama_jurusan').value = data.major.nama_jurusan;

                // Menata rute action update form mengikuti hak akses login
                document.getElementById('editMajorForm').action = `/{{ $prefix }}/majors/${majorId}`;
                openModal('editMajorModal');
            });
    }
</script>
@endsection