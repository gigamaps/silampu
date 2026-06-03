<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubjectsExport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log('view_subjects', 'Melihat daftar mata pelajaran');

        $query = Subject::with(['studentClass.unit', 'users'])->latest();
        $isKurikulum = auth()->user()->role === 'kurikulum';

        // --- SCOPING MULTI-TENANT UNTUK KURIKULUM ---
        if ($isKurikulum) {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');

            // Mapel tidak punya unit_id, tapi nyambung ke Class, jadi filter lewat relasi Class
            $query->whereHas('studentClass', function ($q) use ($myUnitId) {
                $q->where('unit_id', $myUnitId);
            });

            // Hanya ambil unit dan guru dari sekolahnya sendiri
            $units = Unit::with('classes')->where('id', $myUnitId)->get();
            $teachers = User::whereHas('units', function ($q) use ($myUnitId) {
                $q->where('units.id', $myUnitId);
            })->where('role', 'guru')->where('status', 'aktif')->orderBy('nama_lengkap')->get();
        } else {
            // Admin bebas
            $units = Unit::with('classes')->orderBy('nama_unit')->get();
            $teachers = User::where('role', 'guru')->where('status', 'aktif')->orderBy('nama_lengkap')->get();
        }

        // Fitur Pencarian Pintar
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                    ->orWhereHas('studentClass', function ($inner) use ($search) {
                        $inner->where('nama_kelas', 'like', "%{$search}%")
                            ->orWhereHas('unit', function ($unitQuery) use ($search) {
                                $unitQuery->where('nama_unit', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage = $request->per_page ?? 10;
        $subjects = $query->paginate($perPage)->withQueryString();

        return view('admin.subjects.index', compact('subjects', 'units', 'teachers'));
    }

    public function store(Request $request)
    {
        // Validasi ekstra: Pastikan Kurikulum tidak menembak Class ID milik sekolah lain
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $classRequested = DB::table('classes')->where('id', $request->class_id)->first();

            if (!$classRequested || $classRequested->unit_id != $myUnitId) {
                abort(403, 'Anda tidak berhak menambah mata pelajaran di kelas sekolah lain.');
            }
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'nama_mapel' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects')->where('class_id', $request->class_id)
            ],
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id'
        ], [
            'nama_mapel.unique' => 'Mata pelajaran ini sudah terdaftar di kelas tersebut.'
        ]);

        $subject = Subject::create([
            'class_id' => $request->class_id,
            'nama_mapel' => $request->nama_mapel,
        ]);

        if ($request->has('guru_ids')) {
            $subject->users()->attach($request->guru_ids);
        }

        ActivityLogger::log('create_subject', 'Menambahkan mata pelajaran: ' . $subject->nama_mapel);
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function edit(Subject $subject)
    {
        // Proteksi Data
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $subject->load('studentClass'); // Wajib diload untuk mengecek
            if ($subject->studentClass->unit_id != $myUnitId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $subject->load(['studentClass.unit', 'users']);
        return response()->json(['subject' => $subject]);
    }

    public function update(Request $request, Subject $subject)
    {
        // Proteksi Update Multi-tenant
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $classRequested = DB::table('classes')->where('id', $request->class_id)->first();

            if (!$classRequested || $classRequested->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'nama_mapel' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects')->where('class_id', $request->class_id)->ignore($subject->id)
            ],
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id'
        ], [
            'nama_mapel.unique' => 'Mata pelajaran ini sudah digunakan di kelas tersebut.'
        ]);

        $subject->update([
            'class_id' => $request->class_id,
            'nama_mapel' => $request->nama_mapel,
        ]);

        $subject->users()->sync($request->guru_ids ?? []);

        ActivityLogger::log('update_subject', 'Mengubah data mata pelajaran: ' . $subject->nama_mapel);
        return back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    public function destroy(Subject $subject)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $subject->load('studentClass');
            if ($subject->studentClass->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $nama = $subject->nama_mapel;
        $subject->delete();

        ActivityLogger::log('delete_subject', 'Menghapus mata pelajaran: ' . $nama);
        return back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    public function export()
    {
        ActivityLogger::log('export_subjects', 'Mengunduh data mata pelajaran ke Excel');
        return Excel::download(new SubjectsExport, 'Data_Mata_Pelajaran_SILAMPU.xlsx');
    }
}
