<?php

namespace App\Http\Controllers;

use App\Mail\SlipPotonganMail;
use App\Models\WaLog;
use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\Potongan;
use App\Services\FonnteWhatsApp;
use App\Services\SlipWhatsAppMessage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WhatsAppController extends Controller
{
    public function index()
    {
        $periode = Periode::where('user_id', auth()->id())->where('status', 'aktif')->first()
            ?? Periode::where('user_id', auth()->id())->orderByDesc('tahun')->orderByDesc('bulan')->first();

        $query = Karyawan::where('user_id', auth()->id())->where('is_active', true)
            ->where(function ($query) {
                $query->whereNotNull('no_wa');

                if (Schema::hasColumn('karyawans', 'email')) {
                    $query->orWhereNotNull('email');
                }
            });

        $karyawans = $query->orderBy('nama')->get();

        return view('whatsapp.index', compact('karyawans', 'periode'));
    }

    public function send(Request $request, FonnteWhatsApp $whatsapp)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'pesan'       => 'required|string|max:1000',
        ]);

        $karyawan = Karyawan::where('user_id', auth()->id())->findOrFail($request->karyawan_id);

        if (!$karyawan->no_wa) {
            return back()->with('error', 'Karyawan tidak memiliki nomor WhatsApp.');
        }

        $log = $whatsapp->sendAndLog($karyawan->no_wa, $request->pesan, [
            'karyawan_id' => $karyawan->id,
            'tipe'        => 'manual',
            'user_id'     => auth()->id(),
        ]);

        if ($log->status === 'terkirim') {
            return back()->with('success', "Pesan berhasil dikirim ke {$karyawan->nama}.");
        }

        return back()->with('error', 'Pesan WhatsApp gagal dikirim. Periksa token Fonnte dan log pengiriman.');
    }

    public function sendSlip(Request $request, FonnteWhatsApp $whatsapp, SlipWhatsAppMessage $slipMessage)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'channel'     => 'required|in:wa,email',
            'periode_id'  => 'nullable|exists:periodes,id',
        ]);

        $periode = Periode::where('user_id', auth()->id())->find($request->periode_id)
            ?? Periode::where('user_id', auth()->id())->where('status', 'aktif')->first();
        if (!$periode) {
            return back()->with('error', 'Periode potongan belum tersedia.');
        }

        $potongan = Potongan::where('user_id', auth()->id())
            ->with([
                'karyawan.pinjamans.pembayarans',
                'karyawan.kasbons',
                'karyawan.simpanan',
                'periode',
            ])
            ->where('periode_id', $periode->id)
            ->where('karyawan_id', $request->karyawan_id)
            ->first();

        if (!$potongan) {
            return back()->with('error', 'Rincian potongan karyawan ini belum tersedia di periode yang dipilih.');
        }

        if ($request->channel === 'wa') {
            $karyawan = $potongan->karyawan;
            if (!$karyawan->no_wa) {
                return back()->with('error', 'Karyawan belum memiliki nomor WhatsApp.');
            }

            $message = $slipMessage->build($potongan);

            $pdf = Pdf::loadView('slip.pdf', ['potongan' => $potongan])
                ->setPaper('a5', 'portrait');

            $log = $whatsapp->sendAndLog($karyawan->no_wa, $message, [
                'karyawan_id' => $karyawan->id,
                'tipe' => 'slip',
                'user_id' => auth()->id(),
            ], $pdf->output(), "rincian-potongan-{$karyawan->nik}.pdf");

            if ($log->status === 'terkirim') {
                return back()->with('success', "Rincian potongan berhasil dikirim via WhatsApp ke {$karyawan->nama}.");
            }

            return back()->with('error', 'Rincian gagal dikirim via WhatsApp. Periksa token Fonnte dan log pengiriman.');
        }

        $karyawan = $potongan->karyawan;
        if (!$karyawan->email) {
            return back()->with('error', 'Karyawan belum memiliki alamat email.');
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return back()->with('error', 'Pengiriman email belum aktif. Atur MAIL_MAILER=smtp dan konfigurasi SMTP pada file .env.');
        }

        try {
            Mail::to($karyawan->email)->send(new SlipPotonganMail($potongan));

            return back()->with('success', "Rincian potongan berhasil dikirim via email ke {$karyawan->nama}.");
        } catch (\Exception $e) {
            Log::error('Gagal mengirim slip potongan melalui email.', [
                'karyawan_id' => $karyawan->id,
                'periode_id' => $potongan->periode_id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Email gagal dikirim. Periksa konfigurasi SMTP dan log aplikasi.');
        }
    }

    public function sendBroadcast(Request $request, FonnteWhatsApp $whatsapp)
    {
        $request->validate(['pesan' => 'required|string|max:1000']);

        $karyawans = Karyawan::where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereNotNull('no_wa')
            ->get();
        $terkirim  = 0;
        $gagal = 0;

        foreach ($karyawans as $karyawan) {
            $log = $whatsapp->sendAndLog($karyawan->no_wa, $request->pesan, [
                'karyawan_id' => $karyawan->id,
                'tipe'        => 'broadcast',
                'user_id'     => auth()->id(),
            ]);

            if ($log->status === 'terkirim') {
                $terkirim++;
            } else {
                $gagal++;
            }
        }

        if ($gagal > 0) {
            return back()->with('error', "Broadcast selesai: {$terkirim} terkirim, {$gagal} gagal. Periksa log pengiriman.");
        }

        return back()->with('success', "Broadcast berhasil dikirim ke {$terkirim} karyawan.");
    }

    public function logs(Request $request)
    {
        $query = WaLog::with('karyawan', 'user')->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tujuan', 'like', "%$search%")
                  ->orWhereHas('karyawan', fn($q2) => $q2->where('nama', 'like', "%$search%"));
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('whatsapp.logs', compact('logs'));
    }
}
