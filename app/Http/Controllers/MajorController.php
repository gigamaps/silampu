<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MajorsExport;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log('view_majors', 'Melihat daftar jurusan');

        $query = Major::with('unit')->latest();
        $isKurikulum = auth()->user()->role === 'kurikulum';
        $myUnitId = null;

        // --- SCOPING MULTI-TENANT UNTUK KURIKULUM ---
        if ($isKurikulum) {
            $myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->where('unit_id', $myUnitId);
            $units = Unit::where('id', $myUnitId)->get(); // Hanya ambil unit miliknya
        } else {
            $units = Unit::orderBy('nama_unit')->get(); // Admin bisa melihat semua unit
        }

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_jurusan', 'like', "%{$search}%")
                    ->orWhereHas('unit', function ($innerQuery) use ($search) {
                        $innerQuery->where('nama_unit', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->per_page ?? 10;
        $majors = $query->paginate($perPage)->withQueryString();

        return view('admin.majors.index', compact('majors', 'units'));
    }

    public function store(Request $request)
    {
        // Jika Kurikulum, paksa unit_id mengikuti unit_id miliknya dari tabel pivot
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'nama_jurusan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('majors')->where('unit_id', $request->unit_id)
            ],
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah terdaftar di unit sekolah tersebut.'
        ]);

        $major = Major::create([
            'unit_id' => $request->unit_id,
            'nama_jurusan' => $request->nama_jurusan,
        ]);

        ActivityLogger::log('create_major', 'Menambahkan jurusan baru: ' . $major->nama_jurusan);
        return back()->with('success', 'Jurusan baru berhasil ditambahkan!');
    }

    public function edit(Major $major)
    {
        // Proteksi ekstra di level kode
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($major->unit_id != $myUnitId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        return response()->json(['major' => $major]);
    }

    public function update(Request $request, Major $major)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($major->unit_id != $myUnitId) {
                abort(403, 'Akses ditolak.');
            }
            $request->merge(['unit_id' => $myUnitId]);
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'nama_jurusan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('majors')->where('unit_id', $request->unit_id)->ignore($major->id)
            ],
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah digunakan di unit sekolah tersebut.'
        ]);

        $major->update([
            'unit_id' => $request->unit_id,
            'nama_jurusan' => $request->nama_jurusan,
        ]);

        ActivityLogger::log('update_major', 'Mengubah data jurusan: ' . $major->nama_jurusan);
        return back()->with('success', 'Data jurusan berhasil diperbarui!');
    }

    public function destroy(Major $major)
    {
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = \DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            if ($major->unit_id != $myUnitId) {
                abort(403, 'Anda tidak memiliki izin menghapus data dari unit lain.');
            }
        }

        $nama = $major->nama_jurusan;
        $major->delete();

        ActivityLogger::log('delete_major', 'Menghapus jurusan: ' . $nama);
        return back()->with('success', 'Jurusan berhasil dihapus!');
    }

    public function export()
    {
        ActivityLogger::log('export_majors', 'Mengunduh data jurusan ke Excel');
        return Excel::download(new MajorsExport, 'Data_Jurusan_SILAMPU.xlsx');
    }
}
