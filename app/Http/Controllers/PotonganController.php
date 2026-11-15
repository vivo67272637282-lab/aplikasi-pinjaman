<?php

namespace App\Http\Controllers;

use App\Models\Potongan;
use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\Pinjaman;
use App\Models\Kasbon;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class PotonganController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $selectedPeriode = $request->periode_id
            ? Periode::find($request->periode_id)
            : Periode::aktif();

        $query = Potongan::where('user_id', auth()->id())->with('karyawan', 'periode');

        if ($selectedPeriode) {
            $query->where('periode_id', $selectedPeriode->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%"));
        }

        $potongans = $query->paginate(20)->withQueryString();

        return view('potongan.index', compact('potongans', 'periodes', 'selectedPeriode'));
    }

    public function create()
    {
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        $periodes  = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        return view('potongan.create', compact('karyawans', 'periodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'               => 'required|exists:karyawans,id',
            'periode_id'                => 'required|exists:periodes,id',
            'potongan_pinjaman'         => 'nullable|numeric|min:0',
            'potongan_kasbon'           => 'nullable|numeric|min:0',
            'potongan_simpanan_wajib'   => 'nullable|numeric|min:0',
            'potongan_simpanan_sukarela'=> 'nullable|numeric|min:0',
            'potongan_lainnya'          => 'nullable|numeric|min:0',
            'keterangan_lainnya'        => 'nullable|string',
            'catatan'                   => 'nullable|string',
        ]);

        // Cek duplikat
        $exists = Potongan::where('karyawan_id', $validated['karyawan_id'])
            ->where('periode_id', $validated['periode_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['karyawan_id' => 'Potongan untuk karyawan dan periode ini sudah ada.'])->withInput();
        }

        $totalPotongan = ($validated['potongan_pinjaman'] ?? 0)
            + ($validated['potongan_kasbon'] ?? 0)
            + ($validated['potongan_simpanan_wajib'] ?? 0)
            + ($validated['potongan_simpanan_sukarela'] ?? 0)
            + ($validated['potongan_lainnya'] ?? 0);

        $potongan = Potongan::create([
            ...$validated,
            'potongan_pinjaman'         => $validated['potongan_pinjaman'] ?? 0,
            'potongan_kasbon'           => $validated['potongan_kasbon'] ?? 0,
            'potongan_simpanan_wajib'   => $validated['potongan_simpanan_wajib'] ?? 0,
            'potongan_simpanan_sukarela'=> $validated['potongan_simpanan_sukarela'] ?? 0,
            'potongan_lainnya'          => $validated['potongan_lainnya'] ?? 0,
            'total_potongan'            => $totalPotongan,
        ]);

        return redirect()->route('potongan.show', $potongan)
            ->with('success', 'Data potongan berhasil disimpan.');
    }

    public function show(Potongan $potongan)
    {
        $potongan->load('karyawan', 'periode');
        return view('potongan.show', compact('potongan'));
    }

    public function edit(Potongan $potongan)
    {
        $potongan->load('karyawan', 'periode');
        $periodes = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();
        return view('potongan.edit', compact('potongan', 'periodes'));
    }

    public function update(Request $request, Potongan $potongan)
    {
        $validated = $request->validate([
            'potongan_pinjaman'         => 'nullable|numeric|min:0',
            'potongan_kasbon'           => 'nullable|numeric|min:0',
            'potongan_simpanan_wajib'   => 'nullable|numeric|min:0',
            'potongan_simpanan_sukarela'=> 'nullable|numeric|min:0',
            'potongan_lainnya'          => 'nullable|numeric|min:0',
            'keterangan_lainnya'        => 'nullable|string',
            'catatan'                   => 'nullable|string',
        ]);

        $totalPotongan = ($validated['potongan_pinjaman'] ?? 0)
            + ($validated['potongan_kasbon'] ?? 0)
            + ($validated['potongan_simpanan_wajib'] ?? 0)
            + ($validated['potongan_simpanan_sukarela'] ?? 0)
            + ($validated['potongan_lainnya'] ?? 0);

        $potongan->update([
            ...$validated,
            'potongan_pinjaman'         => $validated['potongan_pinjaman'] ?? 0,
            'potongan_kasbon'           => $validated['potongan_kasbon'] ?? 0,
            'potongan_simpanan_wajib'   => $validated['potongan_simpanan_wajib'] ?? 0,
            'potongan_simpanan_sukarela'=> $validated['potongan_simpanan_sukarela'] ?? 0,
            'potongan_lainnya'          => $validated['potongan_lainnya'] ?? 0,
            'total_potongan'            => $totalPotongan,
        ]);

        return redirect()->route('potongan.show', $potongan)
            ->with('success', 'Data potongan berhasil diperbarui.');
    }

    public function destroy(Potongan $potongan)
    {
        $potongan->delete();
        return redirect()->route('potongan.index')
            ->with('success', 'Data potongan berhasil dihapus.');
    }

    public function generate(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periodes,id']);

        $periode   = Periode::findOrFail($request->periode_id);
        $karyawans = Karyawan::where('is_active', true)->with('pinjamans', 'kasbons', 'simpanan')->get();
        $generated = 0;

        foreach ($karyawans as $karyawan) {
            if (Potongan::where('karyawan_id', $karyawan->id)->where('periode_id', $periode->id)->exists()) {
                continue;
            }

            $potonganPinjaman = $karyawan->pinjamans
                ->where('status', 'disetujui')
                ->sum('cicilan_per_bulan');

            $sisaKasbon = $karyawan->kasbons
                ->where('status', 'disetujui')
                ->sum('sisa_kasbon');

            $potonganKasbon = $sisaKasbon;

            $potonganWajib   = $karyawan->simpanan?->simpanan_wajib ?? 0;
            $potonganSukarela= $karyawan->simpanan?->simpanan_sukarela ?? 0;

            $total = $potonganPinjaman + $potonganKasbon + $potonganWajib + $potonganSukarela;

            Potongan::create([
                'karyawan_id'               => $karyawan->id,
                'periode_id'                => $periode->id,
                'potongan_pinjaman'         => $potonganPinjaman,
                'potongan_kasbon'           => $potonganKasbon,
                'potongan_simpanan_wajib'   => $potonganWajib,
                'potongan_simpanan_sukarela'=> $potonganSukarela,
                'potongan_lainnya'          => 0,
                'total_potongan'            => $total,
            ]);

            $generated++;
        }

        return back()->with('success', "Berhasil generate potongan untuk {$generated} karyawan pada periode {$periode->nama}.");
    }
}
