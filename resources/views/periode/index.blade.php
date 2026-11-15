@extends('layouts.app')

@section('title', 'Data Periode')

@section('breadcrumb')
    <li class="breadcrumb-item active">Periode</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Data Periode</h4>
    <a href="{{ route('periode.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Periode
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nama Periode</th>
                        <th>Bulan/Tahun</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Jumlah Potongan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodes as $p)
                    <tr>
                        <td>{{ $periodes->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('periode.show', $p) }}" class="text-decoration-none">{{ $p->nama }}</a>
                        </td>
                        <td>{{ $p->bulan }}/{{ $p->tahun }}</td>
                        <td>{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                        <td>{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>{{ $p->potongans->count() ?? 0 }} karyawan</td>
                        <td>
                            @php $badge = ['aktif'=>'success','selesai'=>'secondary','draft'=>'warning'][$p->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('periode.show', $p) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('periode.edit', $p) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('periode.destroy', $p) }}" method="POST"
                                    onsubmit="return confirm('Hapus periode {{ $p->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data periode
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $periodes->links() }}</div>
    </div>
</div>
@endsection
