<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Progress Nonton
        Schema::create('watch_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->integer('last_position')->default(0);
            $table->boolean('is_finished')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'video_id']);
        });

        // Tabel Balasan Forum (YANG SUDAH DIPERBARUI)
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained('forums')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom baru untuk Nested Replies
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade');

            $table->text('konten'); // Diubah dari isi_balasan agar sesuai dengan Model
            $table->timestamps();
        });

        // Tabel Komentar Video
        Schema::create('video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('isi_komentar');
            $table->enum('status', ['public', 'private'])->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_comments');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('watch_progress');
    }
};
