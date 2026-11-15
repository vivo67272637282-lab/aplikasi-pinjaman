<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinjamanPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_it_records_installments_in_sequence_and_closes_the_loan_when_paid(): void
    {
        [$user, $pinjaman] = $this->createApprovedLoan(1000, 3, 333.33);
        $this->actingAs($user);

        foreach ([
            ['number' => 1, 'amount' => '333.33', 'balance' => '666.67'],
            ['number' => 2, 'amount' => '333.33', 'balance' => '333.34'],
            ['number' => 3, 'amount' => '333.34', 'balance' => '0.00'],
        ] as $payment) {
            $this->post(route('pinjaman.bayar', $pinjaman), [
                'tanggal_bayar' => '2026-10-31',
            ])->assertRedirect(route('pinjaman.show', $pinjaman));

            $pinjaman->refresh();
            $this->assertSame($payment['balance'], $pinjaman->sisa_pinjaman);
            $this->assertDatabaseHas('pinjaman_pembayarans', [
                'pinjaman_id' => $pinjaman->id,
                'pembayaran_ke' => $payment['number'],
                'jumlah_bayar' => $payment['amount'],
            ]);
        }

        $this->assertSame('lunas', $pinjaman->status);
        $this->assertSame(3, $pinjaman->jumlah_pembayaran);
    }

    public function test_pending_loan_cannot_accept_a_payment(): void
    {
        [$user, $pinjaman] = $this->createApprovedLoan(1000, 2, 500);
        $pinjaman->update(['status' => 'pending']);

        $this->actingAs($user)
            ->post(route('pinjaman.bayar', $pinjaman), [
                'tanggal_bayar' => '2026-10-31',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('pinjaman_pembayarans', 0);
    }

    private function createApprovedLoan(float $total, int $tenor, float $installment): array
    {
        $user = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => 'secret123',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'KK001',
            'nama' => 'Karyawan Test',
            'is_active' => true,
        ]);
        $pinjaman = Pinjaman::create([
            'user_id' => $user->id,
            'no_pinjaman' => 'PIN-202610-0001',
            'karyawan_id' => $karyawan->id,
            'jumlah_pinjaman' => $total,
            'bunga_persen' => 0,
            'tenor' => $tenor,
            'cicilan_per_bulan' => $installment,
            'sisa_pinjaman' => $total,
            'tanggal_pinjaman' => '2026-10-01',
            'status' => 'disetujui',
        ]);

        return [$user, $pinjaman];
    }
}
