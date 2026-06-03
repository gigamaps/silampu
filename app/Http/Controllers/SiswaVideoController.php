<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Subject;
use App\Models\WatchProgress;
use Illuminate\Http\Request;

class SiswaVideoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('studentClass');
        $class = $user->studentClass;

        if (!$class) {
            $videos = collect([]);
            $subjects = collect([]);
            $watchedVideoIds = [];
            $watchProgresses = []; // Tambahkan ini agar view tidak error
            return view('siswa.videos.index', compact('videos', 'subjects', 'class', 'watchedVideoIds', 'watchProgresses'));
        }

        $subjects = Subject::where('class_id', $class->id)->get();

        $query = Video::with(['subject.studentClass', 'uploader'])
            ->where(function ($q) use ($class) {
                $q->whereHas('subject', function ($sub) use ($class) {
                    $sub->where('class_id', $class->id);
                })->orWhere('target_tingkat', 'umum');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $videos = $query->latest()->paginate(12)->withQueryString();

        // Ambil array ID video yang SUDAH TUNTAS ditonton oleh siswa ini
        $watchedVideoIds = WatchProgress::where('user_id', $user->id)
            ->where('is_finished', true)
            ->pluck('video_id')
            ->toArray();

        // Ambil daftar history tontonan secara mentah (dalam bentuk Detik Terakhir / last_position)
        $watchProgresses = WatchProgress::where('user_id', $user->id)
            ->pluck('last_position', 'video_id')
            ->toArray();

        // HANYA ADA SATU RETURN VIEW DI SINI
        return view('siswa.videos.index', compact('class', 'subjects', 'videos', 'watchedVideoIds', 'watchProgresses'));
    }
}
