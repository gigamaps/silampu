<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Http\Request;

class VideoCommentController extends Controller
{
    // Menyimpan komentar baru
    public function store(Request $request, Video $video)
    {
        $request->validate([
            'isi_komentar' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:video_comments,id' // Validasi tambahan untuk reply
        ]);

        VideoComment::create([
            'video_id' => $video->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id, // Simpan jika ada
            'isi_komentar' => $request->isi_komentar,
            'status' => 'public'
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function update(Request $request, VideoComment $comment)
    {
        $request->validate(['isi_komentar' => 'required|string|max:1000']);

        if (auth()->id() === $comment->user_id) {
            $comment->update(['isi_komentar' => $request->isi_komentar]);
            return back()->with('success', 'Komentar berhasil diperbarui.');
        }

        abort(403, 'Anda tidak memiliki akses untuk mengedit komentar ini.');
    }

    // Menghapus komentar
    public function destroy(VideoComment $comment)
    {
        // Pastikan hanya pemilik komentar atau uploader video yang bisa menghapus
        $video = $comment->video;
        if (auth()->id() === $comment->user_id || auth()->id() === $video->uploader_id) {
            $comment->delete();
            return back()->with('success', 'Komentar berhasil dihapus.');
        }

        abort(403, 'Anda tidak memiliki akses untuk menghapus komentar ini.');
    }

    // Mengubah status komentar (Sembunyikan/Tampilkan)
    public function toggleHide(VideoComment $comment)
    {
        $video = $comment->video;

        // Pastikan hanya uploader video yang bisa menyembunyikan komentar
        if (auth()->id() === $video->uploader_id) {
            $comment->update([
                'status' => $comment->status === 'public' ? 'private' : 'public'
            ]);

            return back()->with('success', 'Status komentar berhasil diubah.');
        }

        abort(403, 'Hanya pengunggah video yang dapat menyembunyikan komentar.');
    }
}
