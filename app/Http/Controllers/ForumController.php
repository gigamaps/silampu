<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Helpers\ActivityLogger;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil semua kelas yang diajar oleh guru ini
        // Logika: Guru -> (pivot subject_user) -> Subject -> Class
        $myClasses = Classes::whereHas('subjects', function ($query) use ($user) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })->with('unit')->distinct()->get();

        // 2. Ambil data forum milik guru ini
        $query = Forum::with('targetClass.unit')->where('guru_id', $user->id)->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('judul', 'like', "%{$search}%");
        }

        $perPage = $request->per_page ?? 10;
        $forums = $query->paginate($perPage)->withQueryString();

        // 3. Enkripsi ID untuk setiap forum secara on-the-fly
        $forums->getCollection()->transform(function ($forum) {
            $forum->encrypted_id = Crypt::encryptString($forum->id);
            return $forum;
        });

        return view('guru.forums.index', compact('forums', 'myClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:open,closed'
        ]);

        $forum = Forum::create([
            'guru_id' => auth()->id(),
            'class_id' => $request->class_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        ActivityLogger::log('create_forum', 'Membuat forum diskusi kelas: ' . $forum->judul);
        return back()->with('success', 'Forum diskusi berhasil dibuka!');
    }

    public function show($encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan forum tidak valid.');
        }

        // UBAHAN: Tambahkan 'replies.user' untuk memuat komentar beserta nama pembuatnya
        $forum = Forum::with(['targetClass.unit', 'replies.user'])->findOrFail($id);

        if ($forum->guru_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('guru.forums.show', compact('forum'));
    }

    public function edit($encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            return response()->json(['error' => 'Invalid ID'], 400);
        }

        $forum = Forum::findOrFail($id);

        if ($forum->guru_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Jangan lupa sertakan encrypted_id untuk Action Form
        $forum->encrypted_id = $encrypted_id;
        return response()->json(['forum' => $forum]);
    }

    public function update(Request $request, $encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Data tidak ditemukan.');
        }

        $forum = Forum::findOrFail($id);

        if ($forum->guru_id !== auth()->id()) {
            abort(403, 'Anda bukan pembuat forum ini.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:open,closed'
        ]);

        $forum->update([
            'judul' => $request->judul,
            'class_id' => $request->class_id,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        ActivityLogger::log('update_forum', 'Memperbarui forum diskusi: ' . $forum->judul);
        return back()->with('success', 'Perubahan forum berhasil disimpan!');
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $forum = Forum::findOrFail($id);

        if ($forum->guru_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $judul = $forum->judul;
        $forum->delete();

        ActivityLogger::log('delete_forum', 'Menutup & menghapus forum: ' . $judul);
        return back()->with('success', 'Forum diskusi berhasil dihapus!');
    }

    public function storeReply(Request $request, $encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan tidak valid.');
        }

        $forum = Forum::findOrFail($id);

        if ($forum->status === 'closed') {
            return back()->withErrors(['konten' => 'Forum diskusi ini sudah ditutup.']);
        }

        $request->validate([
            'konten' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id' // Validasi parent
        ]);

        \App\Models\ForumReply::create([
            'forum_id' => $forum->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id, // Simpan jika ada
            'konten' => $request->konten,
        ]);

        ActivityLogger::log('reply_forum', 'Mengirim balasan di forum: ' . $forum->judul);
        return back()->with('success', 'Balasan berhasil dikirim!');
    }
}
