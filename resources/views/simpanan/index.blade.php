@extends('layouts.app')

@section('title', 'Data Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Simpanan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-piggy-bank me-2 text-success"></i>Data Simpanan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('simpanan.riwayat') }}" class="btn btn-outline-success">
            <i class="bi bi-clock-history me-1"></i> Riwayat
        </a>
        <a href="{{ route('simpanan.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, nomor induk anggota..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Menabung</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simpanans as $s)
                    <tr>
                        <td>{{ $simpanans->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="fw-semibold">{{ $s->karyawan->nama }}</div>
                            <small class="text-muted">{{ $s->karyawan->nik }}</small>
                        </td>
                        <td>Rp {{ number_format($s->simpanan_sukarela, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($s->total_simpanan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('simpanan.show', $s) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('simpanan.edit', $s) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data simpanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($simpanans->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2">Total</td>
                        <td>Rp {{ number_format($simpanans->sum('simpanan_sukarela'), 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($simpanans->sum('total_simpanan'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $simpanans->firstItem() }} - {{ $simpanans->lastItem() }} dari {{ $simpanans->total() }} data</small>
            {{ $simpanans->links() }}
        </div>
    </div>
</div>
@endsection
