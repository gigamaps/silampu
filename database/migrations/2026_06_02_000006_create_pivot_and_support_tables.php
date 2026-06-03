<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pivot Tabel (Dibiarkan tetap sama)
        Schema::create('unit_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
        });

        Schema::create('subject_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
        });

        // 2. Tabel Settings (DIBUAT DINAMIS: Key-Value)
        // Ini kunci agar error 'Column not found' hilang selamanya
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // ID unik untuk setting
            $table->text('value')->nullable(); // Nilai dari setting
            $table->timestamps();
        });

        // 3. Tabel Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 100); // Nama aksinya (contoh: 'login', 'update_video')
            $table->text('description'); // Deskripsi detail
            $table->string('ip_address', 45)->nullable();
            $table->timestamps(); // Menggunakan standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('subject_user');
        Schema::dropIfExists('unit_user');
    }
};
