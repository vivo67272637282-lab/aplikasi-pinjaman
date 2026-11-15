@extends('layouts.app')

@section('title', 'Ajukan Pinjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
    <li class="breadcrumb-item active">Ajukan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i>Pengajuan Pinjaman Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pinjaman.store') }}" method="POST" id="formPinjaman">
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
                            <label class="form-label fw-semibold">Jumlah Pinjaman <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_pinjaman" id="jumlahPinjaman"
                                    class="form-control currency @error('jumlah_pinjaman') is-invalid @enderror"
                                    value="{{ old('jumlah_pinjaman') ? number_format(old('jumlah_pinjaman'), 0, '.', ',') : '' }}" placeholder="0">
                                @error('jumlah_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tenor (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="tenor" id="tenor"
                                class="form-control @error('tenor') is-invalid @enderror"
                                value="{{ old('tenor') }}" min="1" max="60" placeholder="Contoh: 12">
                            @error('tenor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bunga Pinjaman (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="bunga_persen" id="bungaPersen" step="0.01"
                                    class="form-control @error('bunga_persen') is-invalid @enderror"
                                    value="{{ old('bunga_persen', 0) }}" min="0" max="100" placeholder="Contoh: 5">
                                <span class="input-group-text">%</span>
                                @error('bunga_persen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 py-2" id="infoSicilan" style="display:none">
                        <i class="bi bi-calculator me-1"></i>
                        Bunga: <strong id="hasilBunga">-</strong> ·
                        Total pengembalian: <strong id="hasilTotal">-</strong> ·
                        Estimasi cicilan per bulan: <strong id="hasilCicilan">-</strong>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Tanggal Pinjaman <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pinjaman"
                            class="form-control @error('tanggal_pinjaman') is-invalid @enderror"
                            value="{{ old('tanggal_pinjaman', date('Y-m-d')) }}">
                        @error('tanggal_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2"
                            placeholder="Jelaskan keperluan pinjaman">{{ old('keperluan') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"
                            placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Ajukan Pinjaman
                        </button>
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function hitungCicilan() {
        const jumlah = parseFloat(document.getElementById('jumlahPinjaman').value.replace(/[.,]/g, '')) || 0;
        const bungaPersen = parseFloat(document.getElementById('bungaPersen').value) || 0;
        const tenor  = parseInt(document.getElementById('tenor').value) || 0;
        const info   = document.getElementById('infoSicilan');
        const hasil  = document.getElementById('hasilCicilan');

        if (jumlah > 0 && tenor > 0) {
            const bunga = Math.round(jumlah * bungaPersen) / 100;
            const total = jumlah + bunga;
            const formatRupiah = value => 'Rp ' + value.toLocaleString('id-ID', { maximumFractionDigits: 2 });
            document.getElementById('hasilBunga').textContent = formatRupiah(bunga);
            document.getElementById('hasilTotal').textContent = formatRupiah(total);
            hasil.textContent = formatRupiah(Math.round(total / tenor * 100) / 100);
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    }

    document.getElementById('jumlahPinjaman').addEventListener('input', hitungCicilan);
    document.getElementById('tenor').addEventListener('input', hitungCicilan);
    document.getElementById('bungaPersen').addEventListener('input', hitungCicilan);
    hitungCicilan();
</script>
@endpush
