@extends('layouts.dashboard')

@section('page_title', 'Detail Diskusi')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.forums.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:text-secondary hover:border-secondary hover:shadow-sm transition-all text-decoration-none w-fit">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Diskusi
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
    <div class="p-6 sm:p-8">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-extrabold text-xl uppercase shadow-md ring-4 ring-blue-50 shrink-0 border border-blue-100">
                    {{ substr($forum->teacher->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <h6 class="font-extrabold text-slate-800 text-base m-0 flex items-center gap-1.5">
                        {{ $forum->teacher->nama_lengkap }}
                        <i class="bi bi-patch-check-fill text-blue-500 text-sm" title="Pengajar Terverifikasi"></i>
                    </h6>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[0.7rem] text-slate-500 uppercase tracking-widest font-bold">Pengajar Utama</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-[0.7rem] text-slate-400 font-medium"><i class="bi bi-clock-history mr-1"></i>{{ $forum->created_at->format('d M Y - H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($forum->status === 'open')
                <span class="px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg text-xs font-bold uppercase tracking-widest text-emerald-600 flex items-center gap-1.5">
                    <i class="bi bi-unlock-fill"></i> Diskusi Terbuka
                </span>
                @else
                <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold uppercase tracking-widest text-slate-500 flex items-center gap-1.5">
                    <i class="bi bi-lock-fill"></i> Diskusi Ditutup
                </span>
                @endif
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 leading-tight mb-4">{{ $forum->judul }}</h1>
        <div class="prose prose-sm sm:prose-base max-w-none text-slate-600 leading-relaxed whitespace-pre-wrap bg-slate-50/50 p-5 rounded-2xl border border-slate-100">{{ $forum->deskripsi }}</div>
    </div>
</div>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <h4 class="font-extrabold text-slate-800 text-lg m-0 flex items-center gap-2">
            <i class="bi bi-chat-text text-secondary"></i> Forum Balasan
        </h4>
        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $forum->replies->count() }} Komentar</span>
    </div>

    @if($forum->status === 'open')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex gap-4 transition-all focus-within:border-secondary focus-within:shadow-md">
        <div class="w-12 h-12 shrink-0 rounded-full bg-primary text-white flex items-center justify-center font-extrabold text-lg uppercase shadow-sm border border-white/20">
            {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
        </div>
        <form action="{{ route('siswa.forums.reply', Crypt::encryptString($forum->id)) }}" method="POST" class="flex-1">
            @csrf
            <textarea name="konten" rows="3" placeholder="Tulis pertanyaan atau tanggapan Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar mb-3 transition-colors bg-slate-50 focus:bg-white" required></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600 transition-all shadow-sm shadow-secondary/30"><i class="bi bi-send-fill mr-2"></i>Kirim Tanggapan</button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-center">
        <div class="w-14 h-14 bg-white text-slate-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-sm border border-slate-100">
            <i class="bi bi-lock-fill"></i>
        </div>
        <h6 class="font-extrabold text-slate-700 text-base mb-1">Diskusi Telah Berakhir</h6>
        <p class="text-sm text-slate-500 font-medium m-0">Pengajar telah menutup topik ini. Anda hanya dapat membaca riwayat diskusi.</p>
    </div>
    @endif

    <div class="space-y-5 pb-10">
        @forelse($forum->replies as $reply)
        <div class="bg-white rounded-2xl border {{ $reply->user->role === 'guru' ? 'border-blue-200 bg-blue-50/10' : 'border-slate-200' }} shadow-sm p-5 flex flex-col gap-4">
            <div class="flex gap-4">
                <div class="w-12 h-12 shrink-0 rounded-full {{ $reply->user->role === 'guru' ? 'bg-secondary' : 'bg-primary' }} text-white flex items-center justify-center font-bold text-lg uppercase shadow-sm border border-white/20">
                    {{ substr($reply->user->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <h6 class="font-extrabold text-slate-800 text-[0.95rem] m-0">{{ $reply->user->nama_lengkap }}</h6>

                        @if($reply->user->role === 'guru')
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold text-[0.65rem] uppercase tracking-widest"><i class="bi bi-patch-check-fill mr-1"></i>Pengajar</span>
                        @else
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[0.65rem] uppercase tracking-widest">Siswa</span>
                        @endif

                        <span class="text-[0.7rem] text-slate-400 font-medium ml-auto shrink-0"><i class="bi bi-clock mr-1"></i>{{ $reply->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $reply->konten }}</div>

                    @if($forum->status === 'open')
                    <button onclick="toggleReplyForm({{ $reply->id }})" class="text-xs font-bold text-slate-500 hover:text-secondary mt-3 flex items-center gap-1.5 bg-transparent border-none p-0 cursor-pointer transition-colors">
                        <i class="bi bi-reply-fill text-lg leading-none"></i> Balas
                    </button>
                    @endif
                </div>
            </div>

            @if($reply->childReplies->count() > 0)
            <div class="ml-16 mt-2 space-y-3 border-l-2 border-slate-200 pl-5">
                @foreach($reply->childReplies as $child)
                <div class="flex gap-3 relative">
                    <div class="absolute -left-5 top-4 w-4 h-0.5 bg-slate-200"></div>

                    <div class="w-8 h-8 shrink-0 rounded-full {{ $child->user->role === 'guru' ? 'bg-secondary' : 'bg-primary' }} text-white flex items-center justify-center font-bold text-[0.65rem] uppercase shadow-sm z-10 relative border border-white/20">
                        {{ substr($child->user->nama_lengkap, 0, 1) }}
                    </div>
                    <div class="flex-1 bg-slate-50 p-4 rounded-xl border {{ $child->user->role === 'guru' ? 'border-blue-100' : 'border-slate-100' }}">
                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                            <h6 class="font-bold text-slate-700 text-sm m-0 flex items-center gap-1">
                                {{ $child->user->nama_lengkap }}
                                @if($child->user->role === 'guru')
                                <i class="bi bi-patch-check-fill text-blue-500" title="Pengajar"></i>
                                @endif
                            </h6>
                            <span class="text-[0.65rem] text-slate-400 font-medium ml-auto">{{ $child->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap">{{ $child->konten }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div id="replyForm_{{ $reply->id }}" class="hidden ml-16 mt-2">
                <form action="{{ route('siswa.forums.reply', Crypt::encryptString($forum->id)) }}" method="POST" class="flex flex-col gap-3 bg-white p-4 rounded-xl border border-secondary/30 shadow-sm relative overflow-hidden">
                    @csrf
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-secondary"></div>

                    <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                    <p class="text-xs font-bold text-secondary m-0">Membalas {{ $reply->user->nama_lengkap }}...</p>
                    <textarea name="konten" rows="2" placeholder="Tulis balasan Anda..." class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar bg-slate-50 focus:bg-white transition-colors" required></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleReplyForm({{ $reply->id }})" class="px-4 py-2 rounded-lg font-bold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg font-bold text-xs text-white bg-secondary hover:bg-blue-600 transition-colors shadow-sm shadow-secondary/30">Kirim Balasan</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl bg-white">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="bi bi-chat-square-text"></i>
            </div>
            <h6 class="font-extrabold text-slate-700 text-base mb-1">Jadilah yang Pertama</h6>
            <p class="text-sm text-slate-400 font-medium m-0">Belum ada percakapan. Mulai diskusi dengan mengirimkan tanggapan Anda di atas!</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    function toggleReplyForm(replyId) {
        const form = document.getElementById(`replyForm_${replyId}`);
        if (form.classList.contains('hidden')) {
            // Tutup semua form yang sedang terbuka
            document.querySelectorAll('[id^="replyForm_"]').forEach(f => f.classList.add('hidden'));
            // Buka form yang dipilih
            form.classList.remove('hidden');
            form.querySelector('textarea').focus();
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection