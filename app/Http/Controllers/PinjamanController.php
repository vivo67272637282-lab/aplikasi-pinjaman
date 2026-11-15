<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\PinjamanPembayaran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pinjaman::where('user_id', auth()->id())
            ->with('karyawan')
            ->withCount('pembayarans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pinjaman', 'like', "%$search%")
                  ->orWhereHas('karyawan', fn($q2) => $q2->where('nama', 'like', "%$search%")
                      ->orWhere('nik', 'like', "%$search%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pinjamans = $query->latest()->paginate(15)->withQueryString();

        return view('pinjaman.index', compact('pinjamans'));
    }

    public function create()
    {
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('pinjaman.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'    => 'required|exists:karyawans,id',
            'jumlah_pinjaman'=> 'required|numeric|min:1',
            'bunga_persen'   => 'required|numeric|min:0|max:100',
            'tenor'          => 'required|integer|min:1|max:60',
            'tanggal_pinjaman'=> 'required|date',
            'keperluan'      => 'nullable|string',
            'catatan'        => 'nullable|string',
        ]);

        $totalPengembalian = $this->hitungTotalPengembalian(
            (float) $validated['jumlah_pinjaman'],
            (float) $validated['bunga_persen']
        );
        $cicilan = round($totalPengembalian / $validated['tenor'], 2);

        $pinjaman = Pinjaman::create([
            ...$validated,
            'no_pinjaman'      => Pinjaman::generateNomor(),
            'cicilan_per_bulan'=> $cicilan,
            'sisa_pinjaman'    => $totalPengembalian,
            'status'           => 'pending',
        ]);

        return redirect()->route('pinjaman.show', $pinjaman)
            ->with('success', "Pinjaman {$pinjaman->no_pinjaman} berhasil diajukan.");
    }

    public function show(Pinjaman $pinjaman)
    {
        $pinjaman->load('karyawan', 'disetujuiOleh', 'pembayarans');
        return view('pinjaman.show', compact('pinjaman'));
    }

    public function edit(Pinjaman $pinjaman)
    {
        if (!in_array($pinjaman->status, ['pending', 'disetujui'])) {
            return back()->with('error', 'Pinjaman ini tidak dapat diedit.');
        }
        if ($pinjaman->pembayarans()->exists()) {
            return back()->with('error', 'Pinjaman yang sudah memiliki pembayaran tidak dapat diedit.');
        }
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('pinjaman.edit', compact('pinjaman', 'karyawans'));
    }

    public function update(Request $request, Pinjaman $pinjaman)
    {
        $validated = $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:1',
            'bunga_persen'    => 'required|numeric|min:0|max:100',
            'tenor'           => 'required|integer|min:1|max:60',
            'tanggal_pinjaman'=> 'required|date',
            'keperluan'       => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $totalPengembalian = $this->hitungTotalPengembalian(
            (float) $validated['jumlah_pinjaman'],
            (float) $validated['bunga_persen']
        );
        $cicilan = round($totalPengembalian / $validated['tenor'], 2);

        $pinjaman->update([
            ...$validated,
            'cicilan_per_bulan' => $cicilan,
            'sisa_pinjaman'     => $totalPengembalian,
        ]);

        return redirect()->route('pinjaman.show', $pinjaman)
            ->with('success', 'Data pinjaman berhasil diperbarui.');
    }

    public function destroy(Pinjaman $pinjaman)
    {
        if ($pinjaman->status === 'disetujui') {
            return back()->with('error', 'Pinjaman yang sudah disetujui tidak dapat dihapus.');
        }
        $pinjaman->delete();
        return redirect()->route('pinjaman.index')
            ->with('success', 'Pinjaman berhasil dihapus.');
    }

    public function setujui(Pinjaman $pinjaman)
    {
        if ($pinjaman->status !== 'pending') {
            return back()->with('error', 'Pinjaman ini bukan dalam status pending.');
        }

        $pinjaman->update([
            'status'           => 'disetujui',
            'disetujui_oleh'   => auth()->id(),
            'tanggal_disetujui'=> now(),
        ]);

        return back()->with('success', "Pinjaman {$pinjaman->no_pinjaman} berhasil disetujui.");
    }

    public function bayar(Request $request, Pinjaman $pinjaman)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        abort_if($pinjaman->user_id !== auth()->id(), 403);

        $pembayaranKe = DB::transaction(function () use ($pinjaman, $validated) {
            $lockedPinjaman = Pinjaman::whereKey($pinjaman->id)->lockForUpdate()->firstOrFail();

            if ($lockedPinjaman->status !== 'disetujui' || $lockedPinjaman->sisa_pinjaman <= 0) {
                abort(422, 'Pembayaran hanya dapat dicatat untuk pinjaman yang masih aktif.');
            }

            $pembayaranKe = $lockedPinjaman->pembayarans()->count() + 1;
            if ($pembayaranKe > $lockedPinjaman->tenor) {
                abort(422, 'Jumlah pembayaran sudah mencapai tenor pinjaman.');
            }

            $sisaDalamSen = (int) round((float) $lockedPinjaman->sisa_pinjaman * 100);
            $cicilanDalamSen = (int) round((float) $lockedPinjaman->cicilan_per_bulan * 100);
            $jumlahBayarDalamSen = $pembayaranKe >= $lockedPinjaman->tenor
                ? $sisaDalamSen
                : min($cicilanDalamSen, $sisaDalamSen);
            $sisaDalamSen -= $jumlahBayarDalamSen;

            $lockedPinjaman->pembayarans()->create([
                'pembayaran_ke' => $pembayaranKe,
                'jumlah_bayar' => $jumlahBayarDalamSen / 100,
                'tanggal_bayar' => $validated['tanggal_bayar'],
            ]);

            $lockedPinjaman->update([
                'sisa_pinjaman' => $sisaDalamSen / 100,
                'status' => $sisaDalamSen === 0 ? 'lunas' : 'disetujui',
            ]);

            return $pembayaranKe;
        });

        return redirect()->route('pinjaman.show', $pinjaman)
            ->with('success', "Pembayaran ke-{$pembayaranKe} berhasil dicatat.");
    }

    public function tolak(Request $request, Pinjaman $pinjaman)
    {
        $request->validate(['catatan' => 'nullable|string']);

        if ($pinjaman->status !== 'pending') {
            return back()->with('error', 'Pinjaman ini bukan dalam status pending.');
        }

        $pinjaman->update([
            'status'  => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', "Pinjaman {$pinjaman->no_pinjaman} telah ditolak.");
    }

    private function hitungTotalPengembalian(float $jumlahPinjaman, float $bungaPersen): float
    {
        return round($jumlahPinjaman + ($jumlahPinjaman * $bungaPersen / 100), 2);
    }
}
