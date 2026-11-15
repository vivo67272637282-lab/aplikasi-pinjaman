@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <section class="dashboard-hero mb-4">
        <div class="dashboard-hero-copy">
            <span class="dashboard-eyebrow"><i class="bi bi-stars me-1"></i> Ringkasan keuangan</span>
            <h1>Selamat datang, {{ auth()->user()?->name ?? '-' }}</h1>
            <p>Pantau karyawan, pinjaman, kasbon, dan simpanan Anda dalam satu tempat.</p>
            <div class="dashboard-quick-actions">
                <a href="{{ route('karyawan.create') }}" class="btn dashboard-action-primary">
                    <i class="bi bi-person-plus me-2"></i>Tambah Karyawan
                </a>
                <a href="{{ route('pinjaman.create') }}" class="btn dashboard-action-secondary">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Pinjaman
                </a>
            </div>
        </div>
        <div class="dashboard-hero-meta">
            <div class="dashboard-date">
                <span>Hari ini</span>
                <strong>{{ now()->format('d/m/Y') }}</strong>
            </div>
            <div class="dashboard-period {{ $periodeAktif ? 'is-active' : '' }}">
                <span class="period-indicator"></span>
                <div>
                    <small>Periode penggajian</small>
                    <strong>{{ $periodeAktif?->nama ?? 'Belum ada periode aktif' }}</strong>
                </div>
                <i class="bi bi-calendar2-week ms-auto"></i>
            </div>
        </div>
        <span class="dashboard-hero-decoration dashboard-hero-decoration-one"></span>
        <span class="dashboard-hero-decoration dashboard-hero-decoration-two"></span>
    </section>

    <section class="dashboard-stats row g-3 mb-4" aria-label="Ringkasan data">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card stat-employees h-100">
                <div class="stat-card-top">
                    <span class="stat-icon">
                    <i class="bi bi-people"></i>
                    </span>
                    <span class="stat-trend"><i class="bi bi-person-check"></i> Aktif</span>
                </div>
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-value">{{ number_format($totalKaryawan) }}</div>
                <a href="{{ route('karyawan.index') }}" class="stat-link">Lihat data karyawan <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card stat-loans h-100">
                <div class="stat-card-top">
                    <span class="stat-icon">
                    <i class="bi bi-credit-card"></i>
                    </span>
                    <span class="stat-trend"><i class="bi bi-activity"></i> Berjalan</span>
                </div>
                <div class="stat-label">Sisa Pinjaman</div>
                <div class="stat-value">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</div>
                <a href="{{ route('pinjaman.index') }}" class="stat-link">Pantau pinjaman <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card stat-cash-advance h-100">
                <div class="stat-card-top">
                    <span class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                    </span>
                    <span class="stat-trend"><i class="bi bi-receipt"></i> Berjalan</span>
                </div>
                <div class="stat-label">Sisa Kasbon</div>
                <div class="stat-value">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</div>
                <a href="{{ route('kasbon.index') }}" class="stat-link">Pantau kasbon <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card stat-savings h-100">
                <div class="stat-card-top">
                    <span class="stat-icon">
                    <i class="bi bi-piggy-bank"></i>
                    </span>
                    <span class="stat-trend"><i class="bi bi-shield-check"></i> Terkumpul</span>
                </div>
                <div class="stat-label">Total Simpanan</div>
                <div class="stat-value">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
                <a href="{{ route('simpanan.index') }}" class="stat-link">Lihat simpanan <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
    </section>

<!-- Pending Notifications -->
@if($pinjamanPending > 0 || $kasbonPending > 0)
<div class="row g-3 mb-4">
    @if($pinjamanPending > 0)
    <div class="col-md-6">
        <div class="alert modern-alert modern-alert-warning d-flex align-items-center mb-0">
            <div class="alert-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <strong>{{ $pinjamanPending }} Pinjaman</strong> menunggu persetujuan.
                <a href="{{ route('pinjaman.index', ['status' => 'pending']) }}" class="alert-link ms-1">Lihat →</a>
            </div>
        </div>
    </div>
    @endif
    @if($kasbonPending > 0)
    <div class="col-md-6">
        <div class="alert modern-alert modern-alert-info d-flex align-items-center mb-0">
            <div class="alert-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <strong>{{ $kasbonPending }} Kasbon</strong> menunggu persetujuan.
                <a href="{{ route('kasbon.index', ['status' => 'pending']) }}" class="alert-link ms-1">Lihat →</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Recent Tables -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card modern-table-card dashboard-list-card border-0 h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="list-card-heading">
                    <span class="list-card-icon loan-icon"><i class="bi bi-credit-card"></i></span>
                    <div><h6 class="mb-1 fw-bold">Pinjaman Terbaru</h6><small>Aktivitas pengajuan pinjaman</small></div>
                </div>
                <a href="{{ route('pinjaman.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="dashboard-table-head">
                            <tr>
                                <th>No. Pinjaman</th>
                                <th>Karyawan</th>
                                <th>Total Pengembalian</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pinjamanTerbaru as $p)
                            <tr>
                                <td><a href="{{ route('pinjaman.show', $p) }}" class="text-decoration-none fw-semibold text-primary">{{ $p->no_pinjaman }}</a></td>
                                <td>{{ $p->karyawan->nama }}</td>
                                <td>Rp {{ number_format($p->total_pengembalian, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$p->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $badge }}-subtle text-{{ $badge == 'warning' ? 'warning' : ($badge == 'success' ? 'success' : ($badge == 'danger' ? 'danger' : ($badge == 'info' ? 'info' : 'secondary'))) }} px-2 py-2">{{ ucfirst($p->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="dashboard-empty-state"><i class="bi bi-inbox"></i><span>Belum ada pengajuan pinjaman</span></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card modern-table-card dashboard-list-card border-0 h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="list-card-heading">
                    <span class="list-card-icon cash-icon"><i class="bi bi-cash-stack"></i></span>
                    <div><h6 class="mb-1 fw-bold">Kasbon Terbaru</h6><small>Aktivitas pengajuan kasbon</small></div>
                </div>
                <a href="{{ route('kasbon.index') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="dashboard-table-head">
                            <tr>
                                <th>No. Kasbon</th>
                                <th>Karyawan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kasbonTerbaru as $k)
                            <tr>
                                <td><a href="{{ route('kasbon.show', $k) }}" class="text-decoration-none fw-semibold text-warning">{{ $k->no_kasbon }}</a></td>
                                <td>{{ $k->karyawan->nama }}</td>
                                <td>Rp {{ number_format($k->jumlah_kasbon, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $badge = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger','lunas'=>'info'][$k->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $badge }}-subtle text-{{ $badge == 'warning' ? 'warning' : ($badge == 'success' ? 'success' : ($badge == 'danger' ? 'danger' : ($badge == 'info' ? 'info' : 'secondary'))) }} px-2 py-2">{{ ucfirst($k->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="dashboard-empty-state"><i class="bi bi-inbox"></i><span>Belum ada pengajuan kasbon</span></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
