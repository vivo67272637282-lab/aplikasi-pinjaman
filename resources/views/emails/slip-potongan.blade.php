<!DOCTYPE html>
<html lang="id">
<body>
    <p>Yth. {{ $potongan->karyawan->nama }},</p>
    <p>
        Bersama email ini kami lampirkan rincian potongan untuk periode
        <strong>{{ $potongan->periode->nama }}</strong>.
    </p>
    <p>
        Total potongan: <strong>Rp {{ number_format($potongan->total_potongan, 0, ',', '.') }}</strong>
    </p>
    <p>Silakan buka lampiran PDF untuk melihat rincian lengkap.</p>
    <p>Salam,<br>{{ config('app.name') }}</p>
</body>
</html>
