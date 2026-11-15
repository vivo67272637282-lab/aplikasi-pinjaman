@extends('layouts.app')

@section('title', 'Detail Kasbon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kasbon.index') }}">Kasbon</a></li>
    <li class="breadcrumb-item active">{{ $kasbon->no_kasbon }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Kasbon</h4>
    <a href="{{ route('kasbon.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-warning"></i>{{ $kasbon->no_kasbon }}</h6>
                @php $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$kasbon->status] ?? 'secondary'; @endphp
                <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($kasbon->status) }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" width="40%">Karyawan</td>
                        <td><a href="{{ route('karyawan.show', $kasbon->karyawan) }}" class="fw-semibold">{{ $kasbon->karyawan->nama }}</a>
                            <small class="text-muted ms-1">({{ $kasbon->karyawan->nik }})</small></td></tr>
                    <tr><td class="text-muted">Jumlah Kasbon</td>
                        <td class="fw-bold text-warning fs-5">Rp {{ number_format($kasbon->jumlah_kasbon, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Sisa Kasbon</td>
                        <td class="fw-bold {{ $kasbon->sisa_kasbon > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($kasbon->sisa_kasbon, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Status Pembayaran</td>
                        <td>
                            @if($kasbon->status === 'lunas' || $kasbon->sisa_kasbon <= 0)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                            @elseif($kasbon->status === 'disetujui')
                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock-history me-1"></i>Belum Lunas</span>
                            @else
                                <span class="text-muted">Belum aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="text-muted">Tanggal Kasbon</td><td>{{ $kasbon->tanggal_kasbon->format('d F Y') }}</td></tr>
                    <tr><td class="text-muted">Keperluan</td><td>{{ $kasbon->keperluan ?: '-' }}</td></tr>
                    <tr><td class="text-muted">Catatan</td><td>{{ $kasbon->catatan ?: '-' }}</td></tr>
                    @if($kasbon->disetujuiOleh)
                    <tr><td class="text-muted">Disetujui Oleh</td><td>{{ $kasbon->disetujuiOleh?->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Tanggal Disetujui</td><td>{{ $kasbon->tanggal_disetujui?->format('d F Y H:i') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Tindakan</h6>
            </div>
            <div class="card-body d-grid gap-2">
                @if($kasbon->status === 'pending')
                    <form action="{{ route('kasbon.setujui', $kasbon) }}" method="POST"
                        onsubmit="return confirm('Setujui kasbon ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Setujui Kasbon
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak">
                        <i class="bi bi-x-circle me-1"></i> Tolak Kasbon
                    </button>

                    <a href="{{ route('kasbon.edit', $kasbon) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>

                    <form action="{{ route('kasbon.destroy', $kasbon) }}" method="POST"
                        onsubmit="return confirm('Hapus kasbon ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                @elseif($kasbon->status === 'disetujui')
                    @if($kasbon->sisa_kasbon > 0)
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-clock-history me-2"></i>Kasbon disetujui dan belum lunas.
                        </div>
                        <form action="{{ route('kasbon.lunas', $kasbon) }}" method="POST"
                            onsubmit="return confirm('Tandai kasbon ini sudah lunas dibayar?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-all me-1"></i> Tandai Sudah Lunas
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>Kasbon sudah lunas.
                        </div>
                    @endif
                @elseif($kasbon->status === 'ditolak')
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-x-circle me-2"></i>Kasbon ditolak.
                    </div>
                @elseif($kasbon->status === 'lunas')
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-check2-all me-2"></i>Kasbon sudah lunas.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kasbon.tolak', $kasbon) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Kasbon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin menolak kasbon <strong>{{ $kasbon->no_kasbon }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Kasbon</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
