@extends('layouts.app')

@section('title', 'Identitas Perusahaan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Identitas Perusahaan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Identitas Perusahaan</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('company-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="company_name" class="form-label fw-semibold">Nama Perusahaan</label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name', $setting->company_name ?? config('company.name')) }}"
                            class="form-control @error('company_name') is-invalid @enderror"
                            maxlength="150"
                            required
                        >
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="company_address" class="form-label fw-semibold">Alamat Perusahaan</label>
                        <textarea
                            id="company_address"
                            name="company_address"
                            rows="3"
                            maxlength="500"
                            class="form-control @error('company_address') is-invalid @enderror"
                        >{{ old('company_address', $setting->company_address) }}</textarea>
                        @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label fw-semibold">Logo Perusahaan</label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept=".png,.jpg,.jpeg,.gif,image/png,image/jpeg,image/gif"
                            class="form-control @error('logo') is-invalid @enderror"
                        >
                        <div class="form-text">Format PNG, JPG, atau GIF. Maksimal 2 MB. Unggah file baru untuk mengganti logo.</div>
                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if($setting->logo_path)
                    <p class="text-muted small mb-3">Logo sudah tersimpan dan akan digunakan pada PDF laporan.</p>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Identitas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
