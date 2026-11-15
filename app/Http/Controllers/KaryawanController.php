<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%$search%")
                  ->orWhere('nama', 'like', "%$search%")
                  ->orWhere('jabatan', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $karyawans = $query->latest()->paginate(15)->withQueryString();

        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'jabatan'       => 'nullable|string|max:100',
            'email'         => 'nullable|email|max:255',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_masuk' => 'nullable|date',
            'is_active'     => 'boolean',
        ]);

        $validated['nik'] = Karyawan::generateKodeKaryawan(auth()->id());
        $validated['is_active'] = $request->boolean('is_active', true);

        $karyawan = Karyawan::create($validated);

        // Auto-create simpanan record
        Simpanan::create([
            'karyawan_id'       => $karyawan->id,
            'simpanan_pokok'    => 0,
            'simpanan_wajib'    => 0,
            'simpanan_sukarela' => 0,
            'total_simpanan'    => 0,
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', "Karyawan {$karyawan->nama} berhasil ditambahkan.");
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load([
            'pinjamans' => fn($q) => $q->latest()->take(5),
            'kasbons'   => fn($q) => $q->latest()->take(5),
            'simpanan',
            'potongans.periode',
        ]);
        return view('karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'jabatan'       => 'nullable|string|max:100',
            'email'         => 'nullable|email|max:255',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_masuk' => 'nullable|date',
            'is_active'     => 'boolean',
        ]);

        $validated['nik'] = $karyawan->nik;
        $validated['is_active'] = $request->boolean('is_active');

        $karyawan->update($validated);

        return redirect()->route('karyawan.index')
            ->with('success', "Data karyawan {$karyawan->nama} berhasil diperbarui.");
    }

    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->pinjamans()->whereIn('status', ['pending', 'disetujui'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus karyawan yang masih memiliki pinjaman aktif.');
        }

        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', "Karyawan {$karyawan->nama} berhasil dihapus.");
    }
}
