<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Simpanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .company-header { width: auto; margin: 0 auto 4px; border-collapse: collapse; }
        .company-logo { padding-right: 8px; vertical-align: middle; }
        .company-logo img { max-width: 64px; max-height: 48px; }
        .company-name { font-size: 13px; font-weight: bold; vertical-align: middle; }
        .company-address { font-size: 9px; color: #666; margin-top: 2px; }
        .sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #198754; color: white; padding: 5px 4px; text-align: left; font-size: 9px; }
        td { padding: 3px 4px; border-bottom: 1px solid #eee; }
        tfoot td { background: #f8f9fa; font-weight: bold; }
        .right { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #aaa; text-align: right; }
    </style>
</head>
<body>
    @include('laporan.partials.company-header')
    <p class="sub">Rekap Simpanan Karyawan</p>
    <table>
        <thead>
            <tr>
                <th>#</th><th>NIK</th><th>Nama</th><th>Jabatan</th>
                <th class="right">Pokok</th><th class="right">Wajib</th>
                <th class="right">Sukarela</th><th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $s)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $s->karyawan->nik }}</td>
                <td>{{ $s->karyawan->nama }}</td>
                <td>{{ $s->karyawan->jabatan ?: '-' }}</td>
                <td class="right">{{ number_format($s->simpanan_pokok, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($s->simpanan_wajib, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($s->simpanan_sukarela, 0, ',', '.') }}</td>
                <td class="right"><strong>{{ number_format($s->total_simpanan, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total ({{ $data->count() }} karyawan)</td>
                <td class="right">{{ number_format($data->sum('simpanan_pokok'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('simpanan_wajib'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('simpanan_sukarela'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalSimpanan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    <p class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
