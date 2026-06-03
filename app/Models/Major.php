<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unit_id', 'nama_jurusan'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'jurusan_id');
    }
}
