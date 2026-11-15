@extends('layouts.app')

@section('title', 'Detail Periode')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('periode.index') }}">Periode</a></li>
    <li class="breadcrumb-item active">{{ $periode->nama }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Periode: {{ $periode->nama }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.pdf-potongan', ['periode_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> PDF Rekap
        </a>
        <a href="{{ route('periode.edit', $periode) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('periode.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted">Nama</td><td class="fw-semibold">{{ $periode->nama }}</td></tr>
                    <tr><td class="text-muted">Bulan/Tahun</td><td>{{ $periode->bulan }}/{{ $periode->tahun }}</td></tr>
                    <tr><td class="text-muted">Tanggal Mulai</td><td>{{ $periode->tanggal_mulai->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted">Tanggal Selesai</td><td>{{ $periode->tanggal_selesai->format('d/m/Y') }}</td></tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php $badge = ['aktif'=>'success','selesai'=>'secondary','draft'=>'warning'][$periode->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($periode->status) }}</span>
                        </td>
                    </tr>
                    <tr><td class="text-muted">Keterangan</td><td>{{ $periode->keterangan ?: '-' }}</td></tr>
                </table>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <div class="text-muted small">Total Potongan</div>
                        <div class="fw-bold text-danger">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Daftar Potongan ({{ $periode->potongans->count() }} karyawan)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Karyawan</th>
                                <th>Total Potongan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periode->potongans as $p)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $p->karyawan->nama }}</div>
                                    <small class="text-muted">{{ $p->karyawan->nik }}</small>
                                </td>
                                <td class="text-danger">Rp {{ number_format($p->total_potongan, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('slip.pdf', $p) }}" class="btn btn-xs btn-outline-danger" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data potongan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
