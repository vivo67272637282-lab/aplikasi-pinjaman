<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::where('user_id', auth()->id())->orderByDesc('tahun')->orderByDesc('bulan')->paginate(15);
        return view('periode.index', compact('periodes'));
    }

    public function create()
    {
        return view('periode.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:100',
            'bulan'           => 'required|integer|min:1|max:12',
            'tahun'           => 'required|integer|min:2000|max:2100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,selesai,draft',
            'keterangan'      => 'nullable|string',
        ]);

        // Hanya satu periode aktif per akun
        if ($validated['status'] === 'aktif') {
            Periode::where('user_id', auth()->id())->where('status', 'aktif')->update(['status' => 'selesai']);
        }

        $periode = Periode::create($validated);

        return redirect()->route('periode.show', $periode)
            ->with('success', "Periode {$periode->nama} berhasil ditambahkan.");
    }

    public function show(Periode $periode)
    {
        abort_if($periode->user_id !== auth()->id(), 403);
        $periode->load('potongans.karyawan');
        $totalPotongan = $periode->potongans->sum('total_potongan');
        return view('periode.show', compact('periode', 'totalPotongan'));
    }

    public function edit(Periode $periode)
    {
        abort_if($periode->user_id !== auth()->id(), 403);
        return view('periode.edit', compact('periode'));
    }

    public function update(Request $request, Periode $periode)
    {
        abort_if($periode->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'nama'            => 'required|string|max:100',
            'bulan'           => 'required|integer|min:1|max:12',
            'tahun'           => 'required|integer|min:2000|max:2100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,selesai,draft',
            'keterangan'      => 'nullable|string',
        ]);

        if ($validated['status'] === 'aktif' && $periode->status !== 'aktif') {
            Periode::where('user_id', auth()->id())->where('status', 'aktif')->where('id', '!=', $periode->id)->update(['status' => 'selesai']);
        }

        $periode->update($validated);

        return redirect()->route('periode.show', $periode)
            ->with('success', "Periode {$periode->nama} berhasil diperbarui.");
    }

    public function destroy(Periode $periode)
    {
        abort_if($periode->user_id !== auth()->id(), 403);
        if ($periode->potongans()->exists()) {
            return back()->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data potongan.');
        }

        $periode->delete();

        return redirect()->route('periode.index')
            ->with('success', "Periode {$periode->nama} berhasil dihapus.");
    }
}
