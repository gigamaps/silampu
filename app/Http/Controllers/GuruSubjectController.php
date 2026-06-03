<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GuruSubjectController extends Controller
{
    // 1. Halaman Menampilkan Daftar Mata Pelajaran yang Diampu Guru
    public function index()
    {
        $user = auth()->user();

        $subjects = Subject::whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->with('studentClass.unit')->get();

        // Enkripsi ID masing-masing subject untuk diamankan di URL
        $subjects->transform(function ($subject) {
            // Kita membuat property buatan (on-the-fly) khusus untuk view
            $subject->encrypted_id = Crypt::encryptString($subject->id);
            return $subject;
        });

        return view('guru.subjects.index', compact('subjects'));
    }

    // 2. Halaman Menampilkan Daftar Siswa pada Kelas dari Mapel Tersebut
    public function students($encrypted_id)
    {
        try {
            // Dekripsi ID yang ada di URL
            $subjectId = Crypt::decryptString($encrypted_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Tautan kelas tidak valid atau sudah kadaluarsa.');
        }

        $user = auth()->user();
        $subject = Subject::with('studentClass.unit', 'users')->findOrFail($subjectId);

        // Keamanan: Cek apakah guru ini benar-benar mengajar mapel ini
        if (!$subject->users->contains($user->id)) {
            abort(403, 'Akses Ditolak: Anda tidak ditugaskan untuk mengampu mata pelajaran ini.');
        }

        $class = $subject->studentClass;

        $students = User::where('role', 'siswa')
            ->where('class_id', $class->id)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('guru.subjects.students', compact('subject', 'class', 'students'));
    }
}
