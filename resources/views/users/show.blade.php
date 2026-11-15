@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Detail Pengguna</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-square me-1"></i> Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                        <div class="text-muted">{{ $user->username }}</div>
                    </div>
                </div>

                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 180px;">Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Username</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td>{{ $user->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Role</th>
                        <td>
                            @php
                                $roleClass = ['admin' => 'danger', 'operator' => 'primary', 'viewer' => 'secondary'][$user->role] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $roleClass }} text-capitalize">{{ $user->role }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
