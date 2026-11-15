@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
    <li class="breadcrumb-item active">{{ $karyawan->nama ?? '-' }}</li>
@endsection

@section('content')
@if(!$karyawan)
    <div class="alert alert-warning">Data karyawan tidak ditemukan.</div>
@else
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Karyawan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Karyawan -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Informasi Karyawan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" width="40%">Nomor Induk Anggota</td><td class="fw-semibold">{{ $karyawan->nik }}</td></tr>
                    <tr><td class="text-muted">Nama</td><td class="fw-semibold">{{ $karyawan->nama }}</td></tr>
                    <tr><td class="text-muted">Jabatan</td><td>{{ $karyawan->jabatan ?: '-' }}</td></tr>
                    <tr><td class="text-muted">Jenis Kelamin</td><td>{{ $karyawan->jenis_kelamin === 'L' ? 'Laki-laki' : ($karyawan->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td></tr>
                    <tr><td class="text-muted">Alamat Email</td><td>{{ $karyawan->email ?: '-' }}</td></tr>
                    <tr><td class="text-muted">No. WA</td><td>{{ $karyawan->no_wa ?: '-' }}</td></tr>
                    <tr><td class="text-muted">Tgl. Masuk</td><td>{{ $karyawan->tanggal_masuk?->format('d/m/Y') ?: '-' }}</td></tr>
                    <tr><td class="text-muted">Alamat</td><td>{{ $karyawan->alamat ?: '-' }}</td></tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($karyawan->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Simpanan -->
    <div class="col-md-7">
        @if($karyawan->simpanan)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-piggy-bank me-2 text-success"></i>Simpanan</h6>
                <a href="{{ route('simpanan.show', $karyawan->simpanan) }}" class="btn btn-sm btn-outline-success">Detail</a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="text-muted small">Pokok</div>
                        <div class="fw-bold">Rp {{ number_format($karyawan->simpanan->simpanan_pokok, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Wajib</div>
                        <div class="fw-bold">Rp {{ number_format($karyawan->simpanan->simpanan_wajib, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Sukarela</div>
                        <div class="fw-bold">Rp {{ number_format($karyawan->simpanan->simpanan_sukarela, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Total</div>
                        <div class="fw-bold text-success">Rp {{ number_format($karyawan->simpanan->total_simpanan, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Pinjaman Terakhir -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2 text-danger"></i>Pinjaman</h6>
                <a href="{{ route('pinjaman.create', ['karyawan_id' => $karyawan->id]) }}" class="btn btn-sm btn-outline-danger">+ Ajukan</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>No.</th><th>Total</th><th>Bunga</th><th>Tenor</th><th>Sisa</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($karyawan->pinjamans as $p)
                        <tr>
                            <td><a href="{{ route('pinjaman.show', $p) }}">{{ $p->no_pinjaman }}</a></td>
                            <td>Rp {{ number_format($p->total_pengembalian, 0, ',', '.') }}</td>
                            <td>{{ number_format($p->bunga_persen, 2, ',', '.') }}%</td>
                            <td>{{ $p->tenor }} bln</td>
                            <td>Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td><span class="badge bg-{{ ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$p->status] ?? 'secondary' }}">{{ ucfirst($p->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-2">Belum ada pinjaman</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kasbon Terakhir -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-warning"></i>Kasbon</h6>
                <a href="{{ route('kasbon.create', ['karyawan_id' => $karyawan->id]) }}" class="btn btn-sm btn-outline-warning">+ Ajukan</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>No.</th><th>Jumlah</th><th>Sisa</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($karyawan->kasbons as $k)
                        <tr>
                            <td><a href="{{ route('kasbon.show', $k) }}">{{ $k->no_kasbon }}</a></td>
                            <td>Rp {{ number_format($k->jumlah_kasbon, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($k->sisa_kasbon, 0, ',', '.') }}</td>
                            <td><span class="badge bg-{{ ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$k->status] ?? 'secondary' }}">{{ ucfirst($k->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">Belum ada kasbon</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
