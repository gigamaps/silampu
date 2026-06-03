<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SiswaVideoController;
use App\Http\Controllers\SiswaForumController;
use App\Http\Controllers\SiswaHistoryController;
use App\Http\Controllers\VideoCommentController;
use Illuminate\Support\Facades\Route;

// 1. RUTE PUBLIK (Bisa diakses tanpa login)
Route::get('/', function () {
    // Ambil 4 video terbaru yang berstatus publik
    $latestVideos = \App\Models\Video::with(['subject', 'uploader'])
        ->where('status', 'public')
        ->latest()
        ->take(4)
        ->get();

    return view('welcome', compact('latestVideos'));
});

Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');
Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

// RUTE VIDEO UNTUK VISITOR (Pindahkan ke luar auth agar visitor bisa nonton)
Route::get('/videos/{video:slug}', [VideoController::class, 'show'])->name('videos.show');

// 2. RUTE YANG BUTUH LOGIN
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ================================================================
    // A. AREA RUTE GLOBAL (Aksi yang butuh login)
    // ================================================================
    Route::post('/videos/{video:slug}/track', [VideoController::class, 'trackProgress'])->name('videos.track');

    // RUTE KOMENTAR
    Route::post('/videos/{video}/comments', [VideoCommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [VideoCommentController::class, 'update'])->name('comments.update'); // BARIS BARU
    Route::delete('/comments/{comment}', [VideoCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/toggle-hide', [VideoCommentController::class, 'toggleHide'])->name('comments.toggle_hide');

    // ================================================================
    // A. AREA RUTE GLOBAL (Bisa diakses Admin, Kurikulum, Guru, Siswa)
    // ================================================================
    // Route::get('/videos/{video:slug}', [VideoController::class, 'show'])->name('videos.show');
    // Route::post('/videos/{video:slug}/track', [VideoController::class, 'trackProgress'])->name('videos.track');

    // ================================================================
    // B. AREA INTERNAL ADMINISTRATOR (STRICTLY ADMIN ONLY)
    // ================================================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Master Data
        Route::resource('units', UnitController::class);
        Route::resource('users', UserController::class)->except(['show', 'create']);
        Route::get('/users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

        // Manajemen Akademik
        Route::resource('majors', MajorController::class)->except(['show', 'create']);
        Route::resource('classes', ClassesController::class)->except(['create', 'show']);
        Route::get('/classes/{encrypted_id}/show', [ClassesController::class, 'show'])->name('classes.show');
        Route::post('/classes/{encrypted_id}/promote', [ClassesController::class, 'promote'])->name('classes.promote');
        Route::resource('subjects', SubjectController::class)->except(['show', 'create']);

        Route::get('/majors/export', [MajorController::class, 'export'])->name('majors.export');
        Route::get('/classes/export', [ClassesController::class, 'export'])->name('classes.export');
        Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export');

        // Monitoring Sistem (LENGKAP DENGAN EDIT & DELETE)
        Route::get('/monitoring/active-users', [MonitoringController::class, 'activeUsers'])->name('monitoring.active_users');

        Route::get('/monitoring/videos', [MonitoringController::class, 'videos'])->name('monitoring.videos');
        Route::post('/monitoring/videos', [MonitoringController::class, 'storePodcast'])->name('monitoring.videos.store');
        Route::get('/monitoring/videos/{video}/edit', [MonitoringController::class, 'editVideo'])->name('monitoring.videos.edit');
        Route::put('/monitoring/videos/{video}', [MonitoringController::class, 'updateVideo'])->name('monitoring.videos.update');
        Route::delete('/monitoring/videos/{video}', [MonitoringController::class, 'destroyVideo'])->name('monitoring.videos.destroy');

        Route::get('/monitoring/activity-logs', [ActivityLogController::class, 'index'])->name('monitoring.activity_logs');
        Route::delete('/monitoring/activity-logs/clear', [ActivityLogController::class, 'clearOld'])->name('monitoring.activity_logs.clear');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // ================================================================
    // C. AREA INTERNAL TIM KURIKULUM (URL BERSIH /kurikulum/...)
    // ================================================================
    Route::middleware(['role:kurikulum'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::get('/dashboard', function () {
            return view('kurikulum.dashboard');
        })->name('dashboard');

        Route::resource('majors', MajorController::class)->except(['show', 'create']);
        Route::resource('classes', ClassesController::class)->except(['create', 'show']);
        Route::get('/classes/{encrypted_id}/show', [ClassesController::class, 'show'])->name('classes.show');
        Route::post('/classes/{encrypted_id}/promote', [ClassesController::class, 'promote'])->name('classes.promote');
        Route::resource('subjects', SubjectController::class)->except(['show', 'create']);

        Route::get('/majors/export', [MajorController::class, 'export'])->name('majors.export');
        Route::get('/classes/export', [ClassesController::class, 'export'])->name('classes.export');
        Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export');

        // Monitoring Video Khusus Kurikulum
        Route::get('/monitoring/videos', [MonitoringController::class, 'videos'])->name('monitoring.videos');
        Route::post('/monitoring/videos', [MonitoringController::class, 'storePodcast'])->name('monitoring.videos.store');
        Route::get('/monitoring/videos/{video}/edit', [MonitoringController::class, 'editVideo'])->name('monitoring.videos.edit');
        Route::put('/monitoring/videos/{video}', [MonitoringController::class, 'updateVideo'])->name('monitoring.videos.update');
        Route::delete('/monitoring/videos/{video}', [MonitoringController::class, 'destroyVideo'])->name('monitoring.videos.destroy');

        Route::get('/monitoring/activity-logs', [ActivityLogController::class, 'index'])->name('monitoring.activity_logs');
    });

    // ================================================================
    // D. AREA ROLE GURU & SISWA
    // ================================================================
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {

        Route::get('/dashboard', function () {
            return view('guru.dashboard');
        })->name('dashboard');

        // RUTE BARU UNTUK AKADEMIK GURU
        Route::get('/subjects', [\App\Http\Controllers\GuruSubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/{encrypted_id}/students', [\App\Http\Controllers\GuruSubjectController::class, 'students'])->name('subjects.students');
        // RUTE BARU: Kelola Video Materi Guru
        Route::get('/videos', [\App\Http\Controllers\GuruVideoController::class, 'index'])->name('videos.index');
        Route::post('/videos', [\App\Http\Controllers\GuruVideoController::class, 'store'])->name('videos.store');
        Route::get('/videos/{video}/edit', [\App\Http\Controllers\GuruVideoController::class, 'edit'])->name('videos.edit');
        Route::put('/videos/{video}', [\App\Http\Controllers\GuruVideoController::class, 'update'])->name('videos.update');
        Route::delete('/videos/{video}', [\App\Http\Controllers\GuruVideoController::class, 'destroy'])->name('videos.destroy');
        Route::get('/videos/{video:slug}/stats', [\App\Http\Controllers\GuruVideoController::class, 'statsPage'])->name('videos.stats');
        // RUTE BARU: Forum Diskusi Kelas (Pake Enkripsi)
        Route::get('/forums', [\App\Http\Controllers\ForumController::class, 'index'])->name('forums.index');
        Route::post('/forums', [\App\Http\Controllers\ForumController::class, 'store'])->name('forums.store');
        Route::get('/forums/{encrypted_id}', [\App\Http\Controllers\ForumController::class, 'show'])->name('forums.show');
        Route::get('/forums/{encrypted_id}/edit', [\App\Http\Controllers\ForumController::class, 'edit'])->name('forums.edit');
        Route::put('/forums/{encrypted_id}', [\App\Http\Controllers\ForumController::class, 'update'])->name('forums.update');
        Route::delete('/forums/{encrypted_id}', [\App\Http\Controllers\ForumController::class, 'destroy'])->name('forums.destroy');
        Route::post('/forums/{encrypted_id}/reply', [\App\Http\Controllers\ForumController::class, 'storeReply'])->name('forums.reply');
    });

    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        // 1. Dashboard Siswa
        Route::get('/dashboard', function () {
            return view('siswa.dashboard');
        })->name('dashboard');

        // 2. Materi Kelasku (Berbeda dengan katalog publik)
        Route::get('/video-pembelajaran', [SiswaVideoController::class, 'index'])->name('videos.index');

        // 3. Diskusi Pelajaran
        Route::get('/diskusi', [SiswaForumController::class, 'index'])->name('forums.index');
        Route::get('/diskusi/{encrypted_id}', [SiswaForumController::class, 'show'])->name('forums.show');
        Route::post('/diskusi/{encrypted_id}/reply', [SiswaForumController::class, 'storeReply'])->name('forums.reply');

        // 4. Riwayat Tontonan
        Route::get('/riwayat', [SiswaHistoryController::class, 'index'])->name('history.index');
    });
});

require __DIR__ . '/auth.php';
