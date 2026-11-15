<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use App\Models\Karyawan;
use App\Models\RiwayatSimpanan;
use App\Models\Periode;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $query = Simpanan::where('user_id', auth()->id())->with('karyawan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%"));
        }

        $simpanans = $query->latest()->paginate(15)->withQueryString();

        return view('simpanan.index', compact('simpanans'));
    }

    public function create()
    {
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('simpanan.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'        => 'required|exists:karyawans,id|unique:simpanans,karyawan_id',
            'simpanan_wajib'     => 'nullable|numeric|min:0',
            'simpanan_sukarela'  => 'nullable|numeric|min:0',
        ]);

        $wajib = (float) ($validated['simpanan_wajib'] ?? 0);
        $sukarela = (float) ($validated['simpanan_sukarela'] ?? 0);
        $total = $wajib + $sukarela;

        $simpanan = Simpanan::create([
            'karyawan_id'       => $validated['karyawan_id'],
            'simpanan_pokok'    => 0,
            'simpanan_wajib'    => $wajib,
            'simpanan_sukarela' => $sukarela,
            'total_simpanan'    => $total,
        ]);

        if ($wajib > 0) {
            RiwayatSimpanan::create([
                'karyawan_id' => $validated['karyawan_id'],
                'periode_id'  => null,
                'jenis'       => 'masuk',
                'kategori'    => 'wajib',
                'jumlah'      => $wajib,
                'keterangan'  => 'Setoran simpanan wajib karyawan',
                'tanggal'     => now(),
            ]);
        }

        if ($sukarela > 0) {
            RiwayatSimpanan::create([
                'karyawan_id' => $validated['karyawan_id'],
                'periode_id'  => null,
                'jenis'       => 'masuk',
                'kategori'    => 'sukarela',
                'jumlah'      => $sukarela,
                'keterangan'  => 'Setoran simpanan sukarela karyawan',
                'tanggal'     => now(),
            ]);
        }

        return redirect()->route('simpanan.show', $simpanan)
            ->with('success', 'Data simpanan berhasil disimpan.');
    }

    public function show(Simpanan $simpanan)
    {
        $simpanan->load('karyawan');
        $riwayat = RiwayatSimpanan::where('karyawan_id', $simpanan->karyawan_id)
            ->with('periode')
            ->latest()
            ->paginate(10);
        return view('simpanan.show', compact('simpanan', 'riwayat'));
    }

    public function edit(Simpanan $simpanan)
    {
        $simpanan->load('karyawan');
        return view('simpanan.edit', compact('simpanan'));
    }

    public function update(Request $request, Simpanan $simpanan)
    {
        $validated = $request->validate([
            'simpanan_wajib'    => 'nullable|numeric|min:0',
            'simpanan_sukarela' => 'nullable|numeric|min:0',
        ]);

        $simpanan->simpanan_wajib = (float) ($validated['simpanan_wajib'] ?? $simpanan->simpanan_wajib);
        $simpanan->simpanan_sukarela = (float) ($validated['simpanan_sukarela'] ?? $simpanan->simpanan_sukarela);
        $simpanan->total_simpanan = $simpanan->simpanan_pokok
            + $simpanan->simpanan_wajib
            + $simpanan->simpanan_sukarela;
        $simpanan->save();

        return redirect()->route('simpanan.show', $simpanan)
            ->with('success', 'Data simpanan berhasil diperbarui.');
    }

    public function destroy(Simpanan $simpanan)
    {
        $simpanan->delete();
        return redirect()->route('simpanan.index')
            ->with('success', 'Data simpanan berhasil dihapus.');
    }

    public function riwayat(Request $request)
    {
        $query = RiwayatSimpanan::with('karyawan', 'periode');

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        $riwayat   = $query->latest()->paginate(20)->withQueryString();
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        $periodes  = Periode::orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('simpanan.riwayat', compact('riwayat', 'karyawans', 'periodes'));
    }

    public function tambahSetor(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode_id'  => 'nullable|exists:periodes,id',
            'kategori'    => 'required|in:pokok,wajib,sukarela',
            'jumlah'      => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        RiwayatSimpanan::create([...$validated, 'jenis' => 'masuk']);

        // Update total simpanan
        $simpanan = Simpanan::firstOrCreate(['karyawan_id' => $validated['karyawan_id']]);
        $field = 'simpanan_' . $validated['kategori'];
        $simpanan->increment($field, $validated['jumlah']);
        $simpanan->increment('total_simpanan', $validated['jumlah']);

        return back()->with('success', 'Setoran simpanan berhasil dicatat.');
    }
}
