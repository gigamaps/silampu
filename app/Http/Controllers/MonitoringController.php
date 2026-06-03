<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MonitoringController extends Controller
{
    public function activeUsers(Request $request)
    {
        ActivityLogger::log('view_active_users', 'Memantau halaman pengguna aktif');

        $query = User::with(['studentClass.unit', 'units'])
            ->select('users.*')
            ->selectSub(function ($q) {
                $q->from('sessions')
                    ->select('user_agent')
                    ->whereColumn('user_id', 'users.id')
                    ->orderBy('last_activity', 'desc')
                    ->limit(1);
            }, 'user_agent')
            ->where('last_seen_at', '>=', Carbon::now()->subMinutes(15))
            ->orderBy('last_seen_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis_np', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $perPage = $request->per_page ?? 10;
        $activeUsers = $query->paginate($perPage)->withQueryString();

        return view('admin.monitoring.active_users', compact('activeUsers'));
    }

    public function videos(Request $request)
    {
        ActivityLogger::log('view_video_monitoring', 'Memantau halaman monitoring konten video platform');

        $query = Video::with(['uploader', 'unit', 'subject'])->latest();
        $isKurikulum = auth()->user()->role === 'kurikulum';

        // --- SCOPING MULTI-TENANT ---
        if ($isKurikulum) {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->where('unit_id', $myUnitId);
            $units = Unit::where('id', $myUnitId)->get(); // Ambil unit untuk modal upload
        } else {
            $units = Unit::orderBy('nama_unit')->get();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('uploader', function ($inner) use ($search) {
                        $inner->where('nama_lengkap', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unit', function ($inner) use ($search) {
                        $inner->where('nama_unit', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subject', function ($inner) use ($search) {
                        $inner->where('nama_mapel', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->per_page ?? 15;
        $videos = $query->paginate($perPage)->withQueryString();

        // Lempar variabel units juga untuk form modal upload
        return view('admin.monitoring.videos', compact('videos', 'units'));
    }

    // FUNGSI: Menyimpan Video Podcast Baru
    public function storePodcast(Request $request)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'unit_id' => 'required|exists:units,id',
            'status' => 'required|in:public,private',
            'durasi' => 'nullable|string|max:10'
        ]);

        // 1. Ekstraksi ID YouTube Otomatis
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $request->youtube_url, $match);
        $youtube_id = $match[1] ?? null;

        if (!$youtube_id) {
            return back()->withErrors(['youtube_url' => 'Link YouTube tidak valid atau ID tidak ditemukan.'])->withInput();
        }

        // 2. CEK DUPLIKASI ID YOUTUBE
        $isDuplicate = Video::where('youtube_id', $youtube_id)->exists();
        if ($isDuplicate) {
            return back()->withErrors(['youtube_url' => 'Video/Podcast dengan tautan YouTube ini sudah pernah diunggah ke dalam sistem.'])->withInput();
        }

        $video = Video::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . time(),
            'tipe_video' => 'podcast',
            'youtube_id' => $youtube_id,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi ?? '--:--',
            'target_tingkat' => 'umum', // Podcast ditargetkan untuk umum (semua kelas)
            'unit_id' => $request->unit_id,
            'uploader_id' => auth()->id(),
            'status' => $request->status,
        ]);

        ActivityLogger::log('upload_podcast', 'Mengunggah podcast edukasi baru: ' . $video->judul);
        return back()->with('success', 'Podcast berhasil dipublikasikan!');
    }

    public function destroyVideo(Video $video)
    {
        // Proteksi Hapus Multi-tenant
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($video->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $judul = $video->judul;
        $video->delete();

        ActivityLogger::log('delete_video', 'Menghapus konten video: ' . $judul);
        return back()->with('success', 'Video berhasil diturunkan dari sistem!');
    }

    // FUNGSI: Ambil Data Video untuk Modal Edit
    public function editVideo(Video $video)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($video->unit_id != $myUnitId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Kembalikan URL asli agar gampang di-edit
        $video->youtube_url = 'https://www.youtube.com/watch?v=' . $video->youtube_id;
        return response()->json(['video' => $video]);
    }

    // FUNGSI: Simpan Perubahan Edit Podcast/Video
    public function updateVideo(Request $request, Video $video)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($video->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'unit_id' => 'required|exists:units,id',
            'status' => 'required|in:public,private',
            'durasi' => 'nullable|string|max:10'
        ]);

        // 1. Ekstraksi ID YouTube Otomatis
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $request->youtube_url, $match);
        $youtube_id = $match[1] ?? null;

        if (!$youtube_id) {
            return back()->withErrors(['youtube_url' => 'Link YouTube tidak valid.'])->withInput();
        }

        // 2. CEK DUPLIKASI ID YOUTUBE (Kecuali ID video yang sedang diedit ini)
        $isDuplicate = Video::where('youtube_id', $youtube_id)
            ->where('id', '!=', $video->id)
            ->exists();

        if ($isDuplicate) {
            return back()->withErrors(['youtube_url' => 'Tautan YouTube ini sudah digunakan oleh video lain di dalam sistem.'])->withInput();
        }

        $video->update([
            'judul' => $request->judul,
            'youtube_id' => $youtube_id,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi ?? '--:--',
            'unit_id' => $request->unit_id,
            'status' => $request->status,
        ]);

        ActivityLogger::log('update_video', 'Memperbarui info video/podcast: ' . $video->judul);
        return back()->with('success', 'Data video berhasil diperbarui!');
    }
}
