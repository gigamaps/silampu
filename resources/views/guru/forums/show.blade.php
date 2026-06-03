@extends('layouts.dashboard')

@section('page_title', 'Detail Diskusi')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('guru.forums.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-secondary transition-colors text-decoration-none">
        <i class="bi bi-arrow-left-short text-xl mr-1"></i> Kembali ke Daftar Forum
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
    <div class="bg-slate-50/50 border-b border-slate-100 p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-lg uppercase shrink-0">
                {{ substr($forum->teacher->nama_lengkap, 0, 1) }}
            </div>
            <div>
                <h6 class="font-bold text-slate-800 text-sm m-0">{{ $forum->teacher->nama_lengkap }}</h6>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[0.65rem] text-slate-500 uppercase tracking-widest font-bold"><i class="bi bi-patch-check-fill text-blue-500 mr-1"></i>Pengajar</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-[0.65rem] text-slate-400 font-medium"><i class="bi bi-clock-history mr-1"></i>{{ $forum->created_at->format('d M Y - H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 bg-blue-50 border border-blue-100 rounded-lg text-[0.7rem] font-bold uppercase tracking-widest text-blue-600">
                <i class="bi bi-building mr-1.5"></i> {{ $forum->targetClass->nama_kelas ?? '-' }}
            </span>
            @if($forum->status === 'open')
            <span class="inline-flex items-center px-3 py-1 bg-emerald-50 border border-emerald-100 rounded-lg text-[0.7rem] font-bold uppercase tracking-widest text-emerald-600">
                <i class="bi bi-unlock-fill mr-1.5"></i> Terbuka
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 bg-red-50 border border-red-100 rounded-lg text-[0.7rem] font-bold uppercase tracking-widest text-red-600">
                <i class="bi bi-lock-fill mr-1.5"></i> Ditutup
            </span>
            @endif
        </div>
    </div>

    <div class="p-6 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-tight mb-5">{{ $forum->judul }}</h1>
        <div class="prose prose-sm sm:prose-base max-w-none text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $forum->deskripsi }}</div>
    </div>
</div>

<div class="space-y-6">
    <h4 class="font-bold text-slate-800 text-lg flex items-center gap-2">
        <i class="bi bi-chat-left-text text-secondary"></i> Balasan Diskusi <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md text-sm">{{ $forum->replies->count() }}</span>
    </h4>

    @if($forum->status === 'open')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 flex gap-4">
        <div class="w-10 h-10 shrink-0 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-sm uppercase">
            {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
        </div>
        <form action="{{ route('guru.forums.reply', Crypt::encryptString($forum->id)) }}" method="POST" class="flex-1">
            @csrf
            <textarea name="konten" rows="3" placeholder="Tulis instruksi tambahan atau balasan Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar mb-3" required></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-secondary hover:bg-blue-600 transition-colors shadow-sm"><i class="bi bi-send-fill mr-2"></i> Kirim Balasan</button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-center">
        <div class="w-12 h-12 bg-slate-200 text-slate-500 rounded-full flex items-center justify-center text-xl mx-auto mb-3">
            <i class="bi bi-lock-fill"></i>
        </div>
        <h6 class="font-bold text-slate-700 text-sm mb-1">Diskusi Terkunci</h6>
        <p class="text-xs text-slate-500 font-medium m-0">Forum ini telah ditutup. Tidak ada yang dapat mengirimkan balasan baru.</p>
    </div>
    @endif

    <div class="space-y-4 pb-10">
        @forelse($forum->replies as $reply)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-4">
            <div class="flex gap-4">
                <div class="w-10 h-10 shrink-0 rounded-full {{ $reply->user->role === 'guru' ? 'bg-secondary' : 'bg-emerald-500' }} text-white flex items-center justify-center font-bold text-sm uppercase">
                    {{ substr($reply->user->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                        <h6 class="font-bold text-slate-800 text-sm m-0">{{ $reply->user->nama_lengkap }}</h6>
                        @if($reply->user->role === 'guru')
                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 font-bold text-[0.6rem] uppercase tracking-widest border border-blue-100"><i class="bi bi-patch-check-fill mr-1"></i>Pengajar</span>
                        @else
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[0.6rem] uppercase tracking-widest border border-emerald-100">Siswa</span>
                        @endif
                        <span class="text-[0.65rem] text-slate-400 font-medium ml-auto shrink-0"><i class="bi bi-clock mr-1"></i>{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed whitespace-pre-wrap text-[0.85rem]">{{ $reply->konten }}</div>

                    @if($forum->status === 'open')
                    <button onclick="toggleReplyForm({{ $reply->id }})" class="text-xs font-bold text-secondary hover:text-blue-700 mt-2 flex items-center gap-1 bg-transparent border-none p-0 cursor-pointer">
                        <i class="bi bi-reply-fill"></i> Balas Komentar
                    </button>
                    @endif
                </div>
            </div>

            @if($reply->childReplies->count() > 0)
            <div class="ml-14 mt-2 space-y-3 border-l-2 border-slate-100 pl-4 py-1">
                @foreach($reply->childReplies as $child)
                <div class="flex gap-3">
                    <div class="w-8 h-8 shrink-0 rounded-full {{ $child->user->role === 'guru' ? 'bg-secondary' : 'bg-emerald-500' }} text-white flex items-center justify-center font-bold text-[0.65rem] uppercase">
                        {{ substr($child->user->nama_lengkap, 0, 1) }}
                    </div>
                    <div class="flex-1 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2 mb-1">
                            <h6 class="font-bold text-slate-700 text-[0.8rem] m-0">{{ $child->user->nama_lengkap }}</h6>
                            @if($child->user->role === 'guru')
                            <i class="bi bi-patch-check-fill text-blue-500 text-[0.7rem]" title="Pengajar"></i>
                            @endif
                            <span class="text-[0.6rem] text-slate-400 font-medium ml-auto">{{ $child->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-slate-600 text-[0.8rem] leading-relaxed whitespace-pre-wrap">{{ $child->konten }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div id="replyForm_{{ $reply->id }}" class="hidden ml-14 mt-2">
                <form action="{{ route('guru.forums.reply', Crypt::encryptString($forum->id)) }}" method="POST" class="flex flex-col gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                    <textarea name="konten" rows="2" placeholder="Tulis balasan untuk {{ $reply->user->nama_lengkap }}..." class="w-full px-3 py-2 rounded-lg border border-slate-200 text-[0.8rem] focus:ring-2 focus:ring-secondary/20 focus:border-secondary custom-scrollbar" required></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleReplyForm({{ $reply->id }})" class="px-3 py-1.5 rounded-lg font-bold text-xs text-slate-500 hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit" class="px-3 py-1.5 rounded-lg font-bold text-xs text-white bg-secondary hover:bg-blue-600 transition-colors">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl bg-white/50">
            <p class="text-sm text-slate-400 font-medium m-0">Belum ada percakapan. Jadilah yang pertama memulai diskusi!</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    function toggleReplyForm(replyId) {
        const form = document.getElementById(`replyForm_${replyId}`);
        if (form.classList.contains('hidden')) {
            document.querySelectorAll('[id^="replyForm_"]').forEach(f => f.classList.add('hidden'));
            form.classList.remove('hidden');
            form.querySelector('textarea').focus();
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection