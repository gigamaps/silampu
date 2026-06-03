<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage; // Wajib import Storage

class GuruVideoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil daftar Mapel yang diajar guru ini (untuk form modal upload)
        $mySubjects = Subject::whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->with('studentClass.unit')->get();

        // Query khusus video milik guru ini saja
        $query = Video::with(['subject.studentClass', 'unit'])
            ->where('uploader_id', $user->id)
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($inner) use ($search) {
                        $inner->where('nama_mapel', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->per_page ?? 15;
        $videos = $query->paginate($perPage)->withQueryString();

        return view('guru.videos.index', compact('videos', 'mySubjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'subject_id' => 'required|exists:subjects,id',
            'status' => 'required|in:public,private',
            'durasi' => 'nullable|string|max:10',
            // Validasi File Modul (Opsional, PDF/DOC/DOCX, Maksimal 5MB)
            'file_modul' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $subject = Subject::with('studentClass')->findOrFail($request->subject_id);

        if (!$subject->users->contains(auth()->id())) {
            abort(403, 'Anda tidak berhak menambahkan video ke mata pelajaran ini.');
        }

        // Ekstrak ID YouTube
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $request->youtube_url, $match);
        $youtube_id = $match[1] ?? null;

        if (!$youtube_id) {
            return back()->withErrors(['youtube_url' => 'Link YouTube tidak valid.'])->withInput();
        }

        if (Video::where('youtube_id', $youtube_id)->exists()) {
            return back()->withErrors(['youtube_url' => 'Video pembelajaran ini sudah pernah diunggah.'])->withInput();
        }

        // Proses Upload File Modul jika ada
        $filePath = null;
        if ($request->hasFile('file_modul')) {
            // Simpan di folder storage/app/public/moduls
            $filePath = $request->file('file_modul')->store('moduls', 'public');
        }

        $video = Video::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . time(),
            'tipe_video' => 'pembelajaran',
            'youtube_id' => $youtube_id,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi ?? '--:--',
            'file_modul' => $filePath, // Menyimpan lokasi path file
            'target_tingkat' => $subject->studentClass->tingkat_kelas ?? 'umum',
            'unit_id' => $subject->studentClass->unit_id,
            'subject_id' => $subject->id,
            'uploader_id' => auth()->id(),
            'status' => $request->status,
        ]);

        ActivityLogger::log('upload_materi', 'Mengunggah video materi: ' . $video->judul);
        return back()->with('success', 'Video materi dan modul berhasil dipublikasikan!');
    }

    public function edit(Video $video)
    {
        if ($video->uploader_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $video->youtube_url = 'https://www.youtube.com/watch?v=' . $video->youtube_id;
        return response()->json(['video' => $video]);
    }

    public function update(Request $request, Video $video)
    {
        if ($video->uploader_id !== auth()->id()) {
            abort(403, 'Bukan milik Anda.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'subject_id' => 'required|exists:subjects,id',
            'status' => 'required|in:public,private',
            'durasi' => 'nullable|string|max:10',
            'file_modul' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $subject = Subject::with('studentClass')->findOrFail($request->subject_id);

        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $request->youtube_url, $match);
        $youtube_id = $match[1] ?? null;

        if (!$youtube_id) {
            return back()->withErrors(['youtube_url' => 'Link YouTube tidak valid.'])->withInput();
        }

        if (Video::where('youtube_id', $youtube_id)->where('id', '!=', $video->id)->exists()) {
            return back()->withErrors(['youtube_url' => 'Tautan YouTube ini sudah digunakan oleh video lain.'])->withInput();
        }

        // Proses Update File Modul jika mengunggah file baru
        $filePath = $video->file_modul; // Gunakan file lama sebagai default

        if ($request->hasFile('file_modul')) {
            // Hapus file lama jika ada
            if ($video->file_modul && Storage::disk('public')->exists($video->file_modul)) {
                Storage::disk('public')->delete($video->file_modul);
            }

            // Simpan file baru
            $filePath = $request->file('file_modul')->store('moduls', 'public');
        }

        $video->update([
            'judul' => $request->judul,
            'youtube_id' => $youtube_id,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi ?? '--:--',
            'file_modul' => $filePath, // Perbarui letak file
            'target_tingkat' => $subject->studentClass->tingkat_kelas ?? 'umum',
            'unit_id' => $subject->studentClass->unit_id,
            'subject_id' => $subject->id,
            'status' => $request->status,
        ]);

        ActivityLogger::log('update_materi', 'Mengedit video materi: ' . $video->judul);
        return back()->with('success', 'Perubahan video berhasil disimpan!');
    }

    public function destroy(Video $video)
    {
        if ($video->uploader_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        // Hapus file fisik modul dari storage (Penting agar server tidak penuh)
        if ($video->file_modul && Storage::disk('public')->exists($video->file_modul)) {
            Storage::disk('public')->delete($video->file_modul);
        }

        $judul = $video->judul;
        $video->delete();

        ActivityLogger::log('delete_materi', 'Menghapus video materi: ' . $judul);
        return back()->with('success', 'Video dan file modul terkait berhasil dihapus!');
    }

    public function statsPage(Video $video)
    {
        if ($video->uploader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan mengakses statistik video ini.');
        }

        $subject = $video->subject;
        $class = $subject->studentClass;

        $students = \App\Models\User::where('role', 'siswa')
            ->where('class_id', $class->id)
            ->get();

        $progressData = \App\Models\WatchProgress::where('video_id', $video->id)->get()->keyBy('user_id');

        $stats = $students->map(function ($student) use ($progressData) {
            $progress = $progressData->get($student->id);
            return [
                'nama' => $student->nama_lengkap,
                'nis' => $student->nis_np,
                'status' => $progress ? ($progress->is_finished ? 'Tuntas' : 'Progres') : 'Belum Menonton',
                'last_seen' => $progress ? $progress->updated_at->diffForHumans() : '-',
                'raw_finished' => $progress ? $progress->is_finished : false
            ];
        });

        return view('guru.videos.stats', compact('video', 'stats', 'class'));
    }
}
