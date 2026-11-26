<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Arsip Dokumen</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #172B4D; font-size: 9px; }
        h1, h2 { margin: 0 0 8px; }
        h1 { font-size: 18px; }
        h2 { margin-top: 20px; font-size: 13px; }
        .muted { color: #64748B; }
        .summary { width: 100%; margin: 10px 0 16px; }
        .summary td { border: 1px solid #D7E0EA; padding: 7px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #D7E0EA; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #F1F5F9; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>Laporan Arsip Dokumen</h1>
    <p class="muted">Dibuat {{ now()->format('d M Y H:i') }} · Mengikuti filter dan kewenangan pengguna saat ekspor.</p>
    <table class="summary">
        <tbody>
            <tr><td><strong>Total dokumen</strong><br>{{ $summary['total'] }}</td>
                @foreach (\App\Enums\DokumenVerificationStatus::cases() as $status)
                    <td><strong>{{ $status->label() }}</strong><br>{{ $summary['statuses'][$status->value] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <h2>Dokumen</h2>
    <table>
        <thead><tr><th>Nomor</th><th>Nama</th><th>Proyek</th><th>Kategori</th><th>Status</th><th>Versi terbaru</th><th>Pengunggah</th><th>Dibuat / direvisi</th></tr></thead>
        <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td>{{ $document->nomor_dokumen }}</td>
                    <td>{{ $document->nama_dokumen }}</td>
                    <td>{{ $document->proyek?->nama_proyek }}</td>
                    <td>{{ $document->kategoriDokumen?->nama_kategori }}</td>
                    <td>{{ $document->status_verifikasi->label() }}</td>
                    <td>{{ $document->versiTerbaru?->nomor_versi ?? '-' }}</td>
                    <td>{{ $document->versiTerbaru?->pengunggah?->name ?? '-' }}</td>
                    <td>{{ $document->created_at?->format('d-m-Y') }} / {{ $document->versiTerbaru?->created_at?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada dokumen yang sesuai filter.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Aktivitas audit</h2>
    <table>
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Rincian</th></tr></thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr><td>{{ $activity->created_at?->format('d-m-Y H:i:s') }}</td><td>{{ $activity->user?->name ?? 'Pengguna dihapus' }}</td><td>{{ str_replace('_', ' ', $activity->activity) }}</td><td>{{ $activity->description }}</td></tr>
            @empty
                <tr><td colspan="4">Belum ada aktivitas pada filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
