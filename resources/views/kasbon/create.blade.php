@extends('layouts.app')

@section('title', 'Ajukan Kasbon')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kasbon.index') }}">Kasbon</a></li>
    <li class="breadcrumb-item active">Ajukan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-warning"></i>Pengajuan Kasbon Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kasbon.store') }}" method="POST">
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
                            <input type="hidden" name="karyawan_id" value="{{ old('karyawan_id', request('karyawan_id')) }}">
                        </div>
                        @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Kasbon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_kasbon"
                                    class="form-control currency @error('jumlah_kasbon') is-invalid @enderror"
                                    value="{{ old('jumlah_kasbon') ? number_format(old('jumlah_kasbon'), 0, '.', ',') : '' }}">
                                @error('jumlah_kasbon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Kasbon <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kasbon"
                                class="form-control @error('tanggal_kasbon') is-invalid @enderror"
                                value="{{ old('tanggal_kasbon', date('Y-m-d')) }}">
                            @error('tanggal_kasbon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2"
                            placeholder="Jelaskan keperluan kasbon">{{ old('keperluan') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-send me-1"></i> Ajukan Kasbon
                        </button>
                        <a href="{{ route('kasbon.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
