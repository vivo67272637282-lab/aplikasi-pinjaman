<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Kasbon;
use App\Models\Periode;
use App\Models\Pinjaman;
use App\Models\Potongan;
use App\Models\Simpanan;
use App\Models\WaLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_message_uses_fonnte_and_logs_success(): void
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response([
                'detail' => 'success! message in queue',
                'status' => true,
                'target' => ['6281234567890'],
            ], 200),
        ]);
        config(['services.fonnte.token' => 'test-fonnte-token']);
        [$user, $karyawan] = $this->createEmployee('081234567890');

        $this->actingAs($user)
            ->post(route('whatsapp.send'), [
                'karyawan_id' => $karyawan->id,
                'pesan' => 'Pesan uji',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization')
                && $request['target'] === '6281234567890'
                && $request['message'] === 'Pesan uji'
                && $request['countryCode'] === '0';
        });

        $this->assertDatabaseHas('wa_logs', [
            'karyawan_id' => $karyawan->id,
            'no_tujuan' => '081234567890',
            'status' => 'terkirim',
        ]);
    }

    public function test_missing_fonnte_token_is_logged_as_failure(): void
    {
        config(['services.fonnte.token' => null]);
        [$user, $karyawan] = $this->createEmployee('081234567890');

        $this->actingAs($user)
            ->post(route('whatsapp.send'), [
                'karyawan_id' => $karyawan->id,
                'pesan' => 'Pesan uji',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        Http::assertNothingSent();
        $this->assertDatabaseHas('wa_logs', [
            'karyawan_id' => $karyawan->id,
            'status' => 'gagal',
            'response' => 'Fonnte belum dikonfigurasi. Isi FONNTE_TOKEN di file .env.',
        ]);
    }

    public function test_fonnte_api_error_is_not_reported_as_success(): void
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response([
                'status' => false,
                'reason' => 'token invalid',
            ], 200),
        ]);
        config(['services.fonnte.token' => 'invalid-test-token']);
        [$user, $karyawan] = $this->createEmployee('081234567890');

        $this->actingAs($user)
            ->post(route('whatsapp.send'), [
                'karyawan_id' => $karyawan->id,
                'pesan' => 'Pesan uji',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('wa_logs', [
            'karyawan_id' => $karyawan->id,
            'status' => 'gagal',
            'response' => '{"status":false,"reason":"token invalid"}',
        ]);
    }

    public function test_slip_summary_is_sent_through_fonnte(): void
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);
        config(['services.fonnte.token' => 'test-fonnte-token']);
        [$user, $karyawan] = $this->createEmployee('081234567890');
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
        $pinjaman = Pinjaman::create([
            'user_id' => $user->id,
            'no_pinjaman' => 'PIN-202610-TEST',
            'karyawan_id' => $karyawan->id,
            'jumlah_pinjaman' => 200000,
            'bunga_persen' => 0,
            'tenor' => 4,
            'cicilan_per_bulan' => 50000,
            'sisa_pinjaman' => 150000,
            'tanggal_pinjaman' => '2026-10-01',
            'status' => 'disetujui',
        ]);
        $pinjaman->pembayarans()->create([
            'pembayaran_ke' => 1,
            'jumlah_bayar' => 50000,
            'tanggal_bayar' => '2026-10-31',
        ]);
        Kasbon::create([
            'user_id' => $user->id,
            'no_kasbon' => 'KSB-UNPAID-001',
            'karyawan_id' => $karyawan->id,
            'jumlah_kasbon' => 40000,
            'sisa_kasbon' => 25000,
            'tanggal_kasbon' => '2026-10-01',
            'status' => 'disetujui',
        ]);
        Kasbon::create([
            'user_id' => $user->id,
            'no_kasbon' => 'KSB-PAID-001',
            'karyawan_id' => $karyawan->id,
            'jumlah_kasbon' => 30000,
            'sisa_kasbon' => 0,
            'tanggal_kasbon' => '2026-09-01',
            'status' => 'lunas',
        ]);
        Simpanan::create([
            'user_id' => $user->id,
            'karyawan_id' => $karyawan->id,
            'simpanan_pokok' => 10000,
            'simpanan_wajib' => 20000,
            'simpanan_sukarela' => 30000,
            'total_simpanan' => 60000,
        ]);

        $this->actingAs($user)
            ->post(route('whatsapp.send-slip'), [
                'karyawan_id' => $karyawan->id,
                'periode_id' => $periode->id,
                'channel' => 'wa',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Http::assertSent(function (Request $request): bool {
            return $request->isMultipart()
                && $request->hasFile('file', null, 'rincian-potongan-operatorwa001.pdf');
        });
        $message = WaLog::where('karyawan_id', $karyawan->id)->where('tipe', 'slip')->value('pesan');
        $this->assertStringContainsString('Total potongan: Rp 100.000', $message);
        $this->assertStringContainsString('Cicilan ke-1 (31/10/2026): Rp 50.000', $message);
        $this->assertStringContainsString('Sisa pinjaman: Rp 150.000', $message);
        $this->assertStringContainsString('KSB-UNPAID-001: sisa Rp 25.000 dari Rp 40.000', $message);
        $this->assertStringNotContainsString('KSB-PAID-001', $message);
        $this->assertStringContainsString('Total uang simpanan: Rp 60.000', $message);
        $this->assertDatabaseHas('wa_logs', [
            'karyawan_id' => $karyawan->id,
            'tipe' => 'slip',
            'status' => 'terkirim',
        ]);
    }

    public function test_broadcast_sends_only_to_the_authenticated_users_employees(): void
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);
        config(['services.fonnte.token' => 'test-fonnte-token']);
        [$user] = $this->createEmployee('081234567890');
        $this->createEmployee('081234567891', 'Operator Kedua');

        $this->actingAs($user)
            ->post(route('whatsapp.broadcast'), ['pesan' => 'Pengumuman'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Broadcast berhasil dikirim ke 1 karyawan.');

        Http::assertSentCount(1);
        $this->assertSame(1, WaLog::where('user_id', $user->id)->where('status', 'terkirim')->count());
    }

    private function createEmployee(string $phone, string $username = 'operatorwa'): array
    {
        $user = User::create([
            'name' => 'Operator WhatsApp',
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => $username . '001',
            'nama' => 'Karyawan WhatsApp',
            'no_wa' => $phone,
            'is_active' => true,
        ]);

        return [$user, $karyawan];
    }
}
