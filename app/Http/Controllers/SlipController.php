<?php

namespace App\Http\Controllers;

use App\Models\Potongan;
use App\Models\Periode;
use App\Models\Karyawan;
use App\Services\FonnteWhatsApp;
use App\Services\SlipWhatsAppMessage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SlipController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::where('user_id', auth()->id())->orderByDesc('tahun')->orderByDesc('bulan')->get();
        $selectedPeriode = $request->periode_id
            ? Periode::where('user_id', auth()->id())->find($request->periode_id)
            : Periode::where('user_id', auth()->id())->where('status', 'aktif')->first();

        // Use an empty paginator by default so views can safely call ->links()
        $potongans = Potongan::whereRaw('0 = 1')->paginate(20);
        if ($selectedPeriode) {
            $query = Potongan::with('karyawan')->where('periode_id', $selectedPeriode->id);
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%"));
            }
            $potongans = $query->paginate(20)->withQueryString();
        }

        return view('slip.index', compact('periodes', 'selectedPeriode', 'potongans'));
    }

    public function show(Potongan $potongan)
    {
        $potongan->load('karyawan.pinjamans.pembayarans', 'periode');
        return view('slip.show', compact('potongan'));
    }

    public function pdf(Potongan $potongan)
    {
        $potongan->load('karyawan.pinjamans.pembayarans', 'periode');
        $pdf = Pdf::loadView('slip.pdf', compact('potongan'))
            ->setPaper('a5', 'portrait');
        return $pdf->stream("rincian-potongan-{$potongan->karyawan->nik}-{$potongan->periode->nama}.pdf");
    }

    public function printAll(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periodes,id']);

        $periode   = Periode::findOrFail($request->periode_id);
        $potongans = Potongan::with('karyawan.pinjamans.pembayarans')
            ->where('periode_id', $periode->id)
            ->get();

        $pdf = Pdf::loadView('slip.print-all', compact('potongans', 'periode'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream("rincian-potongan-semua-{$periode->nama}.pdf");
    }

    public function kirimWa(
        Request $request,
        Potongan $potongan,
        FonnteWhatsApp $whatsapp,
        SlipWhatsAppMessage $slipMessage
    )
    {
        abort_unless($potongan->user_id === auth()->id(), 404);
        $potongan->load([
            'karyawan.pinjamans.pembayarans',
            'karyawan.kasbons',
            'karyawan.simpanan',
            'periode',
        ]);

        $noWa = $potongan->karyawan->no_wa;
        if (!$noWa) {
            return back()->with('error', 'Karyawan tidak memiliki nomor WhatsApp.');
        }

        $message = $slipMessage->build($potongan);
        $pdf = Pdf::loadView('slip.pdf', ['potongan' => $potongan])
            ->setPaper('a5', 'portrait');

        $log = $whatsapp->sendAndLog($noWa, $message, [
            'karyawan_id' => $potongan->karyawan_id,
            'tipe' => 'slip',
            'user_id' => auth()->id(),
        ], $pdf->output(), "rincian-potongan-{$potongan->karyawan->nik}.pdf");

        if ($log->status !== 'terkirim') {
            return back()->with('error', 'Rincian gagal dikirim via WhatsApp. Periksa token Fonnte dan log pengiriman.');
        }

        return back()->with('success', 'Rincian potongan berhasil dikirim via WhatsApp.');
    }
}
