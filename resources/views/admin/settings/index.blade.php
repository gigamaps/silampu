@extends('layouts.dashboard')

@section('page_title', 'Konfigurasi Sistem')

@section('content')
<div class="space-y-6">

    <div class="-mt-2">
        <p class="text-sm text-slate-500 font-medium">Personalisasi identitas platform dan pengaturan konfigurasi global sistem Blended Learning.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-secondary flex items-center justify-center text-base">
                        <i class="bi bi-gear-wide-connected"></i>
                    </div>
                    <h6 class="m-0 font-bold text-slate-800 text-sm tracking-tight">Identitas Utama Aplikasi</h6>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Aplikasi Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'SILAMPU' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-medium text-slate-800" required autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Yayasan Pendidikan</label>
                            <input type="text" name="nama_yayasan" value="{{ $settings['nama_yayasan'] ?? '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-medium text-slate-800" autocomplete="off">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tahun Ajaran Aktif</label>
                            <input type="text" name="tahun_ajaran" value="{{ $settings['tahun_ajaran'] ?? '2026/2027' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-mono font-bold text-slate-800" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h6 class="m-0 font-bold text-slate-800 text-sm tracking-tight">Branding Platform & Status</h6>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Logo Utama Sistem</label>
                            <div class="flex items-center gap-4 p-3 border border-slate-100 bg-slate-50/50 rounded-xl">
                                <img src="{{ asset('img/app/' . ($settings['app_logo'] ?? 'logo.png')) }}"
                                    alt="Logo Preview" class="w-12 h-12 object-contain bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm shrink-0">
                                <input type="file" name="logo" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-secondary hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 bg-white cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Favicon Browser</label>
                            <div class="flex items-center gap-4 p-3 border border-slate-100 bg-slate-50/50 rounded-xl">
                                <img src="{{ asset('img/app/' . ($settings['app_favicon'] ?? 'favicon.ico')) }}"
                                    alt="Favicon Preview" class="w-10 h-10 object-contain bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm shrink-0">
                                <input type="file" name="favicon" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-secondary hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 bg-white cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mode Perbaikan (Maintenance)</label>
                            <select name="maintenance_mode" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary font-medium text-slate-800">
                                <option value="0" {{ ($settings['maintenance_mode'] ?? '') == '0' ? 'selected' : '' }}>🟢 Aktif (Operasional Normal)</option>
                                <option value="1" {{ ($settings['maintenance_mode'] ?? '') == '1' ? 'selected' : '' }}>🔴 Maintenance (Kunci Akses Pengguna)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-secondary hover:bg-blue-600 text-white font-bold text-sm rounded-xl shadow-md shadow-secondary/20 hover:shadow-lg transition-all duration-200 gap-2">
                    <i class="bi bi-check-all text-xl leading-none"></i> Simpan Seluruh Konfigurasi
                </button>
            </div>

        </div>
    </form>
</div>
@endsection