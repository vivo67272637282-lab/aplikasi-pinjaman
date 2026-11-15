@extends('layouts.app')

@section('title', 'Rincian Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Rincian Potongan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Rincian Potongan</h4>
    @if($selectedPeriode)
    <a href="{{ route('slip.print-all', ['periode_id' => $selectedPeriode->id]) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-printer me-1"></i> Cetak Semua PDF
    </a>
    @endif
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Pilih Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriode?->id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Cari Karyawan</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama atau nomor induk anggota...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th>Total Potongan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($potongans as $p)
                    <tr>
                        <td>{{ $potongans->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->karyawan->nama }}</div>
                            <small class="text-muted">{{ $p->karyawan->nik }}</small>
                        </td>
                        <td>{{ $p->karyawan->jabatan ?: '-' }}</td>
                        <td class="text-danger">Rp {{ number_format($p->total_potongan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('slip.show', $p) }}" class="btn btn-outline-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('slip.pdf', $p) }}" class="btn btn-outline-danger" title="Download PDF" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <form action="{{ route('whatsapp.send-slip') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="karyawan_id" value="{{ $p->karyawan_id }}">
                                    <input type="hidden" name="periode_id" value="{{ $selectedPeriode?->id ?? $p->periode_id }}">
                                    <input type="hidden" name="channel" value="wa">
                                    <button type="submit" class="btn btn-outline-success" title="Kirim WA">
                                        <i class="bi bi-whatsapp"></i>
                                    </button>
                                </form>
                                <form action="{{ route('whatsapp.send-slip') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="karyawan_id" value="{{ $p->karyawan_id }}">
                                    <input type="hidden" name="periode_id" value="{{ $selectedPeriode?->id ?? $p->periode_id }}">
                                    <input type="hidden" name="channel" value="email">
                                    <button type="submit" class="btn btn-outline-primary" title="Kirim Email">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            @if(!$selectedPeriode)
                                Pilih periode terlebih dahulu
                            @else
                                Belum ada data slip untuk periode ini
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $potongans->links() }}</div>
    </div>
</div>
@endsection
