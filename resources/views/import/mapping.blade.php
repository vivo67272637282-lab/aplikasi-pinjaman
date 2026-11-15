@extends('layouts.app')

@section('title', 'Panduan Import')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('import.index') }}">Import Data</a></li>
    <li class="breadcrumb-item active">Panduan Format</li>
@endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-book me-2 text-primary"></i>Panduan Format Import</h4>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Format Karyawan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>NIK</td><td><span class="badge bg-danger">Ya</span></td><td>Nomor Induk Karyawan (unik)</td></tr>
                            <tr><td>2</td><td>Nama</td><td><span class="badge bg-danger">Ya</span></td><td>Nama lengkap</td></tr>
                            <tr><td>3</td><td>Jabatan</td><td><span class="badge bg-secondary">Tidak</span></td><td>Posisi/jabatan</td></tr>
                            <tr><td>4</td><td>No. HP</td><td><span class="badge bg-secondary">Tidak</span></td><td>Nomor HP</td></tr>
                            <tr><td>5</td><td>No. WA</td><td><span class="badge bg-secondary">Tidak</span></td><td>Format: 628xxxxxxxx</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips Import</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Simpan file sebagai CSV (UTF-8) untuk menghindari masalah encoding</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Baris pertama (header) akan dilewati otomatis</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>NIK yang sudah ada akan diperbarui (update), NIK baru akan dibuat (insert)</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Maksimal ukuran file: 5MB</li>
                    <li class="mb-2"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Pastikan tidak ada kolom kosong di kolom wajib</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('import.create') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> Mulai Import
    </a>
    <a href="{{ route('import.index') }}" class="btn btn-secondary ms-2">Kembali</a>
</div>
@endsection
