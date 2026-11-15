@extends('layouts.app')

@section('title', 'Import Data Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('import.index') }}">Import Data</a></li>
    <li class="breadcrumb-item active">Import Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-cloud-upload me-2 text-primary"></i>Import Data dari File</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Format yang didukung:</strong> Excel (.xlsx, .xls) dan CSV (.csv). Maksimal 5MB.
                </div>

                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Data <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select @error('tipe') is-invalid @enderror">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="karyawan" {{ old('tipe') === 'karyawan' ? 'selected' : '' }}>Data Karyawan</option>
                            <option value="pinjaman" {{ old('tipe') === 'pinjaman' ? 'selected' : '' }}>Data Pinjaman</option>
                            <option value="kasbon"   {{ old('tipe') === 'kasbon'   ? 'selected' : '' }}>Data Kasbon</option>
                        </select>
                        @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv">
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-upload me-1"></i> Upload & Import
                        </button>
                        <a href="{{ route('import.mapping') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-table me-1"></i> Panduan Format
                        </a>
                        <a href="{{ route('import.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Format Karyawan -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i>Format CSV Karyawan</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Kolom yang diperlukan (urut):</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kolom 1</th><th>Kolom 2</th><th>Kolom 3</th><th>Kolom 4</th>
                                <th>Kolom 5</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NIK</td><td>Nama</td><td>Jabatan</td><td>No. HP</td>
                                <td>No. WA</td>
                            </tr>
                            <tr class="text-muted">
                                <td>001</td><td>John Doe</td><td>Staff</td><td>08123</td>
                                <td>62812</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Baris pertama adalah header (dilewati otomatis).</small>
            </div>
        </div>
    </div>
</div>
@endsection
