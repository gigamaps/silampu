<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // Pastikan semua kolom ini masuk agar tidak kena Mass Assignment Exception
    protected $fillable = [
        'nis_np',
        'username',
        'password',
        'nama_lengkap',
        'role',
        'status',
        'class_id',
        'foto_profil',
        'last_seen_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    // Relasi Many-to-Many ke Unit (Sekolah)
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_user');
    }

    // Relasi Many-to-Many ke Mata Pelajaran (Khusus Guru)
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    // Relasi ke Kelas (Khusus Siswa)
    public function studentClass()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
