@extends('layouts.dashboard')

@section('page_title', 'Video Materi Saya')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h6 class="m-0 font-bold text-lg text-slate-800">Daftar Video Pembelajaran Anda</h6>
            <p class="text-xs text-slate-400 font-medium mt-1">Unggah dan kelola video yang dikhususkan untuk kelas Anda.</p>
        </div>
        <button onclick="openModal('addVideoModal')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-xl hover:bg-blue-600 shadow-sm transition-colors text-sm font-semibold">
            <i class="bi bi-cloud-arrow-up-fill mr-2"></i> Unggah Video Baru
        </button>
    </div>

    <div class="p-4 bg-slate-50/50 border-b border-slate-100">
        <form method="GET" action="{{ route('guru.videos.index') }}" class="flex flex-col sm:flex-row gap-3 justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500">Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                    <option value="30" {{ request('per_page') == '30' ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                </select>
                <span class="text-sm font-medium text-slate-500">data</span>
            </div>

            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau mata pelajaran..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('search'))
                <a href="{{ route('guru.videos.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"><i class="bi bi-x-circle-fill"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[0.75rem] uppercase tracking-wider font-bold">
                    <th class="p-4 border-b border-slate-200 text-center w-16">No</th>
                    <th class="p-4 border-b border-slate-200">Judul Video</th>
                    <th class="p-4 border-b border-slate-200">Mata Pelajaran & Kelas</th>
                    <th class="p-4 border-b border-slate-200 text-center">Status</th>
                    <th class="p-4 border-b border-slate-200 text-center">Modul</th>
                    <th class="p-4 border-b border-slate-200 text-center">Tayangan</th>
                    <th class="p-4 border-b border-slate-200 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($videos as $index => $video)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 text-center text-slate-400 font-medium">{{ $videos->firstItem() + $index }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0 w-28 aspect-[16/9] rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shadow-sm">
                                <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 right-1 bg-slate-900/80 text-white font-mono text-[0.6rem] px-1.5 py-0.5 rounded font-bold">{{ $video->durasi ?? '--:--' }}</span>
                            </div>
                            <div class="font-bold text-slate-800 leading-snug max-w-[200px] truncate" title="{{ $video->judul }}">{{ $video->judul }}</div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-slate-700 block">{{ $video->subject->nama_mapel ?? 'Mapel Terhapus' }}</span>
                        <span class="text-xs font-semibold text-slate-500">{{ $video->subject->studentClass->nama_kelas ?? '-' }} • {{ $video->unit->nama_unit ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-center">
                        @if($video->status == 'public')
                        <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 font-bold text-[0.65rem] border border-emerald-100 uppercase">Publik</span>
                        @else
                        <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-500 font-bold text-[0.65rem] border border-slate-200 uppercase">Privat</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($video->file_modul)
                        <span class="text-blue-500" title="Dilengkapi Modul"><i class="bi bi-file-earmark-pdf-fill text-lg"></i></span>
                        @else
                        <span class="text-slate-300" title="Tidak Ada Modul"><i class="bi bi-dash-lg"></i></span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="font-extrabold text-slate-800"><i class="bi bi-eye text-slate-400 mr-1"></i>{{ number_format($video->views) }}</div>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('videos.show', $video->slug) }}" class="p-2 bg-slate-50 hover:bg-secondary border border-slate-200 hover:border-secondary text-slate-600 hover:text-white rounded-lg transition-colors" title="Tonton Video">
                                <i class="bi bi-play-fill"></i>
                            </a>
                            <a href="{{ route('guru.videos.stats', $video->slug) }}" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-lg transition-colors" title="Lihat Statistik">
                                <i class="bi bi-graph-up-arrow"></i>
                            </a>
                            <button onclick="editVideo({{ $video->id }})" class="p-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition-colors" title="Edit Video">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('guru.videos.destroy', $video->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Hapus Video" onclick="return confirm('Yakin ingin menghapus video dan modul ini?');">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-12 text-center text-slate-400">
                        <i class="bi bi-camera-video-off text-4xl mb-3 block text-slate-300"></i>
                        <p class="font-medium">Anda belum mengunggah video materi apapun.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $videos->onEachSide(1)->links('partials.pagination') }}
    </div>
</div>

<div id="addVideoModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="addVideoOverlay" onclick="closeModal('addVideoModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="addVideoContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-cloud-arrow-up text-secondary mr-2"></i> Unggah Video Materi</h5>
            <button onclick="closeModal('addVideoModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>

        <form action="{{ route('guru.videos.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" placeholder="Contoh: Logaritma Dasar Kelas 10" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Tautan YouTube <span class="text-red-500">*</span></label>
                    <input type="url" name="youtube_url" placeholder="https://youtube.com/watch?v=..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">File Modul <small class="text-slate-400 font-normal">(Opsional, max 5MB, format PDF/DOC/DOCX)</small></label>
                    <input type="file" name="file_modul" accept=".pdf,.doc,.docx" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Kaitkan ke Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select name="subject_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mySubjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->studentClass->nama_kelas }} - {{ $subj->nama_mapel }}</option>
                            @endforeach
                        </select>
                        <p class="text-[0.65rem] text-slate-400 mt-1">Daftar ini menyesuaikan dengan kelas yang Anda ampu.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Visibilitas</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                            <option value="public">Publik (Bisa dilihat siapa saja)</option>
                            <option value="private">Privat (Hanya Internal)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Durasi Video <small class="text-slate-400 font-normal">(Opsional)</small></label>
                        <input type="text" name="durasi" placeholder="Contoh: 12:45" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" rows="3" placeholder="Tulis catatan, rangkuman, atau soal latihan singkat..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('addVideoModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600">Publikasikan Materi</button>
            </div>
        </form>
    </div>
</div>

<div id="editVideoModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" id="editVideoOverlay" onclick="closeModal('editVideoModal')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg mx-4 relative z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="editVideoContent">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h5 class="font-bold text-slate-800 text-lg m-0"><i class="bi bi-pencil-square text-secondary mr-2"></i> Edit Data Materi</h5>
            <button type="button" onclick="closeModal('editVideoModal')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
        </div>

        <form id="editVideoForm" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="edit_judul" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Tautan YouTube <span class="text-red-500">*</span></label>
                    <input type="url" name="youtube_url" id="edit_youtube_url" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Perbarui Modul <small class="text-slate-400 font-normal">(Opsional. Abaikan jika tidak ingin diubah)</small></label>
                    <input type="file" name="file_modul" accept=".pdf,.doc,.docx" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer mb-2">

                    <div id="edit_modul_container" class="hidden text-xs bg-blue-50 text-blue-700 p-2 rounded-lg border border-blue-100 flex items-center justify-between">
                        <span><i class="bi bi-file-earmark-check-fill mr-1"></i> Modul saat ini tersedia.</span>
                        <a id="edit_modul_link" href="#" target="_blank" class="font-bold hover:underline">Lihat Modul</a>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Kaitkan ke Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="subject_id" id="edit_subject_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" required>
                        @foreach($mySubjects as $subj)
                        <option value="{{ $subj->id }}">{{ $subj->studentClass->nama_kelas }} - {{ $subj->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Visibilitas</label>
                        <select name="status" id="edit_status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary">
                            <option value="public">Publik</option>
                            <option value="private">Privat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Durasi Video</label>
                        <input type="text" name="durasi" id="edit_durasi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeModal('editVideoModal')" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-50">Batal</button>
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

    function editVideo(videoId) {
        fetch(`/guru/videos/${videoId}/edit`)
            .then(response => response.json())
            .then(data => {
                const vid = data.video;
                document.getElementById('edit_judul').value = vid.judul;
                document.getElementById('edit_youtube_url').value = vid.youtube_url;
                document.getElementById('edit_subject_id').value = vid.subject_id;
                document.getElementById('edit_status').value = vid.status;
                document.getElementById('edit_durasi').value = vid.durasi ?? '';
                document.getElementById('edit_deskripsi').value = vid.deskripsi ?? '';

                // Logika menampilkan indikator modul
                const modulContainer = document.getElementById('edit_modul_container');
                const modulLink = document.getElementById('edit_modul_link');

                if (vid.file_modul) {
                    modulContainer.classList.remove('hidden');
                    // Asumsikan Storage disk public
                    modulLink.href = `/storage/${vid.file_modul}`;
                } else {
                    modulContainer.classList.add('hidden');
                }

                document.getElementById('editVideoForm').action = `/guru/videos/${videoId}`;
                openModal('editVideoModal');
            });
    }
</script>
@endsection