<?php

namespace App\Exports;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Subject::with(['studentClass.unit', 'users'])->latest();

        // Isolasi data ekspor untuk kurikulum agar tidak menyedot data sekolah lain
        if (auth()->user()->role === 'kurikulum') {
            $myUnitId = DB::table('unit_user')->where('user_id', auth()->id())->value('unit_id');
            $query->whereHas('studentClass', function ($q) use ($myUnitId) {
                $q->where('unit_id', $myUnitId);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID Mapel', 'Unit Sekolah', 'Kelas', 'Nama Mata Pelajaran', 'Guru Pengampu'];
    }

    public function map($subject): array
    {
        $guruList = $subject->users->pluck('nama_lengkap')->implode(', ');

        return [
            $subject->id,
            $subject->studentClass->unit->nama_unit ?? '-',
            $subject->studentClass->nama_kelas ?? '-',
            $subject->nama_mapel,
            $guruList ?: 'Belum di-assign',
        ];
    }
}
