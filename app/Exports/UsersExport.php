<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::with(['studentClass.unit', 'units'])->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama Lengkap', 'NIS/NIP', 'Username', 'Role', 'Status', 'Kelas', 'Unit (Bisa Lebih Dari 1)'];
    }

    public function map($user): array
    {
        // Logika untuk menampilkan Unit jamak
        if ($user->role === 'siswa') {
            $unitName = $user->studentClass->unit->nama_unit ?? '-';
        } else {
            $unitName = $user->units->isNotEmpty() ? $user->units->pluck('nama_unit')->implode(', ') : '-';
        }

        return [
            $user->id,
            $user->nama_lengkap,
            $user->nis_np,
            $user->username,
            $user->role,
            $user->status,
            $user->studentClass->nama_kelas ?? '-',
            $unitName,
        ];
    }
}
