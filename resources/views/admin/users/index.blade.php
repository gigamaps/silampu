@extends('layouts.dashboard')

@section('page_title', 'Kelola Pengguna Sistem')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Akun Pengguna</h6>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.users.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors text-sm font-semibold text-decoration-none">
                <i class="bi bi-file-earmark-excel mr-2"></i> Export
            </a>
            <button onclick="openModal('importModal')" class="inline-flex items-center px-4 py-2 bg-slate-50 text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors text-sm font-semibold">
                <i class="bi bi-upload mr-2"></i> Import
            </button>
            <button onclick="openModal('addUserModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
                <i class="bi bi-person-plus-fill mr-2"></i> Tambah
            </button>
        </div>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau username..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Nama Lengkap</th>
                    <th class="p-4 border-b border-slate-200">NIS / NIP & Username</th>
                    <th class="p-4 border-b border-slate-200">Role</th>
                    <th class="p-4 border-b border-slate-200">Detail Kelas/Unit</th>
                    <th class="p-4 border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($users as $index => $user)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $users->firstItem() + $index }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="" class="w-9 h-9 rounded-full object-cover border border-slate-200 bg-slate-100" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=E2E8F0&color=475569'">
                            <span class="font-bold text-slate-800">{{ $user->nama_lengkap }}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-700">{{ $user->nis_np }}</div>
                        <div class="text-[0.75rem] font-medium text-slate-500 mt-0.5"><i class="bi bi-at"></i>{{ $user->username }}</div>
                    </td>
                    <td class="p-4">
                        @if($user->role == 'admin') <span class="px-2.5 py-1 rounded-md bg-red-50 text-red-600 font-bold text-xs">Admin</span>
                        @elseif($user->role == 'kurikulum') <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-600 font-bold text-xs">Kurikulum</span>
                        @elseif($user->role == 'guru') <span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-600 font-bold text-xs">Guru</span>
                        @else <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 font-bold text-xs">Siswa</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($user->role == 'siswa')
                        <span class="font-bold text-slate-700 block">{{ $user->studentClass->nama_kelas ?? 'Tanpa Kelas' }}</span>
                        <span class="text-xs text-slate-500 font-medium">{{ $user->studentClass->unit->nama_unit ?? '-' }}</span>
                        @elseif(($user->role == 'kurikulum' || $user->role == 'guru') && $user->units->isNotEmpty())
                        <span class="font-bold text-slate-700 block">{{ $user->role == 'guru' ? 'Pengajar' : 'Tim Kurikulum' }}</span>
                        <span class="text-xs text-slate-500 font-medium">{{ $user->units->pluck('nama_unit')->implode(', ') }}</span>
                        @else
                        <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editUser({{ $user->id }})" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Akun">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Akun">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400">
                        <i class="bi bi-people text-4xl mb-3 block"></i>
                        <p class="font-medium">Data tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $users->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addUserModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addUserOverlay" onclick="closeModal('addUserModal')"></div>

    <div class="bg-white rounded-2xl w-full max-w-3xl mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="addUserContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Tambah Pengguna Baru</h5>
            <button type="button" onclick="closeModal('addUserModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-xl"></i></button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" required autocomplete="off">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">NIS / NP <span class="text-red-500">*</span></label>
                        <input type="text" name="nis_np" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" required autocomplete="off">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Hak Akses (Role) <span class="text-red-500">*</span></label>
                        <select name="role" id="role_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" required>
                            <option value="">-- Pilih Hak Akses --</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="kurikulum">Tim Kurikulum</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" required autocomplete="off">
                    </div>

                    <div id="class_container" class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Penempatan Kelas <span class="text-red-500">*</span></label>
                        <select name="class_id" id="class_select" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->unit->nama_unit ?? 'Unit Terhapus' }} - {{ $class->nama_kelas }} (Tingkat {{ $class->tingkat_kelas }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="unit_container" class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Penempatan Unit <span class="text-red-500">*</span> <small class="text-slate-400 font-normal">(Bisa lebih dari satu)</small></label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($units as $unit)
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
                                <input type="checkbox" name="unit_ids[]" value="{{ $unit->id }}" class="unit-checkbox rounded text-secondary focus:ring-secondary/20 w-4 h-4 border-slate-300">
                                <span class="text-sm font-semibold text-slate-700">{{ $unit->nama_unit }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p id="unit_error_msg" class="text-xs text-red-500 mt-2 hidden"><i class="bi bi-exclamation-circle mr-1"></i>Harap pilih minimal 1 unit.</p>
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Password Sementara</label>
                        <input type="text" name="password" placeholder="Kosongkan untuk menyamakan dengan Username" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addUserModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" id="submitAddUser" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editUserOverlay" onclick="closeModal('editUserModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-3xl mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="editUserContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Edit Pengguna</h5>
            <button type="button" onclick="closeModal('editUserModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-xl"></i></button>
        </div>
        <form id="editUserForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="edit_nama" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">NIS / NIP</label>
                        <input type="text" name="nis_np" id="edit_nis" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Role</label>
                        <select name="role" id="edit_role" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm" onchange="toggleEditContainers(this.value)">
                            <option value="admin">Admin</option>
                            <option value="kurikulum">Kurikulum</option>
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Username (Readonly)</label>
                        <input type="text" name="username" id="edit_username" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-sm cursor-not-allowed" readonly>
                    </div>

                    <div id="edit_class_container" class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Kelas</label>
                        <select name="class_id" id="edit_class_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="edit_unit_container" class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Penempatan Unit <small class="text-slate-400 font-normal">(Bisa lebih dari satu)</small></label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($units as $unit)
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
                                <input type="checkbox" name="unit_ids[]" id="edit_unit_{{ $unit->id }}" value="{{ $unit->id }}" class="edit-unit-checkbox rounded text-secondary focus:ring-secondary/20 w-4 h-4 border-slate-300">
                                <span class="text-sm font-semibold text-slate-700">{{ $unit->nama_unit }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Password Baru</label>
                        <input type="text" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editUserModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="importModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="importOverlay" onclick="closeModal('importModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="importContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Impor Data Pengguna</h5>
            <button type="button" onclick="closeModal('importModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-xl"></i></button>
        </div>
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium m-0">Gunakan koma (,) untuk mengisi lebih dari 1 unit (Khusus Guru/Kurikulum).</p>
                        <a href="{{ route('admin.users.template') }}" class="text-sm font-bold text-blue-600 hover:underline mt-1 inline-block">Unduh Template Excel</a>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Upload File Excel (.xlsx / .csv)</label>
                    <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 cursor-pointer">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('importModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600"><i class="bi bi-upload mr-2"></i> Mulai Impor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addUserModal' ? 'addUserOverlay' : (modalId === 'editUserModal' ? 'editUserOverlay' : 'importOverlay'));
        const content = document.getElementById(modalId === 'addUserModal' ? 'addUserContent' : (modalId === 'editUserModal' ? 'editUserContent' : 'importContent'));

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addUserModal' ? 'addUserOverlay' : (modalId === 'editUserModal' ? 'editUserOverlay' : 'importOverlay'));
        const content = document.getElementById(modalId === 'addUserModal' ? 'addUserContent' : (modalId === 'editUserModal' ? 'editUserContent' : 'importContent'));

        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // --- FORM LOGIC (ADD) ---
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_select');
        const classContainer = document.getElementById('class_container');
        const classSelect = document.getElementById('class_select');
        const unitContainer = document.getElementById('unit_container');
        const unitCheckboxes = document.querySelectorAll('.unit-checkbox');
        const submitAddBtn = document.getElementById('submitAddUser');
        const unitErrorMsg = document.getElementById('unit_error_msg');

        roleSelect.addEventListener('change', function() {
            classContainer.classList.add('hidden');
            classSelect.removeAttribute('required');
            classSelect.value = '';

            unitContainer.classList.add('hidden');
            unitCheckboxes.forEach(chk => chk.checked = false); // Bersihkan centang

            if (this.value === 'siswa') {
                classContainer.classList.remove('hidden');
                classSelect.setAttribute('required', 'required');
            } else if (this.value === 'kurikulum' || this.value === 'guru') {
                unitContainer.classList.remove('hidden');
            }
        });

        // Validasi Manual: Pastikan minimal 1 unit tercentang jika role Guru/Kurikulum
        submitAddBtn.addEventListener('click', function(e) {
            if (roleSelect.value === 'kurikulum' || roleSelect.value === 'guru') {
                let isChecked = false;
                unitCheckboxes.forEach(chk => {
                    if (chk.checked) isChecked = true;
                });

                if (!isChecked) {
                    e.preventDefault(); // Cegah form dikirim
                    unitErrorMsg.classList.remove('hidden');
                } else {
                    unitErrorMsg.classList.add('hidden');
                }
            }
        });
    });

    // --- FORM LOGIC (EDIT) ---
    function toggleEditContainers(role) {
        const classCont = document.getElementById('edit_class_container');
        const unitCont = document.getElementById('edit_unit_container');

        classCont.classList.add('hidden');
        unitCont.classList.add('hidden');
        document.getElementById('edit_class_id').value = '';

        // Bersihkan centangan edit
        document.querySelectorAll('.edit-unit-checkbox').forEach(chk => chk.checked = false);

        if (role === 'siswa') {
            classCont.classList.remove('hidden');
        } else if (role === 'kurikulum' || role === 'guru') {
            unitCont.classList.remove('hidden');
        }
    }

    function editUser(userId) {
        fetch(`/admin/users/${userId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_nama').value = data.user.nama_lengkap;
                document.getElementById('edit_nis').value = data.user.nis_np;
                document.getElementById('edit_username').value = data.user.username;
                document.getElementById('edit_role').value = data.user.role;

                toggleEditContainers(data.user.role);

                if (data.user.role === 'siswa') {
                    document.getElementById('edit_class_id').value = data.user.class_id;
                } else if (data.user.role === 'kurikulum' || data.user.role === 'guru') {
                    if (data.user.units && data.user.units.length > 0) {
                        data.user.units.forEach(unit => {
                            const checkbox = document.getElementById(`edit_unit_${unit.id}`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                }

                document.getElementById('editUserForm').action = `/admin/users/${userId}`;
                openModal('editUserModal');
            });
    }
</script>
@endsection