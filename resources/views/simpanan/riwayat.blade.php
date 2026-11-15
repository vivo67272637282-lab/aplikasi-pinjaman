@extends('layouts.app')

@section('title', 'Riwayat Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('simpanan.index') }}">Simpanan</a></li>
    <li class="breadcrumb-item active">Riwayat</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Simpanan</h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-4">
                <select name="karyawan_id" class="form-select">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nik }} — {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="periode_id" class="form-select">
                    <option value="">-- Semua Periode --</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('simpanan.riwayat') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td>{{ $riwayat->firstItem() + $loop->index }}</td>
                        <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $r->karyawan->nama }}</div>
                            <small class="text-muted">{{ $r->karyawan->nik }}</small>
                        </td>
                        <td>{{ $r->periode?->nama ?: '-' }}</td>
                        <td>
                            @if($r->jenis === 'masuk')
                                <span class="badge bg-success"><i class="bi bi-arrow-down-circle me-1"></i>Masuk</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-arrow-up-circle me-1"></i>Keluar</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($r->kategori) }}</td>
                        <td class="{{ $r->jenis === 'masuk' ? 'text-success' : 'text-danger' }} fw-semibold">
                            {{ $r->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                        </td>
                        <td>{{ $r->keterangan ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada riwayat transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $riwayat->links() }}</div>
    </div>
</div>
@endsection
