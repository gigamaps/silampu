<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchProgress extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit karena nama tabelnya tidak menggunakan format jamak bahasa Inggris bawaan
    protected $table = 'watch_progress';

    protected $fillable = ['user_id', 'video_id', 'last_position', 'is_finished'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
