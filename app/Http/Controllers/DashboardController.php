<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pinjaman;
use App\Models\Kasbon;
use App\Models\Simpanan;
use App\Models\Potongan;
use App\Models\Periode;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan   = Karyawan::where('user_id', auth()->id())->where('is_active', true)->count();
        $totalPinjaman   = Pinjaman::where('user_id', auth()->id())->where('status', 'disetujui')->sum('sisa_pinjaman');
        $totalKasbon     = Kasbon::where('user_id', auth()->id())->where('status', 'disetujui')->sum('sisa_kasbon');
        $totalSimpanan   = Simpanan::where('user_id', auth()->id())->sum('total_simpanan');

        $pinjamanPending = Pinjaman::where('user_id', auth()->id())->where('status', 'pending')->count();
        $kasbonPending   = Kasbon::where('user_id', auth()->id())->where('status', 'pending')->count();

        $periodeAktif    = Periode::where('user_id', auth()->id())->where('status', 'aktif')->first();

        $pinjamanTerbaru = Pinjaman::with('karyawan')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $kasbonTerbaru = Kasbon::with('karyawan')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalKaryawan',
            'totalPinjaman',
            'totalKasbon',
            'totalSimpanan',
            'pinjamanPending',
            'kasbonPending',
            'periodeAktif',
            'pinjamanTerbaru',
            'kasbonTerbaru'
        ));
    }
}
