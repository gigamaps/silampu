@extends('layouts.public')

@section('page_title', 'Tentang Platform')

@section('content')
<header class="bg-gradient-to-b from-primary to-[#0f243e] text-white py-16 border-b border-white/5 relative overflow-hidden w-full">
    <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="container mx-auto px-6 text-center relative z-10 space-y-3">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Mengenal Lebih Dekat Platform</h1>
        <p class="text-slate-400 text-sm sm:text-base font-medium max-w-2xl mx-auto">SILAMPU hadir sebagai lompatan inovasi digitalisasi ekosistem pendidikan yang fleksibel, transparan, dan berorientasi mutu akademik tinggi.</p>
    </div>
</header>

<div class="container mx-auto px-6 py-12 flex-1 space-y-16 w-full">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-7 space-y-4">
            <span class="text-xs font-extrabold uppercase tracking-widest text-secondary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Visi & Komitmen</span>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight leading-tight">Menyatukan Pembelajaran Konvensional dengan Fleksibilitas Digital</h2>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">SILAMPU dikembangkan atas inisiasi gagasan <span class="font-bold text-slate-700">{{ $globalSettings['nama_yayasan'] ?? 'Cakrawala Foundation' }}</span> untuk menjawab tantangan modernisasi manajemen sekolah. Dengan menerapkan metode *Blended Learning*, siswa tidak lagi berjarak dengan materi pembelajaran. Akses video, podcast, dan interaksi forum belajar kini berada di dalam satu genggaman kendali yang terpusat.</p>
        </div>
        <div class="lg:col-span-5 bg-white border border-slate-200 shadow-sm p-6 rounded-2xl flex items-center justify-center">
            <i class="bi bi-shield-check text-secondary text-[5.5rem] p-4 bg-blue-50/60 rounded-3xl border border-blue-100/50"></i>
        </div>
    </div>

    <div class="space-y-6">
        <div class="text-center max-w-md mx-auto space-y-1.5">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Pilar Ekosistem Utama</h2>
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">3 Komponen Kekuatan Utama SILAMPU</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-secondary flex items-center justify-center text-xl border border-blue-100"><i class="bi bi-play-btn-fill"></i></div>
                <h4 class="font-extrabold text-slate-800 text-sm tracking-tight">Video On Demand (VOD)</h4>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Guru dapat menyiarkan materi edukasi mandiri maupun podcast kurikulum, memungkinkankan siswa mengulang materi kapan pun mereka butuhkan.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl border border-purple-100"><i class="bi bi-chat-square-text-fill"></i></div>
                <h4 class="font-extrabold text-slate-800 text-sm tracking-tight">Interaksi Ruang Forum</h4>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Ruang tanya jawab interaktif per-mata pelajaran yang mendekatkan komunikasi akademik langsung antara siswa dengan guru pengampu kelas.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100"><i class="bi bi-graph-up-arrow"></i></div>
                <h4 class="font-extrabold text-slate-800 text-sm tracking-tight">Audit Monitoring Transparan</h4>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Sistem logging logistik mencatat aktivitas user secara real-time, memberikan rasa aman dan transparansi penuh bagi tim administrator yayasan.</p>
            </div>
        </div>
    </div>
</div>
@endsection