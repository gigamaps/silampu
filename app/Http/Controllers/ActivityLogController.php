<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log('view_activity_logs', 'Melihat daftar log aktivitas sistem');

        $query = ActivityLog::with('user')->latest();
        $isKurikulum = auth()->user()->role === 'kurikulum';

        // --- SCOPING MULTI-TENANT ---
        // Jika Kurikulum, hanya tampilkan log aktivitas dari pengguna (user) di unitnya
        if ($isKurikulum) {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');

            $query->whereHas('user', function ($userQuery) use ($myUnitId) {
                // Mengecek apakah user dalam log terkait dengan unit Kurikulum saat ini
                $userQuery->whereHas('units', function ($unitPivot) use ($myUnitId) {
                    $unitPivot->where('unit_id', $myUnitId);
                })->orWhereHas('studentClass', function ($classQuery) use ($myUnitId) {
                    $classQuery->where('unit_id', $myUnitId);
                });
            });
        }

        // 2. Fitur Pencarian Pintar (Cari Berdasarkan Aksi, Deskripsi, atau Nama Pengguna)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Fitur Filter Jumlah Tampilan Data (Default 25 disinkronkan)
        $perPage = $request->per_page ?? 25;
        $logs = $query->paginate($perPage)->withQueryString();

        return view('admin.monitoring.activity_logs', compact('logs'));
    }

    public function clearOld()
    {
        // Proteksi Tambahan: Hanya Admin yang bisa mengakses rute ini
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Fitur ini khusus Administrator.');
        }

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Hapus log di bawah 30 hari dan rekam jumlahnya
        $deletedCount = ActivityLog::where('created_at', '<', $thirtyDaysAgo)->delete();

        if ($deletedCount > 0) {
            ActivityLogger::log('clear_activity_logs', "Membersihkan log lama. Total $deletedCount baris terhapus.");
            return back()->with('success', "$deletedCount data log aktivitas lama berhasil dibersihkan!");
        }

        return back()->with('success', 'Database sudah bersih. Tidak ada log aktivitas yang berumur lebih dari 30 hari.');
    }
}
