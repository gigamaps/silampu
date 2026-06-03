<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = ['forum_id', 'user_id', 'parent_id', 'konten'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id');
    }

    // Mengambil balasan-balasan dari komentar ini
    public function childReplies()
    {
        return $this->hasMany(ForumReply::class, 'parent_id')->with('user')->oldest();
    }
}
