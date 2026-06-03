<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Hanya mengambil video yang berstatus 'public' secara mutlak
        $query = Video::with(['uploader', 'unit', 'subject'])->where('status', 'public');

        // Filter 1: Berdasarkan Tipe Video (Dropdown Baru: Materi / Podcast)
        if ($request->filled('tipe_video')) {
            $query->where('tipe_video', $request->tipe_video);
        }

        // Filter 2: Pencarian Universal
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Cari di judul video
                $q->where('judul', 'like', "%{$search}%")
                    // Cari di tingkat kelas
                    ->orWhere('target_tingkat', 'like', "%{$search}%")
                    // Cari di nama mata pelajaran
                    ->orWhereHas('subject', function ($inner) use ($search) {
                        $inner->where('nama_mapel', 'like', "%{$search}%");
                    })
                    // Cari di nama unit sekolah
                    ->orWhereHas('unit', function ($inner) use ($search) {
                        $inner->where('nama_unit', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil data video dengan pagination (tetap 12 video per halaman)
        $videos = $query->latest()->paginate(12)->withQueryString();

        // Lempar ke view 'katalog'
        return view('katalog', compact('videos'));
    }
}
