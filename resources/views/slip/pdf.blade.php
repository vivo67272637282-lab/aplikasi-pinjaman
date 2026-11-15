<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Potongan — {{ $potongan->karyawan->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .slip-container { max-width: 500px; margin: 20px auto; border: 2px solid #0d6efd; border-radius: 8px; overflow: hidden; }
        .slip-header { background: #0d6efd; color: white; padding: 14px 16px; }
        .slip-header h2 { font-size: 14px; margin-bottom: 2px; }
        .slip-header p { font-size: 10px; opacity: 0.85; }
        .company-identity { border-collapse: collapse; }
        .company-identity td { vertical-align: middle; }
        .company-logo { padding-right: 8px; }
        .company-logo img { max-width: 48px; max-height: 36px; }
        .company-name { font-size: 14px; font-weight: bold; }
        .company-address { font-size: 9px; opacity: 0.85; }
        .periode-info { float: right; text-align: right; }
        .slip-body { padding: 14px 16px; }
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 4px; }
        .info-table .label { color: #666; width: 110px; }
        hr { border: none; border-top: 1px solid #ddd; margin: 8px 0; }
        .potongan-table { width: 100%; }
        .potongan-table td { padding: 3px 4px; }
        .potongan-table .label { color: #555; }
        .potongan-table .value { text-align: right; }
        .potongan-table .potongan { color: #dc3545; }
        .potongan-table .total-row td { font-weight: bold; background: #f8f9fa; padding: 4px; }
        .loan-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .loan-table th, .loan-table td { padding: 4px; border-bottom: 1px solid #eee; text-align: left; }
        .loan-table th { background: #f8f9fa; color: #555; }
        .loan-table .value { text-align: right; }
        .sub-label { padding-left: 12px !important; }
        .section-title { font-size: 9px; text-transform: uppercase; font-weight: bold; color: #888; padding-top: 4px; }
        .slip-footer { background: #f8f9fa; padding: 8px 16px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; }
        .clearfix::after { content: ''; display: table; clear: both; }
    </style>
</head>
<body>
<div class="slip-container">
    <div class="slip-header clearfix">
        <div class="periode-info">
            <strong>{{ $potongan->periode->nama }}</strong><br>
            <span>{{ $potongan->periode->tanggal_mulai->format('d/m/Y') }} – {{ $potongan->periode->tanggal_selesai->format('d/m/Y') }}</span>
        </div>
        @include('slip.partials.company-identity')
        <p>RINCIAN POTONGAN KARYAWAN</p>
    </div>
    <div class="slip-body">
        <table class="info-table">
            <tr><td class="label">Nama</td><td>: <strong>{{ $potongan->karyawan->nama }}</strong></td></tr>
            <tr><td class="label">NIK</td><td>: {{ $potongan->karyawan->nik }}</td></tr>
            <tr><td class="label">Jabatan</td><td>: {{ $potongan->karyawan->jabatan ?: '-' }}</td></tr>
        </table>
        <hr>
        <table class="potongan-table">
            <tr><td colspan="2" class="section-title">Potongan</td></tr>
            <tr>
                <td class="label sub-label">Potongan Pinjaman</td>
                <td class="value potongan">- Rp {{ number_format($potongan->potongan_pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label sub-label">Potongan Kasbon</td>
                <td class="value potongan">- Rp {{ number_format($potongan->potongan_kasbon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label sub-label">Simpanan Wajib</td>
                <td class="value potongan">- Rp {{ number_format($potongan->potongan_simpanan_wajib, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label sub-label">Simpanan Sukarela</td>
                <td class="value potongan">- Rp {{ number_format($potongan->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
            </tr>
            @if($potongan->potongan_lainnya > 0)
            <tr>
                <td class="label sub-label">{{ $potongan->keterangan_lainnya ?: 'Lainnya' }}</td>
                <td class="value potongan">- Rp {{ number_format($potongan->potongan_lainnya, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label">Total Potongan</td>
                <td class="value potongan">- Rp {{ number_format($potongan->total_potongan, 0, ',', '.') }}</td>
            </tr>
        </table>
        <table class="loan-table">
            <thead>
                <tr><th colspan="4">Pembayaran Pinjaman</th></tr>
                <tr><th>No. Pinjaman</th><th>Pembayaran keberapa</th><th>Total terbayar</th><th>Sisa belum dibayar</th></tr>
            </thead>
            <tbody>
                @forelse($potongan->karyawan->pinjamans->whereIn('status', ['disetujui', 'lunas']) as $pinjaman)
                <tr>
                    <td>{{ $pinjaman->no_pinjaman }}</td>
                    <td>{{ $pinjaman->pembayarans->count() }} dari {{ $pinjaman->tenor }}</td>
                    <td class="value">Rp {{ number_format($pinjaman->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                    <td class="value">Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Belum ada pinjaman aktif atau lunas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="slip-footer">
        Dicetak: {{ now()->format('d/m/Y H:i') }} — Dokumen ini digenerate oleh sistem
    </div>
</div>
</body>
</html>
