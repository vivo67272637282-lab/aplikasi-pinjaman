@extends('layouts.app')

@section('title', 'Rekap Kasbon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Rekap Kasbon</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Rekap Kasbon</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.pdf-kasbon', request()->all()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak"   {{ request('status') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                    <option value="lunas"     {{ request('status') === 'lunas'     ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                <a href="{{ route('laporan.rekap-kasbon') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>#</th><th>No. Kasbon</th><th>Karyawan</th><th>Tgl. Kasbon</th>
                        <th>Jumlah</th><th>Sisa</th><th>Keperluan</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><a href="{{ route('kasbon.show', $d) }}">{{ $d->no_kasbon }}</a></td>
                        <td>{{ $d->karyawan->nama }}<br><small class="text-muted">{{ $d->karyawan->nik }}</small></td>
                        <td>{{ $d->tanggal_kasbon->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($d->jumlah_kasbon, 0, ',', '.') }}</td>
                        <td class="{{ $d->sisa_kasbon > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($d->sisa_kasbon, 0, ',', '.') }}</td>
                        <td>{{ Str::limit($d->keperluan, 30) }}</td>
                        <td><span class="badge bg-{{ ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$d->status] ?? 'secondary' }}">{{ ucfirst($d->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">Total ({{ $data->count() }} kasbon)</td>
                        <td>Rp {{ number_format($data->sum('jumlah_kasbon'), 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($data->sum('sisa_kasbon'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
