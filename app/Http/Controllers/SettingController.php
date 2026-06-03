<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class SettingController extends Controller
{
    public function index()
    {
        // Catat aktivitas akses konfigurasi sistem
        ActivityLogger::log('view_settings', 'Mengakses halaman konfigurasi sistem global');

        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Loop input teks biasa
        foreach ($request->except('_token', 'logo', 'favicon') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. Handle Upload Logo Sekolah
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/app'), $filename);
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $filename]);
        }

        // 3. Handle Upload Favicon Browser
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $filename = 'favicon.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/app'), $filename);
            Setting::updateOrCreate(['key' => 'app_favicon'], ['value' => $filename]);
        }

        // Catat perubahan sistem ke log aktivitas
        ActivityLogger::log('update_settings', 'Memperbarui data konfigurasi identitas dan branding platform');

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui!');
    }
}
