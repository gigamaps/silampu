<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    use HasFactory;

    // Pastikan nama tabel didefinisikan jika pluralisasinya tidak standar
    protected $table = 'video_comments';

    protected $fillable = [
        'video_id',
        'user_id',
        'parent_id',
        'isi_komentar',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Video
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function replies()
    {
        return $this->hasMany(VideoComment::class, 'parent_id');
    }
}
