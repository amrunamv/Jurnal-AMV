<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - {{ $month }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1e3a8a; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; color: #1e40af; }
        .stats-grid { width: 100%; border-collapse: collapse; }
        .stats-grid th, .stats-grid td { padding: 10px; border: 1px solid #e5e7eb; text-align: left; }
        .stats-grid th { background-color: #f3f4f6; }
        .summary-card { padding: 15px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 10px; }
        .summary-value { font-size: 20px; font-weight: bold; color: #2563eb; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        @if($logo)
            <img src="{{ $logo }}" style="max-height: 80px; margin-bottom: 10px;">
        @endif
        <h1>{{ $site_name }}</h1>
        <p>Laporan Aktivitas Bulanan</p>
        <p>Periode: {{ $month }} ({{ $stats['period'] }})</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Manuskrip</div>
        <table class="stats-grid">
            <tr>
                <th>Metrik</th>
                <th>Jumlah</th>
            </tr>
            <tr>
                <td>Manuskrip Baru Dikirim</td>
                <td class="summary-value">{{ $stats['manuscripts']['submitted'] }}</td>
            </tr>
            <tr>
                <td>Manuskrip Berhasil Terbit</td>
                <td class="summary-value">{{ $stats['manuscripts']['published'] }}</td>
            </tr>
            <tr>
                <td>Manuskrip Ditolak</td>
                <td class="summary-value">{{ $stats['manuscripts']['rejected'] }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Pengguna & Komunitas</div>
        <div class="summary-card">
            <div>Pengguna Baru Terdaftar pada periode ini:</div>
            <div class="summary-value">{{ $stats['users']['new'] }} User</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Aktivitas Jurnal</div>
        <table class="stats-grid">
            <thead>
                <tr>
                    <th>Nama Jurnal</th>
                    <th>Jumlah Submission Baru</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['journals'] as $journal)
                <tr>
                    <td>{{ $journal->name }}</td>
                    <td>{{ $journal->manuscripts_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak otomatis oleh AMV Open Science System pada {{ $generated_at }}
    </div>
</body>
</html>
