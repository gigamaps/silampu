@extends('layouts.dashboard')

@section('page_title', 'Kelola Unit Sekolah')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Unit Tersedia</h6>
        <button onclick="openModal('addUnitModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Unit
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Nama Unit</th>
                    <th class="p-4 border-b border-slate-200">Alamat</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($units as $index => $unit)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                    <td class="p-4 font-bold text-slate-800">{{ $unit->nama_unit }}</td>
                    <td class="p-4 text-slate-500 max-w-md truncate">{{ $unit->alamat ?? '-' }}</td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editUnit({{ $unit->id }})" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Unit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus unit ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Unit">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center text-slate-400">
                        <i class="bi bi-inboxes text-4xl mb-3 block"></i>
                        <p class="font-medium">Belum ada data unit yang ditambahkan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="addUnitModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addUnitOverlay" onclick="closeModal('addUnitModal')"></div>

    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="addUnitContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Tambah Unit Baru</h5>
            <button onclick="closeModal('addUnitModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>

        <form action="{{ route('admin.units.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Unit <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_unit" placeholder="Contoh: SMA Cakrawala" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" placeholder="Masukkan alamat operasional unit..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addUnitModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<div id="editUnitModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editUnitOverlay" onclick="closeModal('editUnitModal')"></div>

    <div class="bg-white rounded-2xl w-full max-w-md mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="editUnitContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0">Edit Unit Sekolah</h5>
            <button type="button" onclick="closeModal('editUnitModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>

        <form id="editUnitForm" method="POST">
            @csrf @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Unit <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_unit" id="edit_nama_unit" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" id="edit_alamat" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editUnitModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addUnitModal' ? 'addUnitOverlay' : 'editUnitOverlay');
        const content = document.getElementById(modalId === 'addUnitModal' ? 'addUnitContent' : 'editUnitContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId === 'addUnitModal' ? 'addUnitOverlay' : 'editUnitOverlay');
        const content = document.getElementById(modalId === 'addUnitModal' ? 'addUnitContent' : 'editUnitContent');

        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Fungsi Fetch data Unit ketika tombol Edit diklik
    function editUnit(unitId) {
        fetch(`/admin/units/${unitId}/edit`)
            .then(response => response.json())
            .then(data => {
                // Isi input modal edit dengan data respon DB
                document.getElementById('edit_nama_unit').value = data.unit.nama_unit;
                document.getElementById('edit_alamat').value = data.unit.alamat ?? '';

                // Set Action target form update tujuan
                document.getElementById('editUnitForm').action = `/admin/units/${unitId}`;

                // Buka modal edit unit
                openModal('editUnitModal');
            });
    }
</script>
@endsection