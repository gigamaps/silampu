<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Unit;
use App\Models\Major;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Setting; // Tambahkan import Setting
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'nis_np' => 'admin001',
            'username' => 'admin',
            'password' => Hash::make('123'),
            'nama_lengkap' => 'Administrator',
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // 2. Create Unit
        $unit = Unit::create(['nama_unit' => 'SMA Cakrawala']);

        // 3. Create Major (Jurusan)
        $major = Major::create([
            'nama_jurusan' => 'Ilmu Pengetahuan Alam',
            'unit_id' => $unit->id
        ]);

        // 4. Create Classes
        // Pastikan nama kolom 'major_id' sesuai dengan nama kolom di migrasi tabel classes
        $class = Classes::create([
            'unit_id' => $unit->id,
            'jurusan_id' => $major->id,
            'nama_kelas' => 'X IPA 1',
            'tingkat_kelas' => '10'
        ]);

        // 5. Create Subjects
        $subjects = ['Matematika', 'Bahasa Indonesia', 'Penjaskes', 'Bahasa Inggris'];
        foreach ($subjects as $nama) {
            Subject::create([
                'class_id' => $class->id,
                'nama_mapel' => $nama
            ]);
        }

        // 6. Create Settings
        Setting::insert([
            ['key' => 'app_name', 'value' => 'SILAMPU'],
            ['key' => 'nama_yayasan', 'value' => 'Cakrawala Foundation'],
            ['key' => 'app_logo', 'value' => 'logo.png'],
            ['key' => 'app_favicon', 'value' => 'favicon.ico'],
            ['key' => 'tahun_ajaran', 'value' => '2025/2026'],
            ['key' => 'maintenance_mode', 'value' => '0'],
        ]);
    }
}
