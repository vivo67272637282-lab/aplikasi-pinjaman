@extends('layouts.app')

@section('title', 'Detail Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('simpanan.index') }}">Simpanan</a></li>
    <li class="breadcrumb-item active">{{ $simpanan->karyawan->nama }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Simpanan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('simpanan.edit', $simpanan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('simpanan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-piggy-bank me-2 text-success"></i>Saldo Simpanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">Karyawan</td>
                        <td class="fw-semibold">{{ $simpanan->karyawan->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nomor Induk Anggota</td>
                        <td>{{ $simpanan->karyawan->nik }}</td>
                    </tr>
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <tr>
                        <td class="text-muted">Jumlah Menabung</td>
                        <td>Rp {{ number_format($simpanan->simpanan_sukarela, 0, ',', '.') }}</td>
                    </tr>
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <tr>
                        <td class="fw-bold">Total Simpanan</td>
                        <td class="fw-bold text-success fs-5">Rp {{ number_format($simpanan->total_simpanan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Transaksi Terakhir</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $r)
                            <tr>
                                <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                                <td>
                                    @if($r->jenis === 'masuk')
                                        <span class="badge bg-success">Masuk</span>
                                    @else
                                        <span class="badge bg-danger">Keluar</span>
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
                                <td colspan="5" class="text-center text-muted py-3">Belum ada riwayat transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($riwayat->hasPages())
                <div class="p-3">{{ $riwayat->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
