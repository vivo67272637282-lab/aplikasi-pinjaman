@extends('layouts.app')

@section('title', 'Tambah Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('potongan.index') }}">Potongan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-scissors me-2 text-danger"></i>Tambah Data Potongan</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('potongan.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                            <div class="searchable-select-wrap">
                                <input type="text" class="form-control searchable-select-input @error('karyawan_id') is-invalid @enderror" placeholder="Cari karyawan..." aria-label="Cari karyawan" autocomplete="off">
                                <div class="searchable-select-list d-none">
                                    <button type="button" class="searchable-option" data-value="" data-label="-- Pilih Karyawan --">-- Pilih Karyawan --</button>
                                    @foreach($karyawans as $k)
                                        <button type="button" class="searchable-option" data-value="{{ $k->id }}" data-label="{{ $k->nik }} — {{ $k->nama }}"
                                            data-total-pinjaman="{{ $k->sisa_pinjaman }}"
                                            data-sisa-kasbon="{{ $k->sisa_kasbon }}"
                                            data-simpanan-wajib="{{ $k->simpanan?->simpanan_wajib ?? 0 }}"
                                            data-simpanan-sukarela="{{ $k->simpanan?->simpanan_sukarela ?? 0 }}">
                                            {{ $k->nik }} — {{ $k->nama }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="karyawan_id" value="{{ old('karyawan_id') }}">
                            </div>
                            @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <select name="periode_id" class="form-select @error('periode_id') is-invalid @enderror">
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periodes as $p)
                                    <option value="{{ $p->id }}" {{ old('periode_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Total Pinjaman</label>
                                    <input type="text" id="karyawan_total_pinjaman" class="form-control currency" value="0" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Sisa Kasbon</label>
                                    <input type="text" id="karyawan_sisa_kasbon" class="form-control currency" value="0" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Simpanan Wajib</label>
                                    <input type="text" id="karyawan_simpanan_wajib" class="form-control currency" value="0" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Simpanan Sukarela</label>
                                    <input type="text" id="karyawan_simpanan_sukarela" class="form-control currency" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Pinjaman</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_pinjaman" class="form-control currency"
                                    value="{{ old('potongan_pinjaman', 0) }}" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Kasbon</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_kasbon" class="form-control currency"
                                    value="{{ old('potongan_kasbon', 0) }}" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pot. Simpanan Wajib</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_simpanan_wajib" class="form-control currency"
                                    value="{{ old('potongan_simpanan_wajib', 0) }}" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pot. Simpanan Sukarela</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_simpanan_sukarela" class="form-control currency"
                                    value="{{ old('potongan_simpanan_sukarela', 0) }}" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_lainnya" class="form-control"
                                    value="{{ old('potongan_lainnya', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Keterangan Lainnya</label>
                            <input type="text" name="keterangan_lainnya" class="form-control"
                                value="{{ old('keterangan_lainnya') }}" placeholder="Keterangan potongan lainnya">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('potongan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
