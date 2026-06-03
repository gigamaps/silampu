<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show(Video $video)
    {
        $user = auth()->user();
        $lastPosition = 0; // Default mulai dari detik 0

        if ($user) {
            // 1. Tambah jumlah Views (Hanya jika yang menonton bukan uploader)
            if ($user->id !== $video->uploader_id) {
                $video->increment('views');
            }

            // 2. Ambil progres terakhir untuk fitur Resume
            if ($user->role === 'siswa' || $user->role === 'user') {
                $progress = \App\Models\WatchProgress::where('user_id', $user->id)
                    ->where('video_id', $video->id)
                    ->first();

                // Jika ada riwayat dan belum selesai, mulai dari menit terakhir
                if ($progress && !$progress->is_finished) {
                    $lastPosition = $progress->last_position;
                }
            }
        }

        // 3. Ambil Komentar Induk & Balasannya
        $comments = $video->comments()->with(['user', 'replies' => function ($q) use ($user, $video) {
            // Tarik data balasan beserta usernya, urutkan dari yang paling lama (oldest)
            $q->with('user')->oldest();

            // Uploader bisa melihat balasan yang 'private', user lain hanya melihat 'public'
            if (!$user || $user->id !== $video->uploader_id) {
                $q->where('status', 'public');
            }
        }])
            ->whereNull('parent_id') // PENTING: Hanya ambil komentar utama (yang tidak punya parent)
            ->when(!$user || $user->id !== $video->uploader_id, function ($query) {
                // Uploader bisa melihat komentar induk yang 'private', user lain hanya melihat 'public'
                return $query->where('status', 'public');
            })
            ->latest() // Urutkan komentar induk dari yang paling baru
            ->get();

        // 4. Saran Video (Video lain yang berstatus public)
        $suggestedVideos = Video::where('id', '!=', $video->id)
            ->where('status', 'public')
            ->inRandomOrder()
            ->take(4) // Ambil 4 video
            ->get();

        return view('videos.show', compact('video', 'lastPosition', 'comments', 'suggestedVideos'));
    }

    public function trackProgress(Request $request, Video $video)
    {
        $user = auth()->user();

        if ($user && ($user->role === 'siswa' || $user->role === 'user')) {
            $isFinished = filter_var($request->is_finished, FILTER_VALIDATE_BOOLEAN);

            \App\Models\WatchProgress::updateOrCreate(
                ['user_id' => $user->id, 'video_id' => $video->id],
                [
                    'last_position' => $request->last_position ?? 0,
                    'is_finished' => \DB::raw("IF(is_finished = 1, 1, " . ($isFinished ? 1 : 0) . ")")
                ]
            );
        }

        return response()->json(['status' => 'success']);
    }
}
