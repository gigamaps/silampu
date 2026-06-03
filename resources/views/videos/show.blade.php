@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.public')

@section('page_title', 'Media Player')

@section('content')
<div class="{{ auth()->check() ? '' : 'container mx-auto px-4 md:px-6 py-8' }}">

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-secondary transition-colors text-decoration-none">
            <i class="bi bi-arrow-left-short text-xl mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-black rounded-2xl overflow-hidden shadow-lg border border-slate-200 aspect-video relative"
                id="player-container"
                style="background-image: url('https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg'); background-size: cover; background-position: center;"
                data-video-id="{{ $video->youtube_id }}"
                data-start-time="{{ $lastPosition ?? 0 }}"
                data-track-url="{{ route('videos.track', $video->slug ?? $video->id) }}">

                <div id="youtube-player" class="absolute top-0 left-0 w-full h-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($video->tipe_video == 'pembelajaran')
                    <span class="px-3 py-1 rounded bg-blue-50 text-blue-600 font-extrabold text-[0.7rem] border border-blue-100 uppercase tracking-widest"><i class="bi bi-book-half mr-1"></i>Materi Kelas</span>
                    @else
                    <span class="px-3 py-1 rounded bg-purple-50 text-purple-600 font-extrabold text-[0.7rem] border border-purple-100 uppercase tracking-widest"><i class="bi bi-mic-fill mr-1"></i>Podcast Umum</span>
                    @endif
                    <span class="px-3 py-1 rounded bg-slate-100 text-slate-600 font-bold text-[0.7rem] border border-slate-200 uppercase tracking-widest">{{ $video->unit->nama_unit ?? 'Global' }}</span>
                </div>

                <h1 class="text-2xl font-extrabold text-slate-800 leading-snug mb-4">{{ $video->judul }}</h1>

                <div class="flex items-center justify-between py-4 border-y border-slate-100 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-lg uppercase">
                            {{ substr($video->uploader->nama_lengkap ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm m-0">{{ $video->uploader->nama_lengkap ?? 'Akun Terhapus' }}</p>
                            <p class="text-[0.7rem] font-medium text-slate-500 m-0 mt-0.5">Dipublikasikan pada {{ $video->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-extrabold text-slate-800"><i class="bi bi-eye text-slate-400 mr-1"></i> {{ number_format($video->views) }}</div>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest m-0">Total Tayangan</p>
                    </div>
                </div>

                <div class="prose prose-sm max-w-none text-slate-600 font-medium">
                    {!! nl2br(e($video->deskripsi ?? 'Tidak ada deskripsi yang dilampirkan untuk video ini.')) !!}
                </div>

                @if($video->tipe_video == 'pembelajaran' && !empty($video->file_modul))
                @auth
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-blue-900 text-sm"><i class="bi bi-file-earmark-pdf-fill text-red-500 mr-1"></i> Modul Pembelajaran</h4>
                        <p class="text-xs text-blue-700 mt-1">Unduh modul/materi pendamping untuk video ini.</p>
                    </div>
                    <a href="{{ asset('storage/' . $video->file_modul) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">
                        <i class="bi bi-download mr-1"></i> Unduh / Lihat
                    </a>
                </div>
                @else
                <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <p class="text-sm text-slate-600 m-0"><i class="bi bi-lock-fill text-slate-400 mr-1"></i> Silakan <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Login</a> untuk dapat mengunduh modul materi ini.</p>
                </div>
                @endauth
                @endif
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4">{{ $comments->count() }} Komentar</h3>

                @auth
                <form action="{{ route('comments.store', $video->id) }}" method="POST" class="mb-8 flex gap-3">
                    @csrf
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-slate-600 uppercase">
                        {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <textarea name="isi_komentar" rows="2" class="w-full rounded-xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Tulis komentar Anda..."></textarea>
                        <div class="text-right mt-2">
                            <button type="submit" class="px-4 py-2 bg-secondary text-white font-bold rounded-lg text-sm hover:opacity-90">Kirim</button>
                        </div>
                    </div>
                </form>
                @else
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-center mb-8">
                    <p class="text-sm text-slate-600">Anda harus <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Login</a> untuk dapat berkomentar.</p>
                </div>
                @endauth

                <div class="space-y-6">
                    @forelse($comments as $comment)
                    <div>
                        <div class="flex gap-3 {{ $comment->status == 'private' ? 'opacity-50' : '' }}">
                            <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-slate-600 uppercase">
                                {{ substr($comment->user->nama_lengkap ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="flex justify-between items-start mb-1">
                                        <div>
                                            <span class="font-bold text-sm text-slate-800">{{ $comment->user->nama_lengkap ?? 'Anonim' }}</span>
                                            @if($comment->user_id == $video->uploader_id)
                                            <span class="ml-2 text-[0.6rem] bg-secondary text-white px-2 py-0.5 rounded-full font-bold">Uploader</span>
                                            @endif
                                            <span class="text-xs text-slate-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        @auth
                                        <div class="flex gap-2 text-xs">
                                            <button type="button" onclick="toggleReply('{{ $comment->id }}')" class="text-slate-500 hover:text-blue-600">Balas</button>

                                            @if(auth()->id() == $comment->user_id || auth()->id() == $video->uploader_id)
                                            @if(auth()->id() == $comment->user_id)
                                            <button type="button" onclick="toggleEdit('{{ $comment->id }}')" class="text-blue-500 hover:text-blue-700">Edit</button>
                                            @endif

                                            @if(auth()->id() == $video->uploader_id && auth()->id() != $comment->user_id)
                                            <form action="{{ route('comments.toggle_hide', $comment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-orange-500 hover:text-orange-700">
                                                    {{ $comment->status == 'private' ? 'Tampilkan' : 'Sembunyikan' }}
                                                </button>
                                            </form>
                                            @endif

                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus komentar ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                            </form>
                                            @endif
                                        </div>
                                        @endauth
                                    </div>

                                    <p id="text-comment-{{ $comment->id }}" class="text-sm text-slate-700 mt-1">{{ $comment->isi_komentar }}</p>

                                    @auth
                                    <form id="form-edit-{{ $comment->id }}" action="{{ route('comments.update', $comment->id) }}" method="POST" class="hidden mt-2">
                                        @csrf @method('PUT')
                                        <textarea name="isi_komentar" rows="2" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 text-sm mb-2">{{ $comment->isi_komentar }}</textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="toggleEdit('{{ $comment->id }}')" class="px-3 py-1 bg-slate-200 text-slate-600 font-bold rounded text-xs hover:bg-slate-300">Batal</button>
                                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white font-bold rounded text-xs hover:bg-blue-700">Update</button>
                                        </div>
                                    </form>
                                    @endauth

                                    @if($comment->status == 'private')
                                    <div class="mt-2 text-xs text-red-500 font-bold italic"><i class="bi bi-eye-slash"></i> Komentar ini disembunyikan oleh uploader.</div>
                                    @endif
                                </div>

                                @auth
                                <form id="form-reply-{{ $comment->id }}" action="{{ route('comments.store', $video->id) }}" method="POST" class="hidden mt-3 flex gap-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <textarea name="isi_komentar" rows="1" class="flex-1 rounded-lg border-slate-300 focus:ring-blue-500 text-sm" placeholder="Balas ke {{ $comment->user->nama_lengkap ?? 'Anonim' }}..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-bold rounded-lg text-sm hover:bg-slate-900">Kirim</button>
                                </form>
                                @endauth
                            </div>
                        </div>

                        @if($comment->replies->count() > 0)
                        <div class="ml-12 mt-4 space-y-4">
                            @foreach($comment->replies as $reply)
                            <div class="flex gap-3 {{ $reply->status == 'private' ? 'opacity-50' : '' }}">
                                <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 flex-shrink-0 flex items-center justify-center font-bold text-slate-600 uppercase text-xs">
                                    {{ substr($reply->user->nama_lengkap ?? 'U', 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                                        <div class="flex justify-between items-start mb-1">
                                            <div>
                                                <span class="font-bold text-sm text-slate-800">{{ $reply->user->nama_lengkap ?? 'Anonim' }}</span>
                                                @if($reply->user_id == $video->uploader_id)
                                                <span class="ml-2 text-[0.6rem] bg-secondary text-white px-2 py-0.5 rounded-full font-bold">Uploader</span>
                                                @endif
                                                <span class="text-xs text-slate-400 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            @auth
                                            @if(auth()->id() == $reply->user_id || auth()->id() == $video->uploader_id)
                                            <div class="flex gap-2 text-xs">
                                                @if(auth()->id() == $reply->user_id)
                                                <button type="button" onclick="toggleEdit('{{ $reply->id }}')" class="text-blue-500 hover:text-blue-700">Edit</button>
                                                @endif

                                                @if(auth()->id() == $video->uploader_id && auth()->id() != $reply->user_id)
                                                <form action="{{ route('comments.toggle_hide', $reply->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-orange-500 hover:text-orange-700">
                                                        {{ $reply->status == 'private' ? 'Tampilkan' : 'Sembunyikan' }}
                                                    </button>
                                                </form>
                                                @endif

                                                <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus balasan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                                </form>
                                            </div>
                                            @endif
                                            @endauth
                                        </div>

                                        <p id="text-comment-{{ $reply->id }}" class="text-sm text-slate-700 mt-1">{{ $reply->isi_komentar }}</p>

                                        @auth
                                        <form id="form-edit-{{ $reply->id }}" action="{{ route('comments.update', $reply->id) }}" method="POST" class="hidden mt-2">
                                            @csrf @method('PUT')
                                            <textarea name="isi_komentar" rows="2" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 text-sm mb-2">{{ $reply->isi_komentar }}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="toggleEdit('{{ $reply->id }}')" class="px-3 py-1 bg-slate-200 text-slate-600 font-bold rounded text-xs hover:bg-slate-300">Batal</button>
                                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white font-bold rounded text-xs hover:bg-blue-700">Update</button>
                                            </div>
                                        </form>
                                        @endauth

                                        @if($reply->status == 'private')
                                        <div class="mt-2 text-xs text-red-500 font-bold italic"><i class="bi bi-eye-slash"></i> Balasan ini disembunyikan oleh uploader.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-center text-sm text-slate-500 py-4">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 text-base mb-5 border-b border-slate-100 pb-3"><i class="bi bi-info-square-fill text-blue-500 mr-2"></i>Metadata Konten</h3>
                <ul class="space-y-4 m-0 p-0 list-none">
                    <li>
                        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest m-0 mb-1">Mata Pelajaran</p>
                        <p class="font-bold text-slate-700 m-0">{{ $video->subject->nama_mapel ?? 'Semua Mapel (Umum)' }}</p>
                    </li>
                    <li>
                        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest m-0 mb-1">Durasi Video</p>
                        <p class="font-bold text-slate-700 m-0"><i class="bi bi-clock-history mr-1"></i> {{ $video->durasi ?? 'Tidak diketahui' }}</p>
                    </li>
                </ul>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 text-base mb-5 border-b border-slate-100 pb-3"><i class="bi bi-collection-play-fill text-red-500 mr-2"></i>Video Disarankan</h3>
                <div class="space-y-4">
                    @forelse($suggestedVideos as $sVideo)
                    <a href="{{ route('videos.show', $sVideo->slug ?? $sVideo->id) }}" class="flex gap-3 group text-decoration-none">
                        <div class="w-24 h-16 bg-slate-200 rounded-lg overflow-hidden flex-shrink-0 relative">
                            <img src="https://img.youtube.com/vi/{{ $sVideo->youtube_id }}/mqdefault.jpg" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" alt="Thumbnail">
                            <div class="absolute bottom-1 right-1 bg-black/80 text-white text-[0.6rem] px-1 rounded font-bold">{{ $sVideo->durasi ?? '00:00' }}</div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 line-clamp-2 leading-tight">{{ $sVideo->judul }}</h4>
                            <p class="text-[0.65rem] text-slate-500 mt-1">{{ number_format($sVideo->views) }}x ditonton</p>
                        </div>
                    </a>
                    @empty
                    <p class="text-xs text-slate-400">Tidak ada saran video.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit(commentId) {
            var textElement = document.getElementById('text-comment-' + commentId);
            var formElement = document.getElementById('form-edit-' + commentId);

            if (formElement.classList.contains('hidden')) {
                formElement.classList.remove('hidden');
                textElement.classList.add('hidden');
            } else {
                formElement.classList.add('hidden');
                textElement.classList.remove('hidden');
            }
        }

        function toggleReply(commentId) {
            var formElement = document.getElementById('form-reply-' + commentId);
            formElement.classList.toggle('hidden');
        }

        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        var progressInterval;

        function onYouTubeIframeAPIReady() {
            var playerContainer = document.getElementById('player-container');
            var videoId = playerContainer.getAttribute('data-video-id');

            player = new YT.Player('youtube-player', {
                videoId: videoId,
                playerVars: {
                    'playsinline': 1,
                    'rel': 0,
                    'modestbranding': 1
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            var playerContainer = document.getElementById('player-container');
            var startTime = parseInt(playerContainer.getAttribute('data-start-time')) || 0;

            if (startTime > 0) {
                event.target.seekTo(startTime, true);
            }
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                progressInterval = setInterval(sendProgress, 5000);
            } else {
                clearInterval(progressInterval);
            }

            if (event.data == YT.PlayerState.ENDED) {
                sendProgress(true);
            }
        }

        function sendProgress(isFinished = false) {
            const currentTime = Math.floor(player.getCurrentTime());
            var playerContainer = document.getElementById('player-container');
            var trackUrl = playerContainer.getAttribute('data-track-url');

            fetch(trackUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    last_position: currentTime,
                    is_finished: isFinished
                })
            }).catch(err => console.error("Tracking Error:", err));
        }
    </script>
    @endsection