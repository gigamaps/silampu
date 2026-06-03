<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Unit;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public $importedCount = 0;
    public $skippedCount = 0;

    public function model(array $row)
    {
        // 1. Skip baris kosong (jika ada baris Excel yang tidak ada isinya)
        if (empty($row['nama_lengkap']) || empty($row['nis_nip'])) {
            return null;
        }

        $username = !empty($row['username']) ? $row['username'] : $row['nis_nip'];
        $password = !empty($row['password']) ? $row['password'] : $row['nis_nip'];
        $role = strtolower($row['role']);

        // 2. CEK DUPLIKAT: Jika NIS atau Username SUDAH ADA, lewati baris ini dan tambah angka skipped
        if (User::where('nis_np', $row['nis_nip'])->orWhere('username', $username)->exists()) {
            $this->skippedCount++;
            return null; // return null artinya data ini dibatalkan untuk disimpan
        }

        $class_id = null;
        $unit_ids_to_attach = [];

        // MEMBACA MULTIPLE UNIT DENGAN KOMA
        if (!empty($row['nama_unit'])) {
            $nama_units = array_map('trim', explode(',', $row['nama_unit']));

            foreach ($nama_units as $nama) {
                $unit = Unit::where('nama_unit', 'like', '%' . $nama . '%')->first();
                if ($unit) {
                    $unit_ids_to_attach[] = $unit->id;
                }
            }
        }

        // PENENTUAN KELAS SISWA (Hanya ambil unit PERTAMA yang terdeteksi)
        if ($role === 'siswa' && !empty($row['nama_kelas']) && count($unit_ids_to_attach) > 0) {
            $kelas = Classes::where('nama_kelas', 'like', '%' . trim($row['nama_kelas']) . '%')
                ->where('unit_id', $unit_ids_to_attach[0])
                ->first();
            if ($kelas) {
                $class_id = $kelas->id;
            }
        }

        $user = User::create([
            'nama_lengkap' => $row['nama_lengkap'],
            'nis_np'       => $row['nis_nip'],
            'username'     => $username,
            'password'     => Hash::make($password),
            'role'         => $role,
            'class_id'     => $class_id,
            'status'       => 'aktif',
        ]);

        // ATTACH MULTIPLE UNIT JIKA GURU ATAU KURIKULUM
        if (($role === 'kurikulum' || $role === 'guru') && count($unit_ids_to_attach) > 0) {
            $user->units()->attach($unit_ids_to_attach);
        }

        $this->importedCount++;

        return $user;
    }
}
