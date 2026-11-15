<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kasbon</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .company-header { width: auto; margin: 0 auto 4px; border-collapse: collapse; }
        .company-logo { padding-right: 8px; vertical-align: middle; }
        .company-logo img { max-width: 64px; max-height: 48px; }
        .company-name { font-size: 13px; font-weight: bold; vertical-align: middle; }
        .company-address { font-size: 9px; color: #666; margin-top: 2px; }
        .sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #ffc107; color: #333; padding: 5px 4px; text-align: left; font-size: 9px; }
        td { padding: 3px 4px; border-bottom: 1px solid #eee; }
        tfoot td { background: #f8f9fa; font-weight: bold; }
        .right { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #aaa; text-align: right; }
    </style>
</head>
<body>
    @include('laporan.partials.company-header')
    <p class="sub">Rekap Data Kasbon Karyawan</p>
    <table>
        <thead>
            <tr>
                <th>#</th><th>No. Kasbon</th><th>Karyawan</th><th>Tgl.</th>
                <th class="right">Jumlah</th><th class="right">Sisa</th><th>Keperluan</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->no_kasbon }}</td>
                <td>{{ $d->karyawan->nama }}</td>
                <td>{{ $d->tanggal_kasbon->format('d/m/Y') }}</td>
                <td class="right">{{ number_format($d->jumlah_kasbon, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->sisa_kasbon, 0, ',', '.') }}</td>
                <td>{{ Str::limit($d->keperluan, 25) }}</td>
                <td>{{ ucfirst($d->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total ({{ $data->count() }})</td>
                <td class="right">{{ number_format($data->sum('jumlah_kasbon'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('sisa_kasbon'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    <p class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
