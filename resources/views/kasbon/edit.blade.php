@extends('layouts.app')

@section('title', 'Edit Kasbon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kasbon.index') }}">Kasbon</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kasbon.show', $kasbon) }}">{{ $kasbon->no_kasbon }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Kasbon: {{ $kasbon->no_kasbon }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border mb-3">
                    <strong>Karyawan:</strong> {{ $kasbon->karyawan->nik }} — {{ $kasbon->karyawan->nama }}
                </div>

                <form action="{{ route('kasbon.update', $kasbon) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Kasbon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_kasbon"
                                    class="form-control currency @error('jumlah_kasbon') is-invalid @enderror"
                                    value="{{ old('jumlah_kasbon', number_format($kasbon->jumlah_kasbon, 0, '.', ',')) }}">
                                @error('jumlah_kasbon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Kasbon <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kasbon"
                                class="form-control @error('tanggal_kasbon') is-invalid @enderror"
                                value="{{ old('tanggal_kasbon', $kasbon->tanggal_kasbon->format('Y-m-d')) }}">
                            @error('tanggal_kasbon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2">{{ old('keperluan', $kasbon->keperluan) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $kasbon->catatan) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('kasbon.show', $kasbon) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
