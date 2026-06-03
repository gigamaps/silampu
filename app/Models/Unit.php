<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama_unit', 'alamat', 'logo'];

    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
