@extends('layouts.app')

@section('title', 'Log WhatsApp')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('whatsapp.index') }}">WhatsApp</a></li>
    <li class="breadcrumb-item active">Log</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Log Pengiriman WhatsApp</h4>
    <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kirim Pesan
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="terkirim" {{ request('status') === 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="gagal"    {{ request('status') === 'gagal'    ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-select-sm"
                    value="{{ request('search') }}" placeholder="Cari nama atau no. WA...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('whatsapp.logs') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Karyawan</th>
                        <th>No. Tujuan</th>
                        <th>Tipe</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Dikirim Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $loop->index }}</td>
                        <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                        <td>{{ $log->karyawan?->nama ?: '-' }}</td>
                        <td>{{ $log->no_tujuan }}</td>
                        <td><span class="badge bg-light text-dark">{{ $log->tipe ?: '-' }}</span></td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width:180px" title="{{ $log->pesan }}">
                                {{ $log->pesan }}
                            </span>
                        </td>
                        <td>
                            @php $badge = ['pending'=>'warning','terkirim'=>'success','gagal'=>'danger'][$log->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($log->status) }}</span>
                        </td>
                        <td>{{ $log->user?->name ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada log pengiriman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
