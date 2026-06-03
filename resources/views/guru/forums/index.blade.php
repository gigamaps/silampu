@extends('layouts.dashboard')

@section('page_title', 'Kelola Forum Diskusi')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Ruang Diskusi Kelas</h6>
            <p class="text-xs text-slate-400 font-medium mt-1">Buat topik diskusi baru untuk memacu interaksi dan keaktifan siswa Anda.</p>
        </div>
        <button onclick="openModal('addForumModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
            <i class="bi bi-chat-right-text-fill mr-2"></i> Buat Topik Baru
        </button>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route('guru.forums.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik diskusi..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route('guru.forums.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Topik Diskusi</th>
                    <th class="p-4 border-b border-slate-200">Target Kelas</th>
                    <th class="p-4 border-b border-slate-200 text-center">Status</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($forums as $index => $forum)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $forums->firstItem() + $index }}</td>
                    <td class="p-4">
                        <a href="{{ route('guru.forums.show', $forum->encrypted_id) }}" class="font-bold text-secondary hover:text-blue-700 leading-snug block mb-1 text-decoration-none">
                            {{ $forum->judul }}
                        </a>
                        <span class="text-[0.7rem] text-slate-400 font-medium">Dibuat: {{ $forum->created_at->format('d M Y') }}</span>
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-slate-700 block">{{ $forum->targetClass->nama_kelas ?? 'Kelas Dihapus' }}</span>
                        <span class="text-[0.7rem] text-slate-500 uppercase tracking-widest font-bold">{{ $forum->targetClass->unit->nama_unit ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-center">
                        @if($forum->status === 'open')
                        <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 font-bold text-[0.65rem] border border-emerald-100 uppercase tracking-widest"><i class="bi bi-unlock-fill mr-1"></i> Dibuka</span>
                        @else
                        <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-500 font-bold text-[0.65rem] border border-slate-200 uppercase tracking-widest"><i class="bi bi-lock-fill mr-1"></i> Ditutup</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('guru.forums.show', $forum->encrypted_id) }}" class="p-2 bg-slate-50 hover:bg-secondary border border-slate-200 hover:border-secondary text-slate-600 hover:text-white rounded-lg transition-colors" title="Lihat Diskusi">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <button onclick="editForum('{{ $forum->encrypted_id }}')" class="p-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Forum">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('guru.forums.destroy', $forum->encrypted_id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Forum">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-400">
                        <i class="bi bi-chat-square-text text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Anda belum membuat forum diskusi apapun.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $forums->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addForumModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addForumOverlay" onclick="closeModal('addForumModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-xl mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="addForumContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-chat-left-dots text-secondary mr-2"></i> Buka Diskusi Baru</h5>
            <button onclick="closeModal('addForumModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form action="{{ route('guru.forums.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Topik Diskusi <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" placeholder="Contoh: Tanya Jawab Bab Logaritma Dasar" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Target Kelas <span class="text-red-500">*</span></label>
                        <select name="class_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($myClasses as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->nama_kelas }} ({{ $cls->unit->nama_unit ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status Awal</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                            <option value="open">Dibuka (Siswa bisa membalas)</option>
                            <option value="closed">Ditutup (Read-only)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Deskripsi / Pemantik Diskusi <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" rows="4" placeholder="Tuliskan pertanyaan pemantik atau instruksi diskusi di sini..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar" required></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addForumModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Buat Forum</button>
            </div>
        </form>
    </div>
</div>

<div id="editForumModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editForumOverlay" onclick="closeModal('editForumModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-xl mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="editForumContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-pencil-square text-secondary mr-2"></i> Edit Data Forum</h5>
            <button type="button" onclick="closeModal('editForumModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>
        <form id="editForumForm" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Topik Diskusi <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="edit_judul" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Target Kelas <span class="text-red-500">*</span></label>
                        <select name="class_id" id="edit_class_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                            @foreach($myClasses as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->nama_kelas }} ({{ $cls->unit->nama_unit ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status Akses</label>
                        <select name="status" id="edit_status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                            <option value="open">Dibuka (Siswa bisa membalas)</option>
                            <option value="closed">Ditutup (Terkunci)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Deskripsi / Pemantik</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar" required></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editForumModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = modal.querySelector('div[id$="Overlay"]');
        const content = modal.querySelector('div[id$="Content"]');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = modal.querySelector('div[id$="Overlay"]');
        const content = modal.querySelector('div[id$="Content"]');
        overlay.classList.add('opacity-0');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function editForum(encryptedId) {
        fetch(`/guru/forums/${encryptedId}/edit`)
            .then(response => response.json())
            .then(data => {
                const frm = data.forum;
                document.getElementById('edit_judul').value = frm.judul;
                document.getElementById('edit_class_id').value = frm.class_id;
                document.getElementById('edit_status').value = frm.status;
                document.getElementById('edit_deskripsi').value = frm.deskripsi;
                document.getElementById('editForumForm').action = `/guru/forums/${frm.encrypted_id}`;
                openModal('editForumModal');
            });
    }
</script>
@endsection