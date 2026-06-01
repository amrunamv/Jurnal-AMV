<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .content { margin-bottom: 20px; }
        .footer { font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Laporan Bulanan {{ $siteName }}</h2>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Terlampir adalah laporan aktivitas bulanan untuk periode <strong>{{ $month }}</strong>.</p>
            <p>Laporan ini dikirimkan secara otomatis oleh sistem AMV Open Science untuk memantau perkembangan jurnal dan aktivitas pengguna sepanjang bulan lalu.</p>
            <p>Silakan buka file PDF terlampir untuk melihat rincian statistik.</p>
        </div>
        <div class="footer">
            <p>Pesan ini dikirimkan secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ $siteName }}</p>
        </div>
    </div>
</body>
</html>
