@extends('layouts.app')

@section('title', 'Tambah Simpanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('simpanan.index') }}">Simpanan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-piggy-bank me-2 text-success"></i>Tambah Data Simpanan</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('simpanan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                        <div class="searchable-select-wrap">
                            <input type="text" class="form-control searchable-select-input @error('karyawan_id') is-invalid @enderror" placeholder="Cari karyawan..." aria-label="Cari karyawan" autocomplete="off">
                            <div class="searchable-select-list d-none">
                                <button type="button" class="searchable-option" data-value="" data-label="-- Pilih Karyawan --">-- Pilih Karyawan --</button>
                                @foreach($karyawans as $k)
                                    <button type="button" class="searchable-option" data-value="{{ $k->id }}" data-label="{{ $k->nik }} — {{ $k->nama }}">
                                        {{ $k->nik }} — {{ $k->nama }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="karyawan_id" value="{{ old('karyawan_id') }}">
                        </div>
                        @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Simpanan Wajib</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="simpanan_wajib" inputmode="numeric"
                                    class="form-control currency @error('simpanan_wajib') is-invalid @enderror"
                                    value="{{ old('simpanan_wajib') ? number_format(old('simpanan_wajib'), 0, ',', '.') : '' }}" min="0" step="1000">
                            </div>
                            @error('simpanan_wajib')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Simpanan Sukarela</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="simpanan_sukarela" inputmode="numeric"
                                    class="form-control currency @error('simpanan_sukarela') is-invalid @enderror"
                                    value="{{ old('simpanan_sukarela') ? number_format(old('simpanan_sukarela'), 0, ',', '.') : '' }}" min="0" step="1000">
                            </div>
                            @error('simpanan_sukarela')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
