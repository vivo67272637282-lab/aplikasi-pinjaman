<?php

namespace App\Http\Controllers;

use App\Models\Potongan;
use App\Models\Pinjaman;
use App\Models\Kasbon;
use App\Models\Simpanan;
use App\Models\Karyawan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        return view('laporan.index', compact('periodes'));
    }

    // --- Rekap Potongan ---
    public function rekapPotongan(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periodes,id']);
        $periode = Periode::findOrFail($request->periode_id);

        $data = Potongan::with('karyawan')
            ->where('periode_id', $periode->id)
            ->orderBy('karyawan_id')
            ->get();

        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('laporan.rekap-potongan', compact('data', 'periode', 'periodes'));
    }

    public function pdfPotongan(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periodes,id']);
        $periode = Periode::findOrFail($request->periode_id);

        $data = Potongan::with('karyawan.pinjamans.pembayarans')
            ->where('periode_id', $periode->id)
            ->orderBy('karyawan_id')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf-potongan', compact('data', 'periode'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("rekap-potongan-{$periode->nama}.pdf");
    }

    // --- Rekap Pinjaman ---
    public function rekapPinjaman(Request $request)
    {
        $query = Pinjaman::with('karyawan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_pinjaman', $request->bulan)
                  ->whereYear('tanggal_pinjaman', $request->tahun);
        }

        $data = $query->orderBy('tanggal_pinjaman')->get();

        return view('laporan.rekap-pinjaman', compact('data'));
    }

    public function pdfPinjaman(Request $request)
    {
        $query = Pinjaman::with(['karyawan', 'pembayarans']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tanggal_pinjaman')->get();

        $pdf = Pdf::loadView('laporan.pdf-pinjaman', compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap-pinjaman.pdf');
    }

    // --- Rekap Kasbon ---
    public function rekapKasbon(Request $request)
    {
        $query = Kasbon::with('karyawan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tanggal_kasbon')->get();

        return view('laporan.rekap-kasbon', compact('data'));
    }

    public function pdfKasbon(Request $request)
    {
        $query = Kasbon::with('karyawan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tanggal_kasbon')->get();

        $pdf = Pdf::loadView('laporan.pdf-kasbon', compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap-kasbon.pdf');
    }

    // --- Rekap Simpanan ---
    public function rekapSimpanan(Request $request)
    {
        $query = Simpanan::with('karyawan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%"));
        }

        $data = $query->get();
        $totalSimpanan = $data->sum('total_simpanan');

        return view('laporan.rekap-simpanan', compact('data', 'totalSimpanan'));
    }

    public function pdfSimpanan(Request $request)
    {
        $data = Simpanan::with('karyawan')->get();
        $totalSimpanan = $data->sum('total_simpanan');

        $pdf = Pdf::loadView('laporan.pdf-simpanan', compact('data', 'totalSimpanan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('rekap-simpanan.pdf');
    }
}
