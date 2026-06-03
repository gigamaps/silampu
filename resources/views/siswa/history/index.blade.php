@extends('layouts.dashboard')

@section('page_title', 'Riwayat Tontonan')

@section('content')

<div class="bg-white border border-slate-200 shadow-sm" style="border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-bookmark-star-fill text-secondary"></i> Riwayat Belajar
        </h2>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0;">Lanjutkan materi yang belum selesai Anda pelajari.</p>
    </div>

    <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 0.75rem 1.25rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 1rem;">
        <div class="text-secondary shadow-sm border border-slate-100" style="width: 2.5rem; height: 2.5rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="bi bi-collection-play"></i>
        </div>
        <div>
            <h4 style="font-size: 1.25rem; font-weight: 900; color: #1e293b; margin: 0; line-height: 1;">{{ $histories->total() }}</h4>
            <p style="font-size: 0.65rem; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin: 0; margin-top: 0.25rem;">Total Riwayat</p>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-200 shadow-sm" style="border-radius: 1rem; padding: 1.25rem; margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('siswa.history.index') }}" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.75rem; font-weight: bold; color: #64748b; text-transform: uppercase;">Tampilkan</span>
            <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700" style="font-size: 0.875rem; border-radius: 0.5rem; padding: 0.5rem 1rem; outline: none; cursor: pointer;">
                <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12 Data</option>
                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 Data</option>
                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 Data</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 250px; max-width: 400px; position: relative;">
            <i class="bi bi-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul video..." class="bg-white border border-slate-200 text-slate-800" style="width: 100%; font-size: 0.875rem; border-radius: 0.75rem; padding: 0.6rem 1rem 0.6rem 2.5rem; outline: none;">
        </div>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    @forelse($histories as $history)
    <a href="{{ route('videos.show', $history->video->slug) }}" class="bg-white border border-slate-200 shadow-sm text-decoration-none transition-all hover:border-secondary" style="border-radius: 1rem; overflow: hidden; display: flex; flex-direction: column;">

        <div style="position: relative; padding-bottom: 56.25%; background: #f1f5f9;">
            <img src="https://img.youtube.com/vi/{{ $history->video->youtube_id }}/mqdefault.jpg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; {{ $history->is_finished ? 'opacity: 0.8; filter: grayscale(20%);' : '' }}">

            <span style="position: absolute; top: 0.5rem; left: 0.5rem; background: rgba(255,255,255,0.95); color: #334155; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">{{ $history->video->subject->nama_mapel ?? 'Umum' }}</span>
            <span style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.7); color: white; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-size: 0.65rem; font-weight: bold; font-family: monospace;">{{ $history->video->durasi ?? '--:--' }}</span>

            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(51, 65, 85, 0.5);">
                @if($history->is_finished)
                <div style="height: 100%; background: #10b981; width: 100%;"></div>
                @else
                <div style="height: 100%; background: #f59e0b; width: 50%;"></div>
                @endif
            </div>
        </div>

        <div style="padding: 1rem; display: flex; flex-direction: column; flex: 1;">
            <div style="margin-bottom: 0.5rem;">
                @if($history->is_finished)
                <span style="font-size: 0.65rem; font-weight: 800; color: #059669; text-transform: uppercase; background: #d1fae5; padding: 0.2rem 0.5rem; border-radius: 0.25rem;"><i class="bi bi-check2-all"></i> Selesai Ditonton</span>
                @else
                <span style="font-size: 0.65rem; font-weight: 800; color: #d97706; text-transform: uppercase; background: #fef3c7; padding: 0.2rem 0.5rem; border-radius: 0.25rem;"><i class="bi bi-clock-history"></i> Sedang Dipelajari</span>
                @endif
            </div>

            <h4 style="font-size: 0.875rem; font-weight: 700; color: #1e293b; margin: 0 0 0.75rem 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $history->video->judul }}</h4>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.75rem; color: #475569; font-weight: 500;">
                <i class="bi bi-person-circle text-slate-400"></i>
                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $history->video->uploader->nama_lengkap ?? 'Pengajar' }}</span>
            </div>

            <div style="margin-top: auto; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem;">
                <span style="color: #94a3b8; font-weight: 500;"><i class="bi bi-calendar-event"></i> Terakhir dilihat</span>
                <span style="font-weight: 700; color: #475569;">{{ $history->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </a>
    @empty
    <div style="grid-column: 1 / -1; background: white; border: 1px solid #e2e8f0; border-radius: 1.5rem; padding: 4rem 1.5rem; text-align: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
        <div style="width: 5rem; height: 5rem; background: #f8fafc; color: #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.25rem auto; border: 4px solid #f1f5f9;">
            <i class="bi bi-journal-x"></i>
        </div>
        <h5 style="font-size: 1.25rem; font-weight: 800; color: #334155; margin: 0 0 0.5rem 0;">Belum Ada Riwayat</h5>
        <p style="font-size: 0.875rem; color: #64748b; margin: 0 auto 1.5rem auto; max-width: 24rem; line-height: 1.5;">Anda belum menonton video apapun. Riwayat pembelajaran Anda akan otomatis tersimpan di sini.</p>

        <a href="{{ route('siswa.videos.index') }}" class="bg-secondary hover:bg-blue-600 transition-colors text-decoration-none" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; color: white; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <i class="bi bi-play-circle-fill" style="font-size: 1.1rem;"></i> Jelajahi Materi Sekarang
        </a>
    </div>
    @endforelse
</div>

@if($histories->hasPages())
<div class="bg-white border border-slate-200 shadow-sm" style="border-radius: 1rem; padding: 1rem; display: flex; justify-content: center;">
    {{ $histories->onEachSide(1)->links('partials.pagination') }}
</div>
@endif

@endsection