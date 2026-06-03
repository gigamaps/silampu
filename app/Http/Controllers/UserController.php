<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log('view_users', 'Melihat daftar pengguna');

        $query = User::with(['studentClass.unit', 'units'])->where('users.id', '!=', Auth::id())->latest();

        // Fitur Pencarian (Berdasarkan Nama, NIS, atau Username)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis_np', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Jumlah Data (Default 10)
        $perPage = $request->per_page ?? 10;

        // Fitur Pagination Pintar: withQueryString() agar saat pindah halaman, filter pencarian tidak hilang
        $users = $query->paginate($perPage)->withQueryString();

        $classes = Classes::with('unit')->orderBy('nama_kelas')->get();
        $units = \App\Models\Unit::all();

        return view('admin.users.index', compact('users', 'classes', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nis_np' => 'required|string|max:50|unique:users,nis_np',
            'username' => 'required|string|max:50|unique:users,username',
            'role' => 'required|in:admin,kurikulum,guru,siswa',
            'class_id' => 'nullable|exists:classes,id',

            // UBAHAN: Terima unit sebagai array (bisa lebih dari satu)
            'unit_ids' => 'nullable|array',
            'unit_ids.*' => 'exists:units,id',

            'password' => 'nullable|string|min:6',
        ], [
            'nis_np.unique' => 'NIS atau NP ini sudah terdaftar di sistem.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'password.min' => 'Password minimal harus berisi 6 karakter.',
        ]);

        $class_id = $request->role === 'siswa' ? $request->class_id : null;
        $password = $request->password ? Hash::make($request->password) : Hash::make($request->username);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nis_np' => $request->nis_np,
            'username' => $request->username,
            'password' => $password,
            'role' => $request->role,
            'class_id' => $class_id,
            'status' => 'aktif',
        ]);

        // UBAHAN: Simpan ke tabel pivot unit_user untuk role Kurikulum DAN Guru
        if (in_array($user->role, ['kurikulum', 'guru']) && $request->has('unit_ids')) {
            $user->units()->attach($request->unit_ids);
        }

        ActivityLogger::log('create_user', 'Menambahkan pengguna: ' . $user->nama_lengkap);
        return back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        // Load relasi units agar JS bisa membaca unit_id milik kurikulum dan guru
        $user->load('units');
        $classes = Classes::with('unit')->orderBy('nama_kelas')->get();
        return response()->json(['user' => $user, 'classes' => $classes]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nis_np' => ['required', 'string', 'max:50', Rule::unique('users', 'nis_np')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => 'required|in:admin,kurikulum,guru,siswa',
            'class_id' => 'nullable|exists:classes,id',

            // UBAHAN: Terima unit sebagai array
            'unit_ids' => 'nullable|array',
            'unit_ids.*' => 'exists:units,id',
        ]);

        $data = $request->only(['nama_lengkap', 'nis_np', 'username', 'role']);
        $data['class_id'] = $request->role === 'siswa' ? $request->class_id : null;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // UBAHAN: Update relasi pivot tabel secara massal (sync) untuk Guru & Kurikulum
        if (in_array($user->role, ['kurikulum', 'guru']) && $request->has('unit_ids')) {
            $user->units()->sync($request->unit_ids);
        } else {
            // Jika role diubah jadi bukan kurikulum/guru, hapus semua relasi unit-nya
            $user->units()->detach();
        }

        ActivityLogger::log('update_user', 'Mengubah data pengguna: ' . $user->nama_lengkap);
        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $nama = $user->nama_lengkap;
        $user->delete();
        ActivityLogger::log('delete_user', 'Menghapus pengguna: ' . $nama);
        return back()->with('success', 'Pengguna berhasil dihapus!');
    }

    public function export()
    {
        ActivityLogger::log('export_users', 'Mengunduh data pengguna ke Excel');
        return Excel::download(new UsersExport, 'Data_Pengguna_SILAMPU.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Jadikan UsersImport sebagai instance agar kita bisa membaca variabel hitungannya
        $import = new UsersImport;
        Excel::import($import, $request->file('file'));

        // Catat di Activity Log dengan detail angkanya
        ActivityLogger::log('import_users', "Impor Excel: {$import->importedCount} sukses, {$import->skippedCount} duplikat dilewati.");

        // Buat pesan dinamis berdasarkan ada/tidaknya data yang duplikat
        if ($import->skippedCount > 0) {
            return back()->with('success', "Impor selesai! {$import->importedCount} data berhasil ditambahkan, namun ada {$import->skippedCount} data yang dilewati karena NIS/Username sudah terdaftar.");
        }

        return back()->with('success', "Luar biasa! Semua {$import->importedCount} data pengguna berhasil diimpor tanpa kendala.");
    }

    public function downloadTemplate()
    {
        $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function headings(): array
            {
                // Kolom diganti menjadi NAMA bukan ID
                return ['nama_lengkap', 'nis_nip', 'username', 'password', 'role', 'nama_unit', 'nama_kelas'];
            }
            public function array(): array
            {
                return [
                    ['Laura Basuki', '26271501', '', '', 'siswa', 'SMA cakrawala', 'X IPA 1'],
                    ['Rano Karno', '18197250', '', '', 'kurikulum', 'SMp cakrawala', ''],
                    // UBAHAN: Contoh guru dengan koma (Multi-Unit)
                    ['Bima Sakti', '18196372', '', '', 'guru', 'SMA Cakrawala, SMK Cakrawala', '']
                ];
            }
        };

        ActivityLogger::log('download_template', 'Mengunduh template impor pengguna');
        return Excel::download($export, 'Template_Impor_Pengguna.xlsx');
    }
}
