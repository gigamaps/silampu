<?php

namespace App\Http\Controllers;

use App\Models\WatchProgress;
use Illuminate\Http\Request;

class SiswaHistoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        // Ambil riwayat tontonan user yang sedang login
        $query = WatchProgress::with(['video.subject', 'video.uploader'])
            ->where('user_id', auth()->id())
            ->whereHas('video'); // Pastikan videonya belum dihapus oleh guru

        // Pencarian berdasarkan judul video
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('video', function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");
            });
        }

        $histories = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('siswa.history.index', compact('histories'));
    }
}
