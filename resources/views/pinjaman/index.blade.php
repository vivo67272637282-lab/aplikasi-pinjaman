@extends('layouts.app')

@section('title', 'Data Pinjaman')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pinjaman</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Data Pinjaman</h4>
    <a href="{{ route('pinjaman.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Pinjaman
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari no. pinjaman, nama, nomor induk anggota..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak"   {{ request('status') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                    <option value="lunas"     {{ request('status') === 'lunas'     ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('pinjaman.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>No. Pinjaman</th>
                        <th>Karyawan</th>
                        <th>Pokok</th>
                        <th>Bunga</th>
                        <th>Total Pengembalian</th>
                        <th>Tenor</th>
                        <th>Cicilan/Bln</th>
                        <th>Pembayaran</th>
                        <th>Sisa</th>
                        <th>Tgl. Pinjaman</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjamans as $p)
                    <tr>
                        <td>{{ $pinjamans->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('pinjaman.show', $p) }}" class="text-decoration-none">{{ $p->no_pinjaman }}</a>
                        </td>
                        <td>
                            <div>{{ $p->karyawan->nama }}</div>
                            <small class="text-muted">{{ $p->karyawan->nik }}</small>
                        </td>
                        <td>Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td>{{ number_format($p->bunga_persen, 2, ',', '.') }}%</td>
                        <td class="fw-semibold">Rp {{ number_format($p->total_pengembalian, 0, ',', '.') }}</td>
                        <td>{{ $p->tenor }} bln</td>
                        <td>Rp {{ number_format($p->cicilan_per_bulan, 0, ',', '.') }}</td>
                        <td>{{ min($p->pembayarans_count, $p->tenor) }} / {{ $p->tenor }}</td>
                        <td class="{{ $p->sisa_pinjaman > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                            Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                        </td>
                        <td>{{ $p->tanggal_pinjaman->format('d/m/Y') }}</td>
                        <td>
                            @php $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$p->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pinjaman.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($p->status === 'pending')
                                <a href="{{ route('pinjaman.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('pinjaman.destroy', $p) }}" method="POST"
                                    onsubmit="return confirm('Hapus pinjaman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data pinjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $pinjamans->firstItem() }} - {{ $pinjamans->lastItem() }} dari {{ $pinjamans->total() }} data</small>
            {{ $pinjamans->links() }}
        </div>
    </div>
</div>
@endsection
