@extends('layouts.app')

@section('title', 'Rincian Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('slip.index') }}">Rincian Potongan</a></li>
    <li class="breadcrumb-item active">{{ $potongan->karyawan->nama }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Rincian Potongan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('slip.pdf', $potongan) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('slip.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow border-0" id="slipCard">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ config('app.name') }}</h5>
                        <small>Rincian Potongan Karyawan</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">{{ $potongan->periode->nama }}</div>
                        <small>{{ $potongan->periode->tanggal_mulai->format('d/m/Y') }} – {{ $potongan->periode->tanggal_selesai->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless table-sm mb-3">
                    <tr>
                        <td width="30%" class="text-muted">Nama</td>
                        <td>: <strong>{{ $potongan->karyawan->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK</td>
                        <td>: {{ $potongan->karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>: {{ $potongan->karyawan->jabatan ?: '-' }}</td>
                    </tr>
                </table>

                <hr>

                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr><td colspan="2"><small class="text-muted fw-semibold">POTONGAN</small></td></tr>
                        <tr>
                            <td class="ps-3 text-muted">Potongan Pinjaman</td>
                            <td class="text-end text-danger">- Rp {{ number_format($potongan->potongan_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Potongan Kasbon</td>
                            <td class="text-end text-danger">- Rp {{ number_format($potongan->potongan_kasbon, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Simpanan Wajib</td>
                            <td class="text-end text-danger">- Rp {{ number_format($potongan->potongan_simpanan_wajib, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Simpanan Sukarela</td>
                            <td class="text-end text-danger">- Rp {{ number_format($potongan->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
                        </tr>
                        @if($potongan->potongan_lainnya > 0)
                        <tr>
                            <td class="ps-3 text-muted">{{ $potongan->keterangan_lainnya ?: 'Lainnya' }}</td>
                            <td class="text-end text-danger">- Rp {{ number_format($potongan->potongan_lainnya, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr class="table-light">
                            <td class="fw-semibold">Total Potongan</td>
                            <td class="text-end fw-bold text-danger">- Rp {{ number_format($potongan->total_potongan, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($potongan->catatan)
                <div class="alert alert-light border mt-3 mb-0 py-2">
                    <small><i class="bi bi-info-circle me-1"></i>{{ $potongan->catatan }}</small>
                </div>
                @endif
            </div>
            <div class="card-footer text-center text-muted small py-2">
                Dicetak pada {{ now()->format('d/m/Y H:i') }} — {{ config('app.name') }}
            </div>
        </div>
    </div>
</div>
@endsection
