@extends('layouts.app')

@section('title', 'Edit Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('simpanan.index') }}">Simpanan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('simpanan.show', $simpanan) }}">{{ $simpanan->karyawan->nama }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Simpanan: {{ $simpanan->karyawan->nama }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('simpanan.update', $simpanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Simpanan Wajib</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="simpanan_wajib" inputmode="numeric"
                                    class="form-control currency"
                                    value="{{ old('simpanan_wajib', number_format($simpanan->simpanan_wajib, 0, ',', '.')) }}" min="0" step="1000">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Simpanan Sukarela</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="simpanan_sukarela" inputmode="numeric"
                                    class="form-control currency"
                                    value="{{ old('simpanan_sukarela', number_format($simpanan->simpanan_sukarela, 0, ',', '.')) }}" min="0" step="1000">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('simpanan.show', $simpanan) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
