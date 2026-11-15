<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kasbon::where('user_id', auth()->id())->with('karyawan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kasbon', 'like', "%$search%")
                  ->orWhereHas('karyawan', fn($q2) => $q2->where('nama', 'like', "%$search%")
                      ->orWhere('nik', 'like', "%$search%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kasbons = $query->latest()->paginate(15)->withQueryString();

        return view('kasbon.index', compact('kasbons'));
    }

    public function create()
    {
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kasbon.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'    => 'required|exists:karyawans,id',
            'jumlah_kasbon'  => 'required|numeric|min:1',
            'tanggal_kasbon' => 'required|date',
            'keperluan'      => 'nullable|string',
            'catatan'        => 'nullable|string',
        ]);

        $kasbon = Kasbon::create([
            ...$validated,
            'no_kasbon'   => Kasbon::generateNomor(),
            'sisa_kasbon' => $validated['jumlah_kasbon'],
            'status'      => 'pending',
        ]);

        return redirect()->route('kasbon.show', $kasbon)
            ->with('success', "Kasbon {$kasbon->no_kasbon} berhasil diajukan.");
    }

    public function show(Kasbon $kasbon)
    {
        $kasbon->load('karyawan', 'disetujuiOleh');
        return view('kasbon.show', compact('kasbon'));
    }

    public function edit(Kasbon $kasbon)
    {
        if (!in_array($kasbon->status, ['pending'])) {
            return back()->with('error', 'Kasbon ini tidak dapat diedit.');
        }
        $karyawans = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kasbon.edit', compact('kasbon', 'karyawans'));
    }

    public function update(Request $request, Kasbon $kasbon)
    {
        $validated = $request->validate([
            'jumlah_kasbon'  => 'required|numeric|min:1',
            'tanggal_kasbon' => 'required|date',
            'keperluan'      => 'nullable|string',
            'catatan'        => 'nullable|string',
        ]);

        $kasbon->update([
            ...$validated,
            'sisa_kasbon' => $validated['jumlah_kasbon'],
        ]);

        return redirect()->route('kasbon.show', $kasbon)
            ->with('success', 'Data kasbon berhasil diperbarui.');
    }

    public function destroy(Kasbon $kasbon)
    {
        if ($kasbon->status === 'disetujui') {
            return back()->with('error', 'Kasbon yang sudah disetujui tidak dapat dihapus.');
        }
        $kasbon->delete();
        return redirect()->route('kasbon.index')
            ->with('success', 'Kasbon berhasil dihapus.');
    }

    public function setujui(Kasbon $kasbon)
    {
        if ($kasbon->status !== 'pending') {
            return back()->with('error', 'Kasbon ini bukan dalam status pending.');
        }

        $kasbon->update([
            'status'           => 'disetujui',
            'disetujui_oleh'   => auth()->id(),
            'tanggal_disetujui'=> now(),
        ]);

        return back()->with('success', "Kasbon {$kasbon->no_kasbon} berhasil disetujui.");
    }

    public function tolak(Request $request, Kasbon $kasbon)
    {
        $request->validate(['catatan' => 'nullable|string']);

        if ($kasbon->status !== 'pending') {
            return back()->with('error', 'Kasbon ini bukan dalam status pending.');
        }

        $kasbon->update([
            'status'  => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', "Kasbon {$kasbon->no_kasbon} telah ditolak.");
    }

    public function lunas(Kasbon $kasbon)
    {
        abort_unless($kasbon->status === 'disetujui', 422, 'Hanya kasbon yang disetujui yang dapat dilunasi.');

        DB::transaction(function () use ($kasbon) {
            $kasbon->update([
                'status' => 'lunas',
                'sisa_kasbon' => 0,
            ]);
        });

        return back()->with('success', "Kasbon {$kasbon->no_kasbon} ditandai sudah lunas.");
    }
}
