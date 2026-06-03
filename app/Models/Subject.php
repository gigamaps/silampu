<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['class_id', 'nama_mapel']; // Ganti unit_id jadi class_id

    public function studentClass()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    // Tambahkan fungsi relasi ini di dalam class Subject
    public function users()
    {
        return $this->belongsToMany(User::class, 'subject_user', 'subject_id', 'user_id');
    }
}
