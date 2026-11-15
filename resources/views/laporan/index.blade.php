@extends('layouts.app')

@section('title', 'Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Laporan</h4>

<div class="row g-4">
    <!-- Rekap Potongan -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-scissors fs-2 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rekap Potongan</h6>
                        <small class="text-muted">Per periode potongan</small>
                    </div>
                </div>
                <form action="{{ route('laporan.rekap-potongan') }}" method="GET" class="d-flex gap-2">
                    <select name="periode_id" class="form-select flex-grow-1">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-eye"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Rekap Pinjaman -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-credit-card fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rekap Pinjaman</h6>
                        <small class="text-muted">Semua data pinjaman karyawan</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.rekap-pinjaman') }}" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Lihat Rekap
                    </a>
                    <a href="{{ route('laporan.pdf-pinjaman') }}" class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Kasbon -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-cash-stack fs-2 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rekap Kasbon</h6>
                        <small class="text-muted">Semua data kasbon karyawan</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.rekap-kasbon') }}" class="btn btn-outline-warning flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Lihat Rekap
                    </a>
                    <a href="{{ route('laporan.pdf-kasbon') }}" class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Simpanan -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-piggy-bank fs-2 text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rekap Simpanan</h6>
                        <small class="text-muted">Total simpanan seluruh karyawan</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.rekap-simpanan') }}" class="btn btn-outline-success flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Lihat Rekap
                    </a>
                    <a href="{{ route('laporan.pdf-simpanan') }}" class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
