<?php

namespace Tests\Feature;

use App\Mail\SlipPotonganMail;
use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\Potongan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SlipEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_slip_is_sent_to_the_employee_with_a_pdf_attachment(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        [$user, $karyawan, $periode] = $this->createSlipData();

        $this->actingAs($user)
            ->post(route('whatsapp.send-slip'), [
                'karyawan_id' => $karyawan->id,
                'periode_id' => $periode->id,
                'channel' => 'email',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertSent(SlipPotonganMail::class, function (SlipPotonganMail $mail) use ($karyawan) {
            $mail->build();

            $mail->assertSeeInHtml('Total potongan');
            $this->assertSame('rincian-potongan-' . $karyawan->nik . '.pdf', $mail->rawAttachments[0]['name']);
            $this->assertStringStartsWith('%PDF-', $mail->rawAttachments[0]['data']);

            return $mail->hasTo($karyawan->email);
        });
    }

    public function test_log_mailer_does_not_report_email_as_sent(): void
    {
        Mail::fake();
        config(['mail.default' => 'log']);

        [$user, $karyawan, $periode] = $this->createSlipData();

        $this->actingAs($user)
            ->post(route('whatsapp.send-slip'), [
                'karyawan_id' => $karyawan->id,
                'periode_id' => $periode->id,
                'channel' => 'email',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Pengiriman email belum aktif. Atur MAIL_MAILER=smtp dan konfigurasi SMTP pada file .env.');

        Mail::assertNothingOutgoing();
    }

    private function createSlipData(): array
    {
        $user = User::create([
            'name' => 'Operator Email',
            'username' => 'operatoremail',
            'email' => 'operator@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'KKEMAIL001',
            'nama' => 'Karyawan Email',
            'email' => 'karyawan@example.test',
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

        return [$user, $karyawan, $periode];
    }
}
