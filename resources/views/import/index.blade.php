@extends('layouts.app')

@section('title', 'Import Data')

@section('breadcrumb')
    <li class="breadcrumb-item active">Import Data</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-upload me-2 text-primary"></i>Import Data</h4>
    <a href="{{ route('import.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Import Baru
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nama File</th>
                        <th>Tipe</th>
                        <th>Total Baris</th>
                        <th>Berhasil</th>
                        <th>Gagal</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $log->file_name }}</td>
                        <td><span class="badge bg-light text-dark">{{ ucfirst($log->tipe) }}</span></td>
                        <td>{{ $log->total_baris }}</td>
                        <td class="text-success fw-semibold">{{ $log->berhasil }}</td>
                        <td class="{{ $log->gagal > 0 ? 'text-danger fw-semibold' : '' }}">{{ $log->gagal }}</td>
                        <td>
                            @php $badge = ['proses'=>'warning','selesai'=>'success','gagal'=>'danger'][$log->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($log->status) }}</span>
                        </td>
                        <td>{{ $log->user?->name ?: '-' }}</td>
                        <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                        <td>
                            <form action="{{ route('import.destroy', $log) }}" method="POST"
                                onsubmit="return confirm('Hapus log ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @if($log->pesan_error)
                    <tr class="table-danger">
                        <td colspan="10" class="ps-4">
                            <small><i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>Error:</strong> {{ Str::limit($log->pesan_error, 200) }}
                            </small>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada riwayat import
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
