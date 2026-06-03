<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unit_id', 'jurusan_id', 'tingkat_kelas', 'nama_kelas'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'jurusan_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }
}
