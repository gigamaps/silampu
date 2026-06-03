<?php

namespace App\Exports;

use App\Models\Major;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MajorsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Major::with('unit')->latest();

        // --- SCOPING EKSPOR MULTI-TENANT ---
        // Jika yang mengunduh adalah Kurikulum, kunci data agar hanya memuat unit miliknya saja
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->where('unit_id', $myUnitId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID Jurusan', 'Nama Unit Sekolah', 'Nama Jurusan', 'Tanggal Dibuat'];
    }

    public function map($major): array
    {
        return [
            $major->id,
            $major->unit->nama_unit ?? 'Unit Dihapus',
            $major->nama_jurusan,
            $major->created_at ? $major->created_at->format('d-m-Y H:i') : '-',
        ];
    }
}
