@extends('layouts.app')

@section('title', 'Edit Pinjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pinjaman.show', $pinjaman) }}">{{ $pinjaman->no_pinjaman }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Pinjaman: {{ $pinjaman->no_pinjaman }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border mb-3">
                    <strong>Karyawan:</strong> {{ $pinjaman->karyawan->nik }} — {{ $pinjaman->karyawan->nama }}
                </div>

                <form action="{{ route('pinjaman.update', $pinjaman) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Pinjaman <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_pinjaman" id="jumlahPinjaman"
                                    class="form-control currency @error('jumlah_pinjaman') is-invalid @enderror"
                                    value="{{ old('jumlah_pinjaman', number_format($pinjaman->jumlah_pinjaman, 0, '.', ',')) }}">
                                @error('jumlah_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tenor (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="tenor" id="tenor"
                                class="form-control @error('tenor') is-invalid @enderror"
                                value="{{ old('tenor', $pinjaman->tenor) }}" min="1" max="60">
                            @error('tenor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bunga Pinjaman (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="bunga_persen" id="bungaPersen" step="0.01"
                                    class="form-control @error('bunga_persen') is-invalid @enderror"
                                    value="{{ old('bunga_persen', $pinjaman->bunga_persen) }}" min="0" max="100">
                                <span class="input-group-text">%</span>
                                @error('bunga_persen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 py-2" id="infoSicilan">
                        <i class="bi bi-calculator me-1"></i>
                        Bunga: <strong id="hasilBunga">Rp {{ number_format($pinjaman->jumlah_bunga, 0, ',', '.') }}</strong> ·
                        Total pengembalian: <strong id="hasilTotal">Rp {{ number_format($pinjaman->total_pengembalian, 0, ',', '.') }}</strong> ·
                        Estimasi cicilan per bulan: <strong id="hasilCicilan">Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</strong>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Tanggal Pinjaman <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pinjaman"
                            class="form-control @error('tanggal_pinjaman') is-invalid @enderror"
                            value="{{ old('tanggal_pinjaman', $pinjaman->tanggal_pinjaman->format('Y-m-d')) }}">
                        @error('tanggal_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="2">{{ old('keperluan', $pinjaman->keperluan) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $pinjaman->catatan) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('pinjaman.show', $pinjaman) }}" class="btn btn-secondary">Batal</a>
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
        if (jumlah > 0 && tenor > 0) {
            const bunga = Math.round(jumlah * bungaPersen) / 100;
            const total = jumlah + bunga;
            const formatRupiah = value => 'Rp ' + value.toLocaleString('id-ID', { maximumFractionDigits: 2 });
            document.getElementById('hasilBunga').textContent = formatRupiah(bunga);
            document.getElementById('hasilTotal').textContent = formatRupiah(total);
            document.getElementById('hasilCicilan').textContent = formatRupiah(Math.round(total / tenor * 100) / 100);
        }
    }
    document.getElementById('jumlahPinjaman').addEventListener('input', hitungCicilan);
    document.getElementById('tenor').addEventListener('input', hitungCicilan);
    document.getElementById('bungaPersen').addEventListener('input', hitungCicilan);
    hitungCicilan();
</script>
@endpush
