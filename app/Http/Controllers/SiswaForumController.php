<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ForumReply;

class SiswaForumController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('studentClass');
        $class = $user->studentClass;

        if (!$class) {
            $forums = collect([]);
            return view('siswa.forums.index', compact('forums', 'class'));
        }

        // Ambil input per_page, default 10
        $perPage = $request->get('per_page', 10);

        $query = Forum::with(['teacher'])
            ->withCount('replies')
            ->where('class_id', $class->id);

        if ($request->filled('search')) {
            $query->where('judul', 'like', "%{$request->search}%");
        }

        $forums = $query->latest()->paginate($perPage)->withQueryString();

        // Statistik untuk header
        $stats = [
            'total' => Forum::where('class_id', $class->id)->count(),
            'open' => Forum::where('class_id', $class->id)->where('status', 'open')->count(),
        ];

        return view('siswa.forums.index', compact('forums', 'class', 'stats'));
    }

    public function show($encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan diskusi tidak valid.');
        }

        $user = auth()->user();

        $forum = Forum::with([
            'teacher',
            'targetClass',
            'replies' => function ($q) {
                $q->whereNull('parent_id')->oldest(); // Urutkan dari yang terlama
            },
            'replies.user',
            'replies.childReplies.user'
        ])->findOrFail($id);

        // Pastikan forum ini memang untuk kelas siswa yang login
        if ($forum->class_id !== $user->class_id) {
            abort(403, 'Akses ditolak. Diskusi ini bukan untuk kelas Anda.');
        }

        return view('siswa.forums.show', compact('forum'));
    }

    public function storeReply(Request $request, $encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan tidak valid.');
        }

        $forum = Forum::findOrFail($id);

        // Keamanan: Tolak jika forum sudah ditutup
        if ($forum->status !== 'open') {
            return back()->withErrors(['error' => 'Diskusi ini sudah ditutup.']);
        }

        $request->validate([
            'konten' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:forum_replies,id'
        ]);

        ForumReply::create([
            'forum_id' => $forum->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'konten' => $request->konten,
        ]);

        return back()->with('success', 'Tanggapan Anda berhasil dikirim!');
    }
}
