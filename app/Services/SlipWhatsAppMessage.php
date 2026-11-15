<?php

namespace App\Services;

use App\Models\Potongan;

class SlipWhatsAppMessage
{
    public function build(Potongan $potongan): string
    {
        $karyawan = $potongan->karyawan;
        $lines = [
            "Yth. {$karyawan->nama}, berikut rincian potongan periode {$potongan->periode->nama}:",
            '',
            '*RINCIAN POTONGAN*',
            'Potongan pinjaman: Rp ' . $this->rupiah($potongan->potongan_pinjaman),
            'Potongan kasbon: Rp ' . $this->rupiah($potongan->potongan_kasbon),
            'Simpanan wajib: Rp ' . $this->rupiah($potongan->potongan_simpanan_wajib),
            'Simpanan sukarela: Rp ' . $this->rupiah($potongan->potongan_simpanan_sukarela),
        ];

        if ($potongan->potongan_lainnya > 0) {
            $lines[] = ($potongan->keterangan_lainnya ?: 'Potongan lainnya')
                . ': Rp ' . $this->rupiah($potongan->potongan_lainnya);
        }

        $lines[] = 'Total potongan: Rp ' . $this->rupiah($potongan->total_potongan);

        $pinjamans = $karyawan->pinjamans->whereIn('status', ['disetujui', 'lunas']);
        if ($pinjamans->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '*RINCIAN PINJAMAN*';

            foreach ($pinjamans as $pinjaman) {
                $pembayarans = $pinjaman->pembayarans->sortBy('pembayaran_ke');
                $lines[] = '';
                $lines[] = "No. pinjaman: {$pinjaman->no_pinjaman}";
                $lines[] = 'Pembayaran: ' . $pembayarans->count() . " dari {$pinjaman->tenor} kali";

                foreach ($pembayarans as $pembayaran) {
                    $tanggal = $pembayaran->tanggal_bayar?->format('d/m/Y') ?? '-';
                    $lines[] = "  Cicilan ke-{$pembayaran->pembayaran_ke} ({$tanggal}): Rp "
                        . $this->rupiah($pembayaran->jumlah_bayar);
                }

                $lines[] = 'Total sudah dibayar: Rp ' . $this->rupiah($pembayarans->sum('jumlah_bayar'));
                $lines[] = 'Sisa pinjaman: Rp ' . $this->rupiah($pinjaman->sisa_pinjaman);
            }
        }

        $kasbonsBelumLunas = $karyawan->kasbons
            ->filter(fn($kasbon) => $kasbon->status === 'disetujui' && $kasbon->sisa_kasbon > 0);

        if ($kasbonsBelumLunas->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '*KASBON BELUM LUNAS*';

            foreach ($kasbonsBelumLunas as $kasbon) {
                $lines[] = "{$kasbon->no_kasbon}: sisa Rp " . $this->rupiah($kasbon->sisa_kasbon)
                    . ' dari Rp ' . $this->rupiah($kasbon->jumlah_kasbon);
            }
        }

        $simpanan = $karyawan->simpanan;
        $lines[] = '';
        $lines[] = '*TOTAL SIMPANAN*';
        $lines[] = 'Simpanan pokok: Rp ' . $this->rupiah($simpanan?->simpanan_pokok ?? 0);
        $lines[] = 'Simpanan wajib: Rp ' . $this->rupiah($simpanan?->simpanan_wajib ?? 0);
        $lines[] = 'Simpanan sukarela: Rp ' . $this->rupiah($simpanan?->simpanan_sukarela ?? 0);
        $lines[] = 'Total uang simpanan: Rp ' . $this->rupiah($simpanan?->total_simpanan ?? 0);

        return implode("\n", $lines);
    }

    private function rupiah(float|int|string $amount): string
    {
        return number_format((float) $amount, 0, ',', '.');
    }
}
