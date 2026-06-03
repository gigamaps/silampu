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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->enum('tipe_video', ['pembelajaran', 'podcast']);
            $table->string('youtube_id', 50);
            $table->text('deskripsi')->nullable();
            $table->string('durasi', 10)->nullable();
            $table->string('file_modul')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->enum('target_tingkat', ['7', '8', '9', '10', '11', '12', 'umum'])->default('umum');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['public', 'private'])->default('public');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
