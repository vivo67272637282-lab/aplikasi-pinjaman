<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pinjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .company-header { width: auto; margin: 0 auto 4px; border-collapse: collapse; }
        .company-logo { padding-right: 8px; vertical-align: middle; }
        .company-logo img { max-width: 64px; max-height: 48px; }
        .company-name { font-size: 13px; font-weight: bold; vertical-align: middle; }
        .company-address { font-size: 9px; color: #666; margin-top: 2px; }
        .sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0d6efd; color: white; padding: 5px 4px; text-align: left; font-size: 9px; }
        td { padding: 3px 4px; border-bottom: 1px solid #eee; }
        tfoot td { background: #f8f9fa; font-weight: bold; }
        .right { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #aaa; text-align: right; }
    </style>
</head>
<body>
    @include('laporan.partials.company-header')
    <p class="sub">Rekap Data Pinjaman Karyawan</p>
    <table>
        <thead>
            <tr>
                <th>#</th><th>No. Pinjaman</th><th>Karyawan</th><th>Tgl.</th>
                <th class="right">Pokok</th><th>Bunga</th><th class="right">Total Pengembalian</th>
                <th>Tenor</th><th class="right">Cicilan</th>
                <th>Pembayaran keberapa</th><th class="right">Total terbayar</th>
                <th class="right">Sisa belum dibayar</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->no_pinjaman }}</td>
                <td>{{ $d->karyawan->nama }}</td>
                <td>{{ $d->tanggal_pinjaman->format('d/m/Y') }}</td>
                <td class="right">{{ number_format($d->jumlah_pinjaman, 0, ',', '.') }}</td>
                <td>{{ number_format($d->bunga_persen, 2, ',', '.') }}%</td>
                <td class="right">{{ number_format($d->total_pengembalian, 0, ',', '.') }}</td>
                <td>{{ $d->tenor }} bln</td>
                <td class="right">{{ number_format($d->cicilan_per_bulan, 0, ',', '.') }}</td>
                <td>{{ $d->pembayarans->count() }} dari {{ $d->tenor }}</td>
                <td class="right">{{ number_format($d->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->sisa_pinjaman, 0, ',', '.') }}</td>
                <td>{{ ucfirst($d->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total ({{ $data->count() }})</td>
                <td class="right">{{ number_format($data->sum('jumlah_pinjaman'), 0, ',', '.') }}</td>
                <td></td>
                <td class="right">{{ number_format($data->sum(fn($pinjaman) => $pinjaman->total_pengembalian), 0, ',', '.') }}</td>
                <td colspan="2"></td>
                <td></td>
                <td class="right">{{ number_format($data->sum(fn($pinjaman) => $pinjaman->pembayarans->sum('jumlah_bayar')), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('sisa_pinjaman'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <p class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
