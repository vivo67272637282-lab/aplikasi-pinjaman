<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\Pinjaman;
use App\Models\Potongan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SlipPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_pdf_headers_use_the_configured_company_name(): void
    {
        config([
            'company.name' => 'Koperasi Nusantara',
            'company.logo' => 'images/logo-perusahaan.png',
        ]);
        \App\Models\CompanySetting::create([
            'user_id' => User::create([
                'name' => 'Operator PDF',
                'username' => 'operatorpdf',
                'email' => 'operatorpdf@test.com',
                'password' => 'secret123',
                'role' => 'operator',
                'is_active' => true,
            ])->id,
            'company_name' => 'Koperasi Nusantara',
            'company_address' => 'Jl. Merdeka No. 10, Jakarta',
        ]);
        $this->actingAs(User::where('username', 'operatorpdf')->firstOrFail());

        $periode = new Periode(['nama' => 'Oktober 2026']);
        $reports = [
            'laporan.pdf-potongan' => ['data' => collect(), 'periode' => $periode],
            'laporan.pdf-pinjaman' => ['data' => collect()],
            'laporan.pdf-kasbon' => ['data' => collect()],
            'laporan.pdf-simpanan' => ['data' => collect(), 'totalSimpanan' => 0],
        ];

        foreach ($reports as $template => $data) {
            $html = view($template, $data)->render();

            $this->assertStringContainsString('Koperasi Nusantara', $html);
            $this->assertStringContainsString('Jl. Merdeka No. 10, Jakarta', $html);
            $this->assertStringNotContainsString('<img', $html);
        }
    }

    public function test_bulk_potongan_pdf_route_returns_a_pdf(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Operator Test',
            'username' => 'operatortest',
            'email' => 'operator@test.com',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);
        $logoPath = UploadedFile::fake()->image('logo.png')->store('company-logos', 'public');
        \App\Models\CompanySetting::create([
            'user_id' => $user->id,
            'company_name' => 'Koperasi Uji',
            'company_address' => 'Jl. Contoh No. 1',
            'logo_path' => $logoPath,
        ]);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'KK001',
            'nama' => 'Karyawan Test',
            'is_active' => true,
        ]);
        $periode = Periode::create([
            'user_id' => $user->id,
            'nama' => 'Oktober 2026',
            'bulan' => 10,
            'tahun' => 2026,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
            'status' => 'aktif',
        ]);
        Potongan::create([
            'user_id' => $user->id,
            'karyawan_id' => $karyawan->id,
            'periode_id' => $periode->id,
            'potongan_pinjaman' => 100000,
            'total_potongan' => 100000,
        ]);
        $potongan = Potongan::firstOrFail();
        $pinjaman = Pinjaman::create([
            'user_id' => $user->id,
            'no_pinjaman' => 'PIN-202610-0001',
            'karyawan_id' => $karyawan->id,
            'jumlah_pinjaman' => 90000,
            'bunga_persen' => 0,
            'tenor' => 3,
            'cicilan_per_bulan' => 30000,
            'sisa_pinjaman' => 30000,
            'tanggal_pinjaman' => '2026-10-01',
            'status' => 'disetujui',
        ]);
        $pinjaman->pembayarans()->createMany([
            ['pembayaran_ke' => 1, 'jumlah_bayar' => 30000, 'tanggal_bayar' => '2026-10-10'],
            ['pembayaran_ke' => 2, 'jumlah_bayar' => 30000, 'tanggal_bayar' => '2026-10-20'],
        ]);

        $this->actingAs($user)
            ->get(route('potongan.create'))
            ->assertOk()
            ->assertSee(route('potongan.store'), false);

        $this->get(route('slip.show', $potongan))
            ->assertOk()
            ->assertSee('Rincian Potongan');

        $potongan->load('karyawan.pinjamans.pembayarans', 'periode');
        $slipHtml = view('slip.pdf', compact('potongan'))->render();
        $this->assertStringContainsString('Koperasi Uji', $slipHtml);
        $this->assertStringContainsString('Jl. Contoh No. 1', $slipHtml);
        $this->assertStringContainsString('data:image/png;base64,', $slipHtml);
        $this->assertStringContainsString('2 dari 3', $slipHtml);
        $this->assertStringContainsString('60.000', $slipHtml);
        $this->assertStringContainsString('30.000', $slipHtml);

        $slipPdf = $this->get(route('slip.pdf', $potongan));
        $slipPdf
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $slipPdf->getContent());

        $response = $this
            ->get(route('slip.print-all', ['periode_id' => $periode->id]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        $printAllHtml = view('slip.print-all', [
            'potongans' => Potongan::with('karyawan.pinjamans.pembayarans')->get(),
            'periode' => $periode,
        ])->render();
        $this->assertStringContainsString('Koperasi Uji', $printAllHtml);
        $this->assertStringContainsString('Jl. Contoh No. 1', $printAllHtml);
        $this->assertStringContainsString('data:image/png;base64,', $printAllHtml);
        $this->assertStringContainsString('2 dari 3', $printAllHtml);
        $this->assertStringContainsString('60.000', $printAllHtml);
        $this->assertStringContainsString('30.000', $printAllHtml);

        $potonganData = Potongan::with('karyawan.pinjamans.pembayarans')->get();
        $potonganReportHtml = view('laporan.pdf-potongan', [
            'data' => $potonganData,
            'periode' => $periode,
        ])->render();
        $this->assertStringContainsString('2 dari 3 pembayaran', $potonganReportHtml);
        $this->assertStringContainsString('terbayar Rp 60.000', $potonganReportHtml);
        $this->assertStringContainsString('sisa Rp 30.000', $potonganReportHtml);

        $loanData = Pinjaman::with(['karyawan', 'pembayarans'])->get();
        $loanReportHtml = view('laporan.pdf-pinjaman', ['data' => $loanData])->render();
        $this->assertStringContainsString('2 dari 3', $loanReportHtml);
        $this->assertStringContainsString('60.000', $loanReportHtml);
        $this->assertStringContainsString('30.000', $loanReportHtml);

        $this->get(route('laporan.pdf-potongan', ['periode_id' => $periode->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->get(route('laporan.pdf-pinjaman'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
