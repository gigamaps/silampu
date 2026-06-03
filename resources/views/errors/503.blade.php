<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perbaikan - {{ \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'SILAMPU' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css'])
</head>

<body class="bg-slate-50 font-['Plus_Jakarta_Sans'] min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-2 animate-bounce">
            <i class="bi bi-tools"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-800">Sistem Sedang Diperbarui</h1>
        <p class="text-slate-500 font-medium">Kami sedang melakukan peningkatan sistem dan pemeliharaan rutin pada platform pembelajaran. Mohon kembali beberapa saat lagi.</p>

        <div class="pt-6 border-t border-slate-200 text-sm text-slate-400">
            Tim IT {{ \App\Models\Setting::where('key', 'nama_yayasan')->value('value') ?? 'Cakrawala Foundation' }}
        </div>
    </div>
</body>

</html>