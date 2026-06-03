<?php

namespace App\Exports;

use App\Models\Classes;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Classes::with(['unit', 'major'])->latest();

        // Jika Kurikulum yang mengunduh, isolasi data rombel sekolah miliknya saja
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->where('unit_id', $myUnitId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID Kelas', 'Unit Sekolah', 'Jurusan', 'Tingkat', 'Nama Kelas', 'Tanggal Dibuat'];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->unit->nama_unit ?? '-',
            $class->major->nama_jurusan ?? 'Umum (Tanpa Jurusan)',
            'Kelas ' . $class->tingkat_kelas,
            $class->nama_kelas,
            $class->created_at ? $class->created_at->format('d-m-Y H:i') : '-',
        ];
    }
}
