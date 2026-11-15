@extends('layouts.app')

@section('title', 'Kirim Rincian Potongan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kirim Rincian Potongan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-send-check me-2 text-success"></i>Kirim Rincian Potongan</h4>
    <a href="{{ route('whatsapp.logs') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-clock-history me-1"></i> Log Pengiriman
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2 text-success"></i>Tujuan Pengiriman</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('whatsapp.send-slip') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                        <div class="searchable-select-wrap">
                            <input type="text" class="form-control searchable-select-input @error('karyawan_id') is-invalid @enderror" placeholder="Cari karyawan..." aria-label="Cari karyawan" autocomplete="off">
                            <div class="searchable-select-list d-none">
                                <button type="button" class="searchable-option" data-value="" data-label="-- Pilih Karyawan --">-- Pilih Karyawan --</button>
                                @foreach($karyawans as $k)
                                    <button type="button" class="searchable-option" data-value="{{ $k->id }}" data-label="{{ $k->nik }} — {{ $k->nama }}{{ $k->no_wa ? ' (WA)' : '' }}{{ $k->email ? ' (Email)' : '' }}">
                                        {{ $k->nik }} — {{ $k->nama }} {{ $k->no_wa ? '(WA)' : '' }} {{ $k->email ? '(Email)' : '' }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="karyawan_id" value="{{ old('karyawan_id') }}">
                        </div>
                        @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                        <select name="periode_id" class="form-select @error('periode_id') is-invalid @enderror">
                            <option value="">-- Pilih Periode --</option>
                            @php $periods = App\Models\Periode::orderByDesc('tahun')->orderByDesc('bulan')->get(); @endphp
                            @foreach($periods as $p)
                                <option value="{{ $p->id }}" {{ ($periode && $periode->id == $p->id) ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                        @error('periode_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Metode Pengiriman <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="channel" id="channelWa" value="wa" checked>
                                <label class="form-check-label" for="channelWa">
                                    <i class="bi bi-whatsapp text-success me-1"></i> WhatsApp
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="channel" id="channelEmail" value="email">
                                <label class="form-check-label" for="channelEmail">
                                    <i class="bi bi-envelope text-primary me-1"></i> Email
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-send me-1"></i> Kirim Rincian Potongan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
