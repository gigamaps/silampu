@extends('layouts.dashboard')

@section('page_title', 'Kelola Ruang Kelas')

@section('content')
@php
$prefix = auth()->user()->role === 'kurikulum' ? 'kurikulum' : 'admin';
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Kelas Tersedia</h6>
            @if(auth()->user()->role === 'kurikulum')
            <p class="text-xs text-slate-400 font-medium mt-1">Mengelola rombongan belajar pada unit <span class="font-bold text-primary">{{ $units->first()->nama_unit ?? '' }}</span></p>
            @endif
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <a href="{{ route($prefix . '.classes.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors text-sm font-semibold text-decoration-none">
                <i class="bi bi-file-earmark-excel mr-2"></i> Export
            </a>
            <button onclick="openModal('addClassModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Kelas
            </button>
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route($prefix . '.classes.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari unit, jurusan, atau kelas..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route($prefix . '.classes.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
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
                    <th class="p-4 border-b border-slate-200">Jurusan</th>
                    <th class="p-4 border-b border-slate-200 text-center">Tingkat</th>
                    <th class="p-4 border-b border-slate-200">Nama Kelas</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($classes as $index => $class)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $classes->firstItem() + $index }}</td>
                    <td class="p-4 font-bold text-slate-800">{{ $class->unit->nama_unit ?? '-' }}</td>
                    <td class="p-4 text-slate-600">{{ $class->major->nama_jurusan ?? 'Umum (Tanpa Jurusan)' }}</td>
                    <td class="p-4 text-center">
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200">
                            Kelas {{ $class->tingkat_kelas }}
                        </span>
                    </td>
                    <td class="p-4 font-extrabold text-secondary">
                        <a href="{{ route($prefix . '.classes.show', Crypt::encryptString($class->id)) }}" class="hover:underline">{{ $class->nama_kelas }}</a>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">

                            <button onclick="editClass({{ $class->id }})" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Kelas">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route($prefix . '.classes.destroy', $class->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Kelas">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400">
                        <i class="bi bi-door-open text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Data ruang kelas tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $classes->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addClassModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addClassOverlay" onclick="closeModal('addClassModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="addClassContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Tambah Kelas Baru</h5>
            <button onclick="closeModal('addClassModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form action="{{ route($prefix . '.classes.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Sekolah <span class="text-red-500">*</span></label>
                    @if(auth()->user()->role === 'kurikulum')

                    @php
                    $firstUnit = $units->first();
                    $majorsData = $firstUnit ? json_encode($firstUnit->majors) : '[]';
                    $unitNameStr = $firstUnit ? strtolower($firstUnit->nama_unit) : '';
                    @endphp

                    <input type="hidden" name="unit_id" id="unit_select_hidden" value="{{ $firstUnit->id ?? '' }}" data-majors="{{ $majorsData }}" data-name="{{ $unitNameStr }}">

                    <div class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl border border-slate-200">
                        <i class="bi bi-building mr-2 text-slate-400"></i> {{ $firstUnit->nama_unit ?? '' }}
                    </div>

                    @else
                    <select name="unit_id" id="unit_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" data-majors="{{ json_encode($unit->majors) }}" data-name="{{ strtolower($unit->nama_unit) }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Jurusan <small class="text-slate-400 font-normal">(Opsional)</small></label>
                    <select name="jurusan_id" id="jurusan_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                        <option value="">-- Pilih Unit Terlebih Dahulu --</option>
                    </select>
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tingkat <span class="text-red-500">*</span></label>
                        <select name="tingkat_kelas" id="tingkat_select" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                            <option value="">Pilih...</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" placeholder="Contoh: X TKJ 1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addClassModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<div id="editClassModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editClassOverlay" onclick="closeModal('editClassModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="editClassContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Edit Ruang Kelas</h5>
            <button type="button" onclick="closeModal('editClassModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form id="editClassForm" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="unit_id" id="edit_unit_id">

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Sekolah</label>
                    <input type="text" id="edit_unit_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-sm text-slate-500 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Jurusan <small class="text-slate-400 font-normal">(Opsional)</small></label>
                    <select name="jurusan_id" id="edit_jurusan_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                    </select>
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tingkat <span class="text-red-500">*</span></label>
                        <select name="tingkat_kelas" id="edit_tingkat_select" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editClassModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addClassModal' ? 'addClassOverlay' : 'editClassOverlay');
        const content = document.getElementById(modalId === 'addClassModal' ? 'addClassContent' : 'editClassContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addClassModal' ? 'addClassOverlay' : 'editClassOverlay');
        const content = document.getElementById(modalId === 'addClassModal' ? 'addClassContent' : 'editClassContent');

        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function populateDropdowns(unitName, majors, targetJurusanElement, targetTingkatElement) {
        targetJurusanElement.innerHTML = '<option value="">-- Tanpa Jurusan (Umum) --</option>';
        targetTingkatElement.innerHTML = '<option value="">Pilih...</option>';

        if (majors && majors.length > 0) {
            majors.forEach(major => {
                const opt = document.createElement('option');
                opt.value = major.id;
                opt.textContent = major.nama_jurusan;
                targetJurusanElement.appendChild(opt);
            });
        }

        let tingkatOptions = [];
        if (unitName.includes('sd')) {
            tingkatOptions = [1, 2, 3, 4, 5, 6];
        } else if (unitName.includes('smp')) {
            tingkatOptions = [7, 8, 9];
        } else if (unitName.includes('sma') || unitName.includes('smk')) {
            tingkatOptions = [10, 11, 12];
        } else {
            tingkatOptions = [7, 8, 9, 10, 11, 12];
        }

        tingkatOptions.forEach(tingkat => {
            const opt = document.createElement('option');
            opt.value = tingkat;
            opt.textContent = `Kelas ${tingkat}`;
            targetTingkatElement.appendChild(opt);
        });
    }

    // --- LOGIKA JS BERSIH TANPA PHP RENTAN PRETTIER ---
    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit_select');
        const hiddenUnitSelect = document.getElementById('unit_select_hidden');
        const jurusanSelect = document.getElementById('jurusan_select');
        const tingkatSelect = document.getElementById('tingkat_select');

        // Jika yang login Kurikulum, pakai data dari elemen hidden HTML
        if (hiddenUnitSelect) {
            const majorsString = hiddenUnitSelect.getAttribute('data-majors');
            const unitNameString = hiddenUnitSelect.getAttribute('data-name');

            // Parse JSON dengan aman
            let myMajors = [];
            if (majorsString && majorsString !== '[]') {
                myMajors = JSON.parse(majorsString);
            }

            populateDropdowns(unitNameString, myMajors, jurusanSelect, tingkatSelect);
        }
        // Jika Admin, pakai select dropdown
        else if (unitSelect) {
            unitSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const majors = JSON.parse(selectedOption.getAttribute('data-majors'));
                    const unitName = selectedOption.getAttribute('data-name');
                    populateDropdowns(unitName, majors, jurusanSelect, tingkatSelect);
                } else {
                    jurusanSelect.innerHTML = '<option value="">-- Pilih Unit Terlebih Dahulu --</option>';
                    tingkatSelect.innerHTML = '<option value="">Pilih...</option>';
                }
            });
        }
    });

    function editClass(classId) {
        fetch(`/{{ $prefix }}/classes/${classId}/edit`)
            .then(response => response.json())
            .then(data => {
                const classData = data.class;
                const unitName = classData.unit.nama_unit.toLowerCase();
                const majors = classData.unit.majors;

                const editJurusanSelect = document.getElementById('edit_jurusan_select');
                const editTingkatSelect = document.getElementById('edit_tingkat_select');

                document.getElementById('edit_unit_id').value = classData.unit_id;
                document.getElementById('edit_unit_name').value = classData.unit.nama_unit;
                document.getElementById('edit_nama_kelas').value = classData.nama_kelas;

                populateDropdowns(unitName, majors, editJurusanSelect, editTingkatSelect);

                editJurusanSelect.value = classData.jurusan_id ?? '';
                editTingkatSelect.value = classData.tingkat_kelas;

                document.getElementById('editClassForm').action = `/{{ $prefix }}/classes/${classId}`;
                openModal('editClassModal');
            });
    }
</script>
@endsection