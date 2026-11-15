@extends('layouts.app')

@section('title', 'Data Kasbon')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kasbon</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-warning"></i>Data Kasbon</h4>
    <a href="{{ route('kasbon.create') }}" class="btn btn-warning">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Kasbon
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari no. kasbon, nama, Nomor Induk Anggota..."
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
                <a href="{{ route('kasbon.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>No. Kasbon</th>
                        <th>Karyawan</th>
                        <th>Jumlah</th>
                        <th>Sisa</th>
                        <th>Tgl. Kasbon</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kasbons as $k)
                    <tr>
                        <td>{{ $kasbons->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('kasbon.show', $k) }}" class="text-decoration-none">{{ $k->no_kasbon }}</a>
                        </td>
                        <td>
                            <div>{{ $k->karyawan->nama }}</div>
                            <small class="text-muted">{{ $k->karyawan->nik }}</small>
                        </td>
                        <td>Rp {{ number_format($k->jumlah_kasbon, 0, ',', '.') }}</td>
                        <td class="{{ $k->sisa_kasbon > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                            Rp {{ number_format($k->sisa_kasbon, 0, ',', '.') }}
                        </td>
                        <td>{{ $k->tanggal_kasbon->format('d/m/Y') }}</td>
                        <td><span class="text-truncate d-inline-block" style="max-width:120px" title="{{ $k->keperluan }}">{{ $k->keperluan ?: '-' }}</span></td>
                        <td>
                            @php $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$k->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($k->status) }}</span>
                        </td>
                        <td>
                            @if($k->status === 'lunas' || $k->sisa_kasbon <= 0)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                            @elseif($k->status === 'disetujui')
                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock-history me-1"></i>Belum Lunas</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kasbon.show', $k) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($k->status === 'disetujui' && $k->sisa_kasbon > 0)
                                <form action="{{ route('kasbon.lunas', $k) }}" method="POST"
                                    onsubmit="return confirm('Tandai kasbon ini sudah lunas dibayar?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Tandai lunas">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                </form>
                                @endif
                                @if($k->status === 'pending')
                                <a href="{{ route('kasbon.edit', $k) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kasbon.destroy', $k) }}" method="POST"
                                    onsubmit="return confirm('Hapus kasbon ini?')">
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
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data kasbon
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $kasbons->firstItem() }} - {{ $kasbons->lastItem() }} dari {{ $kasbons->total() }} data</small>
            {{ $kasbons->links() }}
        </div>
    </div>
</div>
@endsection
