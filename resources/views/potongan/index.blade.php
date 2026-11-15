@extends('layouts.app')

@section('title', 'Data Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Potongan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-scissors me-2 text-danger"></i>Data Potongan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('potongan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Periode</label>
                <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Periode --</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriode?->id == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Cari Karyawan</label>
                <input type="text" name="search" class="form-control form-select-sm"
                    value="{{ request('search') }}" placeholder="Nama atau nomor induk anggota...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('potongan.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>

            @if($selectedPeriode)
            <div class="col-auto ms-auto">
                <form action="{{ route('potongan.generate') }}" method="POST"
                    onsubmit="return confirm('Generate potongan otomatis untuk periode {{ $selectedPeriode->nama }}?')">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $selectedPeriode->id }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-magic me-1"></i> Generate Otomatis
                    </button>
                </form>
            </div>
            @endif
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
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Pot. Pinjaman</th>
                        <th>Pot. Kasbon</th>
                        <th>Pot. Simpanan</th>
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
                        <td>{{ $p->periode->nama }}</td>
                        <td>Rp {{ number_format($p->potongan_pinjaman, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->potongan_kasbon, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->potongan_simpanan_wajib + $p->potongan_simpanan_sukarela, 0, ',', '.') }}</td>
                        <td class="text-danger fw-semibold">Rp {{ number_format($p->total_potongan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('potongan.show', $p) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('potongan.edit', $p) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <a href="{{ route('slip.show', $p) }}" class="btn btn-outline-secondary" title="Slip"><i class="bi bi-file-text"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data potongan
                            @if($selectedPeriode)
                                untuk periode <strong>{{ $selectedPeriode->nama }}</strong>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $potongans->total() }} data</small>
            {{ $potongans->links() }}
        </div>
    </div>
</div>
@endsection
