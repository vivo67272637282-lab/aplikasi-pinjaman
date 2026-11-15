@extends('layouts.app')

@section('title', 'Detail Pinjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
    <li class="breadcrumb-item active">{{ $pinjaman->no_pinjaman }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Pinjaman</h4>
    <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i>{{ $pinjaman->no_pinjaman }}</h6>
                @php $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$pinjaman->status] ?? 'secondary'; @endphp
                <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($pinjaman->status) }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" width="40%">Karyawan</td>
                        <td><a href="{{ route('karyawan.show', $pinjaman->karyawan) }}" class="fw-semibold">{{ $pinjaman->karyawan->nama }}</a>
                            <small class="text-muted ms-1">({{ $pinjaman->karyawan->nik }})</small></td></tr>
                    <tr><td class="text-muted">Jumlah Pinjaman</td>
                        <td class="fw-bold text-primary fs-5">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Bunga ({{ number_format($pinjaman->bunga_persen, 2, ',', '.') }}%)</td>
                        <td>Rp {{ number_format($pinjaman->jumlah_bunga, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Total Pengembalian</td>
                        <td class="fw-bold">Rp {{ number_format($pinjaman->total_pengembalian, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Tenor</td><td>{{ $pinjaman->tenor }} bulan</td></tr>
                    <tr><td class="text-muted">Cicilan/Bulan</td>
                        <td class="fw-semibold">Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Pembayaran</td>
                        <td class="fw-semibold">{{ min($pinjaman->jumlah_pembayaran, $pinjaman->tenor) }} dari {{ $pinjaman->tenor }} pembayaran</td></tr>
                    @if($pinjaman->pembayaran_berikutnya)
                    <tr><td class="text-muted">Pembayaran Berikutnya</td>
                        <td class="fw-semibold">Ke-{{ $pinjaman->pembayaran_berikutnya }} · Rp {{ number_format($pinjaman->jumlah_pembayaran_berikutnya, 0, ',', '.') }}</td></tr>
                    @endif
                    <tr><td class="text-muted">Sisa Pinjaman</td>
                        <td class="fw-bold {{ $pinjaman->sisa_pinjaman > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                        </td></tr>
                    <tr><td class="text-muted">Tanggal Pinjaman</td><td>{{ $pinjaman->tanggal_pinjaman->format('d F Y') }}</td></tr>
                    <tr><td class="text-muted">Keperluan</td><td>{{ $pinjaman->keperluan ?: '-' }}</td></tr>
                    <tr><td class="text-muted">Catatan</td><td>{{ $pinjaman->catatan ?: '-' }}</td></tr>
                    @if($pinjaman->disetujuiOleh)
                    <tr><td class="text-muted">Disetujui Oleh</td><td>{{ $pinjaman->disetujuiOleh?->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Tanggal Disetujui</td><td>{{ $pinjaman->tanggal_disetujui?->format('d F Y H:i') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Aksi -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Tindakan</h6>
            </div>
            <div class="card-body d-grid gap-2">
                @if($pinjaman->status === 'pending')
                    <form action="{{ route('pinjaman.setujui', $pinjaman) }}" method="POST"
                        onsubmit="return confirm('Setujui pinjaman ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Setujui Pinjaman
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak">
                        <i class="bi bi-x-circle me-1"></i> Tolak Pinjaman
                    </button>

                    <a href="{{ route('pinjaman.edit', $pinjaman) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>

                    <form action="{{ route('pinjaman.destroy', $pinjaman) }}" method="POST"
                        onsubmit="return confirm('Hapus pinjaman ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                @elseif($pinjaman->status === 'disetujui')
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i>Pinjaman sudah disetujui dan aktif.
                    </div>
                    <form action="{{ route('pinjaman.bayar', $pinjaman) }}" method="POST"
                        onsubmit="return confirm('Catat pembayaran ke-{{ $pinjaman->pembayaran_berikutnya }} sebesar Rp {{ number_format($pinjaman->jumlah_pembayaran_berikutnya, 0, ',', '.') }}?')">
                        @csrf
                        <div class="mb-2">
                            <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control"
                                value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cash-coin me-1"></i> Catat Pembayaran Ke-{{ $pinjaman->pembayaran_berikutnya }}
                        </button>
                    </form>
                @elseif($pinjaman->status === 'ditolak')
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-x-circle me-2"></i>Pinjaman ditolak.
                    </div>
                @elseif($pinjaman->status === 'lunas')
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-check2-all me-2"></i>Pinjaman sudah lunas.
                    </div>
                @endif
            </div>
        </div>

        <!-- Progress Pelunasan -->
        @if($pinjaman->status === 'disetujui')
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Progress Pelunasan</small>
                    <small class="fw-semibold">
                        {{ number_format((($pinjaman->total_pengembalian - $pinjaman->sisa_pinjaman) / $pinjaman->total_pengembalian) * 100, 1) }}%
                    </small>
                </div>
                @php $pct = min(100, (($pinjaman->total_pengembalian - $pinjaman->sisa_pinjaman) / $pinjaman->total_pengembalian) * 100); @endphp
                <div class="progress" style="height:10px">
                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                </div>
                <div class="row mt-2 text-center">
                    <div class="col-6">
                        <div class="text-muted small">Terbayar</div>
                        <div class="fw-bold text-success">Rp {{ number_format($pinjaman->total_pengembalian - $pinjaman->sisa_pinjaman, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Sisa</div>
                        <div class="fw-bold text-danger">Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if($pinjaman->pembayarans->isNotEmpty())
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pembayaran Ke</th>
                    <th>Tanggal</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pinjaman->pembayarans as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->pembayaran_ke }} dari {{ $pinjaman->tenor }}</td>
                    <td>{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</td>
                    <td class="text-end">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Modal Tolak -->
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pinjaman.tolak', $pinjaman) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin menolak pinjaman <strong>{{ $pinjaman->no_pinjaman }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pinjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
