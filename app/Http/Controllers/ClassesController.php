<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClassesExport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log('view_classes', 'Melihat daftar ruang kelas');

        $query = Classes::with(['unit', 'major'])->latest();
        $isKurikulum = auth()->user()->role === 'kurikulum';

        // --- SCOPING MULTI-TENANT UNTUK KURIKULUM ---
        if ($isKurikulum) {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->where('unit_id', $myUnitId);
            $units = Unit::with('majors')->where('id', $myUnitId)->get();
        } else {
            $units = Unit::with('majors')->orderBy('nama_unit')->get();
        }

        // Fitur Pencarian Pintar
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('tingkat_kelas', 'like', "%{$search}%")
                    ->orWhereHas('unit', function ($inner) use ($search) {
                        $inner->where('nama_unit', 'like', "%{$search}%");
                    })
                    ->orWhereHas('major', function ($inner) use ($search) {
                        $inner->where('nama_jurusan', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->per_page ?? 10;
        $classes = $query->paginate($perPage)->withQueryString();

        return view('admin.classes.index', compact('classes', 'units'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'jurusan_id' => 'nullable|exists:majors,id',
            'tingkat_kelas' => 'required|integer|between:1,12',
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classes')->where('unit_id', $request->unit_id)
            ],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar di unit sekolah tersebut.'
        ]);

        $class = Classes::create([
            'unit_id' => $request->unit_id,
            'jurusan_id' => $request->jurusan_id,
            'tingkat_kelas' => $request->tingkat_kelas,
            'nama_kelas' => $request->nama_kelas,
        ]);

        ActivityLogger::log('create_class', 'Menambahkan ruang kelas baru: ' . $class->nama_kelas);
        return back()->with('success', 'Data kelas berhasil ditambahkan!');
    }

    public function edit(Classes $class)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($class->unit_id != $myUnitId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        $class->load('unit.majors');
        return response()->json(['class' => $class]);
    }

    public function update(Request $request, Classes $class)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($class->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'jurusan_id' => 'nullable|exists:majors,id',
            'tingkat_kelas' => 'required|integer|between:1,12',
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classes')->where('unit_id', $request->unit_id)->ignore($class->id)
            ],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah digunakan di unit sekolah tersebut.'
        ]);

        $class->update([
            'unit_id' => $request->unit_id,
            'jurusan_id' => $request->jurusan_id,
            'tingkat_kelas' => $request->tingkat_kelas,
            'nama_kelas' => $request->nama_kelas,
        ]);

        ActivityLogger::log('update_class', 'Mengubah data ruang kelas: ' . $class->nama_kelas);
        return back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroy(Classes $class)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($class->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $nama = $class->nama_kelas;
        $class->delete();

        ActivityLogger::log('delete_class', 'Menghapus ruang kelas: ' . $nama);
        return back()->with('success', 'Data kelas berhasil dihapus!');
    }

    public function export()
    {
        ActivityLogger::log('export_classes', 'Mengunduh data ruang kelas ke Excel');
        return Excel::download(new ClassesExport, 'Data_Kelas_SILAMPU.xlsx');
    }

    public function show($encrypted_id)
    {
        try {
            $id = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan kelas tidak valid.');
        }

        $class = Classes::with('unit', 'major')->findOrFail($id);

        // Keamanan Multi-Tenant untuk Kurikulum
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($class->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $students = \App\Models\User::where('class_id', $class->id)
            ->where('role', 'siswa')
            ->get();

        $availableClasses = Classes::where('unit_id', $class->unit_id)
            ->where('id', '!=', $class->id)
            ->orderBy('tingkat_kelas')
            ->get();

        return view('admin.classes.show', compact('class', 'students', 'availableClasses'));
    }

    public function promote(Request $request, $encrypted_id)
    {
        // 1. Dekripsi ID Kelas asal
        try {
            $classId = Crypt::decryptString($encrypted_id);
        } catch (DecryptException $e) {
            abort(404, 'Tautan tidak valid.');
        }

        // 2. Validasi input
        $request->validate([
            'student_ids' => 'required|array',
            'target_class_id' => 'required|exists:classes,id'
        ]);

        // 3. Update class_id untuk siswa-siswa yang dipilih
        \App\Models\User::whereIn('id', $request->student_ids)
            ->update(['class_id' => $request->target_class_id]);

        $class = Classes::findOrFail($classId);
        ActivityLogger::log('promote_students', 'Memindahkan ' . count($request->student_ids) . ' siswa dari kelas ' . $class->nama_kelas);

        return back()->with('success', 'Siswa berhasil dipindahkan ke kelas tujuan.');
    }
}
