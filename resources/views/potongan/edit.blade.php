@extends('layouts.app')

@section('title', 'Edit Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('potongan.index') }}">Potongan</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>
                    Edit Potongan: {{ $potongan->karyawan->nama }} — {{ $potongan->periode->nama }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('potongan.update', $potongan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Pinjaman</label>
                                    <input type="text" class="form-control currency" value="{{ number_format($potongan->karyawan->sisa_pinjaman, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Pinjaman</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_pinjaman" class="form-control currency"
                                    value="{{ old('potongan_pinjaman', $potongan->potongan_pinjaman) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Kasbon</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_kasbon" class="form-control"
                                    value="{{ old('potongan_kasbon', $potongan->potongan_kasbon) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pot. Simpanan Wajib</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_simpanan_wajib" class="form-control"
                                    value="{{ old('potongan_simpanan_wajib', $potongan->potongan_simpanan_wajib) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pot. Simpanan Sukarela</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_simpanan_sukarela" class="form-control"
                                    value="{{ old('potongan_simpanan_sukarela', $potongan->potongan_simpanan_sukarela) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_lainnya" class="form-control"
                                    value="{{ old('potongan_lainnya', $potongan->potongan_lainnya) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Keterangan Lainnya</label>
                            <input type="text" name="keterangan_lainnya" class="form-control"
                                value="{{ old('keterangan_lainnya', $potongan->keterangan_lainnya) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $potongan->catatan) }}</textarea>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('potongan.show', $potongan) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
