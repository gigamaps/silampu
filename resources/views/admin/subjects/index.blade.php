@extends('layouts.dashboard')

@section('page_title', 'Kelola Mata Pelajaran Kelas')

@section('content')
@php
$prefix = auth()->user()->role === 'kurikulum' ? 'kurikulum' : 'admin';
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Mata Pelajaran</h6>
            @if(auth()->user()->role === 'kurikulum')
            <p class="text-xs text-slate-400 font-medium mt-1">Mengelola kurikulum pada unit <span class="font-bold text-primary">{{ $units->first()->nama_unit ?? '' }}</span></p>
            @endif
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <a href="{{ route($prefix . '.subjects.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors text-sm font-semibold text-decoration-none">
                <i class="bi bi-file-earmark-excel mr-2"></i> Export
            </a>
            <button onclick="openModal('addSubjectModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Mapel
            </button>
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route($prefix . '.subjects.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari unit, kelas, atau mapel..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route($prefix . '.subjects.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
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
                    <th class="p-4 border-b border-slate-200">Kelas</th>
                    <th class="p-4 border-b border-slate-200">Mata Pelajaran</th>
                    <th class="p-4 border-b border-slate-200">Guru Pengampu</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($subjects as $index => $subject)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $subjects->firstItem() + $index }}</td>
                    <td class="p-4"><span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 font-semibold text-xs border border-slate-200">{{ $subject->studentClass->unit->nama_unit ?? '-' }}</span></td>
                    <td class="p-4 font-bold text-slate-700">{{ $subject->studentClass->nama_kelas ?? '-' }}</td>
                    <td class="p-4 font-extrabold text-blue-600 tracking-tight">{{ $subject->nama_mapel }}</td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @forelse($subject->users as $teacher)
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100"><i class="bi bi-person-badge mr-1"></i>{{ $teacher->nama_lengkap }}</span>
                            @empty
                            <span class="text-slate-400 italic text-xs">Belum di-assign guru</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editSubject({{ $subject->id }})" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Mapel">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route($prefix . '.subjects.destroy', $subject->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Mapel">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400">
                        <i class="bi bi-book text-4xl mb-3 block"></i>
                        <p class="font-medium">Data mata pelajaran tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $subjects->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addSubjectModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addSubjectOverlay" onclick="closeModal('addSubjectModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="addSubjectContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Tambah Mata Pelajaran</h5>
            <button onclick="closeModal('addSubjectModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form action="{{ route($prefix . '.subjects.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Unit Sekolah <span class="text-red-500">*</span></label>
                    @if(auth()->user()->role === 'kurikulum')
                    @php
                    $firstUnit = $units->first();
                    $classesData = $firstUnit ? json_encode($firstUnit->classes) : '[]';
                    @endphp
                    <input type="hidden" id="unit_select_hidden" data-classes="{{ $classesData }}">
                    <div class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl border border-slate-200">
                        <i class="bi bi-building mr-2 text-slate-400"></i> {{ $firstUnit->nama_unit ?? '' }}
                    </div>
                    @else
                    <select id="unit_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        <option value="">-- Pilih Unit --</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" data-classes="{{ json_encode($unit->classes) }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Kelas <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        <option value="">-- Pilih Unit Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_mapel" placeholder="Contoh: Matematika Wajib" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Assign Guru Pengampu <small class="text-slate-400 font-normal">(Bisa pilih lebih dari satu)</small></label>
                    <div class="border border-slate-200 rounded-xl p-3 bg-slate-50 space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                        @forelse($teachers as $teacher)
                        <label class="flex items-center gap-2.5 cursor-pointer py-0.5">
                            <input type="checkbox" name="guru_ids[]" value="{{ $teacher->id }}" class="rounded text-secondary focus:ring-secondary/20 w-4 h-4 border-slate-300">
                            <span class="text-sm text-slate-700 font-medium">{{ $teacher->nama_lengkap }} ({{ $teacher->nis_np }})</span>
                        </label>
                        @empty
                        <span class="text-xs text-slate-400 italic">Belum ada guru yang terdaftar/aktif di unit ini.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addSubjectModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Mapel</button>
            </div>
        </form>
    </div>
</div>

<div id="editSubjectModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editSubjectOverlay" onclick="closeModal('editSubjectModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="editSubjectContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Edit Mata Pelajaran</h5>
            <button type="button" onclick="closeModal('editSubjectModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form id="editSubjectForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf @method('PUT')
            <input type="hidden" name="class_id" id="edit_class_id">

            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Sekolah</label>
                    <input type="text" id="edit_unit_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-sm text-slate-400 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Kelas</label>
                    <input type="text" id="edit_class_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-sm text-slate-400 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_mapel" id="edit_nama_mapel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Assign Guru Pengampu</label>
                    <div class="border border-slate-200 rounded-xl p-3 bg-slate-50 space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($teachers as $teacher)
                        <label class="flex items-center gap-2.5 cursor-pointer py-0.5">
                            <input type="checkbox" name="guru_ids[]" id="edit_guru_{{ $teacher->id }}" value="{{ $teacher->id }}" class="edit-guru-checkbox rounded text-secondary focus:ring-secondary/20 w-4 h-4 border-slate-300">
                            <span class="text-sm text-slate-700 font-medium">{{ $teacher->nama_lengkap }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editSubjectModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addSubjectModal' ? 'addSubjectOverlay' : 'editSubjectOverlay');
        const content = document.getElementById(modalId === 'addSubjectModal' ? 'addSubjectContent' : 'editSubjectContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addSubjectModal' ? 'addSubjectOverlay' : 'editSubjectOverlay');
        const content = document.getElementById(modalId === 'addSubjectModal' ? 'addSubjectContent' : 'editSubjectContent');

        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Logic Menampilkan Dropdown Kelas Berdasarkan Data Unit
    function renderClassOptions(classesString, classSelectElement) {
        classSelectElement.innerHTML = '<option value="">-- Pilih Kelas --</option>';
        if (classesString && classesString !== '[]') {
            const classesArr = JSON.parse(classesString);
            if (classesArr.length > 0) {
                classesArr.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.textContent = cls.nama_kelas;
                    classSelectElement.appendChild(opt);
                });
            } else {
                classSelectElement.innerHTML = '<option value="">-- Tidak ada kelas di unit ini --</option>';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit_select');
        const hiddenUnitSelect = document.getElementById('unit_select_hidden');
        const classSelect = document.getElementById('class_select');

        // Jika Kurikulum (Pakai elemen hidden untuk trigger datanya)
        if (hiddenUnitSelect) {
            renderClassOptions(hiddenUnitSelect.getAttribute('data-classes'), classSelect);
        }
        // Jika Admin (Pakai onchange select)
        else if (unitSelect) {
            unitSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    renderClassOptions(selectedOption.getAttribute('data-classes'), classSelect);
                } else {
                    classSelect.innerHTML = '<option value="">-- Pilih Unit Terlebih Dahulu --</option>';
                }
            });
        }
    });

    function editSubject(subjectId) {
        document.querySelectorAll('.edit-guru-checkbox').forEach(chk => chk.checked = false);

        fetch(`/{{ $prefix }}/subjects/${subjectId}/edit`)
            .then(response => response.json())
            .then(data => {
                const sub = data.subject;

                document.getElementById('edit_class_id').value = sub.class_id;
                document.getElementById('edit_unit_name').value = sub.student_class.unit.nama_unit;
                document.getElementById('edit_class_name').value = sub.student_class.nama_kelas;
                document.getElementById('edit_nama_mapel').value = sub.nama_mapel;

                if (sub.users && sub.users.length > 0) {
                    sub.users.forEach(teacher => {
                        const checkbox = document.getElementById(`edit_guru_${teacher.id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                document.getElementById('editSubjectForm').action = `/{{ $prefix }}/subjects/${subjectId}`;
                openModal('editSubjectModal');
            });
    }
</script>
@endsection