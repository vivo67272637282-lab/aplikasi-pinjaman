@extends('layouts.app')

@section('title', 'Rekap Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Rekap Potongan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Rekap Potongan — {{ $periode->nama }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.pdf-potongan', ['periode_id' => $periode->id]) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" action="{{ route('laporan.rekap-potongan') }}" method="GET">
            <div class="col-md-4">
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $periode->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-danger">
                    <tr>
                        <th>#</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Pot. Pinjaman</th>
                        <th>Pot. Kasbon</th>
                        <th>Pot. Simpanan</th>
                        <th>Pot. Lain</th>
                        <th>Total Potongan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $d->karyawan->nik }}</td>
                        <td class="fw-semibold">{{ $d->karyawan->nama }}</td>
                        <td>{{ $d->karyawan->jabatan ?: '-' }}</td>
                        <td>Rp {{ number_format($d->potongan_pinjaman, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->potongan_kasbon, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->potongan_simpanan_wajib + $d->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->potongan_lainnya, 0, ',', '.') }}</td>
                        <td class="text-danger fw-semibold">Rp {{ number_format($d->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">TOTAL ({{ $data->count() }} karyawan)</td>
                        <td>Rp {{ number_format($data->sum('potongan_pinjaman'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data->sum('potongan_kasbon'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data->sum(fn($d) => $d->potongan_simpanan_wajib + $d->potongan_simpanan_sukarela), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data->sum('potongan_lainnya'), 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($data->sum('total_potongan'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
