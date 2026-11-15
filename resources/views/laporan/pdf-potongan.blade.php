<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Potongan — {{ $periode->nama }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        .company-header { width: auto; margin: 0 auto 4px; border-collapse: collapse; }
        .company-logo { padding-right: 8px; vertical-align: middle; }
        .company-logo img { max-width: 64px; max-height: 48px; }
        .company-name { font-size: 13px; font-weight: bold; vertical-align: middle; }
        .company-address { font-size: 9px; color: #666; margin-top: 2px; }
        .sub-title { text-align: center; font-size: 10px; color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #dc3545; color: white; padding: 5px 4px; text-align: left; font-size: 9px; }
        td { padding: 4px; border-bottom: 1px solid #eee; }
        tfoot td { background: #f8f9fa; font-weight: bold; border-top: 2px solid #ccc; }
        .right { text-align: right; }
        .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>
    @include('laporan.partials.company-header')
    <p class="sub-title">Rekap Potongan — Periode: {{ $periode->nama }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th><th>NIK</th><th>Nama</th><th>Jabatan</th>
                <th class="right">Pot. Pinjaman</th>
                <th class="right">Pot. Kasbon</th><th class="right">Pot. Simpanan</th>
                <th class="right">Lainnya</th><th class="right">Total Pot.</th><th>Rincian Pembayaran Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->karyawan->nik }}</td>
                <td>{{ $d->karyawan->nama }}</td>
                <td>{{ $d->karyawan->jabatan ?: '-' }}</td>
                <td class="right">{{ number_format($d->potongan_pinjaman, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->potongan_kasbon, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->potongan_simpanan_wajib + $d->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($d->potongan_lainnya, 0, ',', '.') }}</td>
                <td class="right"><strong>{{ number_format($d->total_potongan, 0, ',', '.') }}</strong></td>
                <td>
                    @forelse($d->karyawan->pinjamans->whereIn('status', ['disetujui', 'lunas']) as $pinjaman)
                        <div>
                            {{ $pinjaman->no_pinjaman }}:
                            {{ $pinjaman->pembayarans->count() }} dari {{ $pinjaman->tenor }} pembayaran,
                            terbayar Rp {{ number_format($pinjaman->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }},
                            sisa Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                        </div>
                    @empty
                        -
                    @endforelse
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL ({{ $data->count() }} karyawan)</td>
                <td class="right">{{ number_format($data->sum('potongan_pinjaman'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('potongan_kasbon'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum(fn($d) => $d->potongan_simpanan_wajib + $d->potongan_simpanan_sukarela), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('potongan_lainnya'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data->sum('total_potongan'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <p class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
