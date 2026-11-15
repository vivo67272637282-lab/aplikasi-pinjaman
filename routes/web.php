<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PotonganController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\SlipController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password/change', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [LoginController::class, 'changePassword'])->name('password.update');
    Route::get('settings/company', [CompanySettingController::class, 'edit'])->name('company-settings.edit');
    Route::put('settings/company', [CompanySettingController::class, 'update'])->name('company-settings.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('slip/print-all', [SlipController::class, 'printAll'])->name('slip.print-all');
    Route::get('slip/{potongan}', [SlipController::class, 'show'])->name('slip.show');
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('kasbon', KasbonController::class);
    Route::resource('pinjaman', PinjamanController::class);
    Route::resource('simpanan', SimpananController::class);
    Route::resource('periode', PeriodeController::class);
    Route::resource('potongan', PotonganController::class);
    Route::resource('slip', SlipController::class)->only(['index']);

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::resource('import', ImportController::class)->except(['show', 'edit', 'update']);
    Route::get('import/mapping', [ImportController::class, 'mapping'])->name('import.mapping');

    Route::get('whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::post('whatsapp/send', [WhatsAppController::class, 'send'])->name('whatsapp.send');
    Route::post('whatsapp/send-slip', [WhatsAppController::class, 'sendSlip'])->name('whatsapp.send-slip');
    Route::post('whatsapp/broadcast', [WhatsAppController::class, 'sendBroadcast'])->name('whatsapp.broadcast');
    Route::get('whatsapp/logs', [WhatsAppController::class, 'logs'])->name('whatsapp.logs');

    Route::post('kasbon/{kasbon}/setujui', [KasbonController::class, 'setujui'])->name('kasbon.setujui');
    Route::post('kasbon/{kasbon}/tolak', [KasbonController::class, 'tolak'])->name('kasbon.tolak');
    Route::post('kasbon/{kasbon}/lunas', [KasbonController::class, 'lunas'])->name('kasbon.lunas');
    Route::post('pinjaman/{pinjaman}/setujui', [PinjamanController::class, 'setujui'])->name('pinjaman.setujui');
    Route::post('pinjaman/{pinjaman}/tolak', [PinjamanController::class, 'tolak'])->name('pinjaman.tolak');
    Route::post('pinjaman/{pinjaman}/bayar', [PinjamanController::class, 'bayar'])->name('pinjaman.bayar');
    Route::post('potongan/generate', [PotonganController::class, 'generate'])->name('potongan.generate');
    Route::get('simpanan/riwayat', [SimpananController::class, 'riwayat'])->name('simpanan.riwayat');
    Route::post('simpanan/tambah-setor', [SimpananController::class, 'tambahSetor'])->name('simpanan.tambahSetor');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/rekap-potongan', [LaporanController::class, 'rekapPotongan'])->name('laporan.rekap-potongan');
    Route::get('laporan/pdf-potongan', [LaporanController::class, 'pdfPotongan'])->name('laporan.pdf-potongan');
    Route::get('laporan/rekap-pinjaman', [LaporanController::class, 'rekapPinjaman'])->name('laporan.rekap-pinjaman');
    Route::get('laporan/pdf-pinjaman', [LaporanController::class, 'pdfPinjaman'])->name('laporan.pdf-pinjaman');
    Route::get('laporan/rekap-kasbon', [LaporanController::class, 'rekapKasbon'])->name('laporan.rekap-kasbon');
    Route::get('laporan/pdf-kasbon', [LaporanController::class, 'pdfKasbon'])->name('laporan.pdf-kasbon');
    Route::get('laporan/rekap-simpanan', [LaporanController::class, 'rekapSimpanan'])->name('laporan.rekap-simpanan');
    Route::get('laporan/pdf-simpanan', [LaporanController::class, 'pdfSimpanan'])->name('laporan.pdf-simpanan');

    Route::get('slip/{potongan}/pdf', [SlipController::class, 'pdf'])->name('slip.pdf');
    Route::post('slip/{potongan}/kirim-wa', [SlipController::class, 'kirimWa'])->name('slip.kirim-wa');
});
