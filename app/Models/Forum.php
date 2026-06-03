<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['guru_id', 'class_id', 'judul', 'deskripsi', 'status'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function targetClass()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function replies()
    {
        // Hanya ambil komentar root/utama (bukan balasan dari komentar)
        return $this->hasMany(ForumReply::class, 'forum_id')
            ->whereNull('parent_id')
            ->with('childReplies')
            ->latest();
    }
}
