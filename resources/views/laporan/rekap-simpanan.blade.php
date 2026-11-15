@extends('layouts.app')

@section('title', 'Rekap Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Rekap Simpanan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Rekap Simpanan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.pdf-simpanan') }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center py-3">
            <div class="text-muted small">Total Simpanan Pokok</div>
            <div class="fw-bold fs-5">Rp {{ number_format($data->sum('simpanan_pokok'), 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center py-3">
            <div class="text-muted small">Total Simpanan Wajib</div>
            <div class="fw-bold fs-5">Rp {{ number_format($data->sum('simpanan_wajib'), 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-success border-2 text-center py-3">
            <div class="text-muted small">Total Keseluruhan</div>
            <div class="fw-bold fs-4 text-success">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th><th>NIK</th><th>Nama</th><th>Jabatan</th>
                        <th>Pokok</th><th>Wajib</th><th>Sukarela</th><th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->karyawan->nik }}</td>
                        <td class="fw-semibold">{{ $s->karyawan->nama }}</td>
                        <td>{{ $s->karyawan->jabatan ?: '-' }}</td>
                        <td>Rp {{ number_format($s->simpanan_pokok, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($s->simpanan_wajib, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($s->simpanan_sukarela, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($s->total_simpanan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">Total ({{ $data->count() }} karyawan)</td>
                        <td>Rp {{ number_format($data->sum('simpanan_pokok'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data->sum('simpanan_wajib'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data->sum('simpanan_sukarela'), 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
