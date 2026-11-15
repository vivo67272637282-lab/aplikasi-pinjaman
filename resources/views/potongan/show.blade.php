@extends('layouts.app')

@section('title', 'Detail Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('potongan.index') }}">Potongan</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Potongan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('slip.pdf', $potongan) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> PDF Slip
        </a>
        <a href="{{ route('potongan.edit', $potongan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('potongan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-scissors me-2 text-danger"></i>
                        {{ $potongan->karyawan->nama }} — {{ $potongan->periode->nama }}
                    </h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nomor Induk Anggota</div>
                        <div class="fw-semibold">{{ $potongan->karyawan->nik }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jabatan</div>
                        <div>{{ $potongan->karyawan->jabatan ?: '-' }}</div>
                    </div>
                </div>
                <hr>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">(-) Potongan Pinjaman</td>
                        <td class="text-end text-danger">Rp {{ number_format($potongan->potongan_pinjaman, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(-) Potongan Kasbon</td>
                        <td class="text-end text-danger">Rp {{ number_format($potongan->potongan_kasbon, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(-) Potongan Simpanan Wajib</td>
                        <td class="text-end text-danger">Rp {{ number_format($potongan->potongan_simpanan_wajib, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">(-) Potongan Simpanan Sukarela</td>
                        <td class="text-end text-danger">Rp {{ number_format($potongan->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
                    </tr>
                    @if($potongan->potongan_lainnya > 0)
                    <tr>
                        <td class="text-muted">(-) {{ $potongan->keterangan_lainnya ?: 'Potongan Lainnya' }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($potongan->potongan_lainnya, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <tr class="table-light">
                        <td class="fw-bold">Total Potongan</td>
                        <td class="text-end fw-bold text-danger fs-6">Rp {{ number_format($potongan->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                </table>

                @if($potongan->catatan)
                <div class="alert alert-light mt-3">
                    <i class="bi bi-info-circle me-1"></i> {{ $potongan->catatan }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
