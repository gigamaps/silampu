<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'judul',
        'slug',
        'tipe_video',
        'youtube_id',
        'deskripsi',
        'durasi',
        'file_modul',
        'subject_id',
        'target_tingkat',
        'unit_id',
        'uploader_id',
        'status',
        'views'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function comments()
    {
        return $this->hasMany(VideoComment::class);
    }
}
