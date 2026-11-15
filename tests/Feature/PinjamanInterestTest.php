<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class PinjamanInterestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_interest_is_added_to_the_loan_balance_and_monthly_installment(): void
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

        $this->actingAs($user)
            ->post(route('pinjaman.store'), [
                'karyawan_id' => $karyawan->id,
                'jumlah_pinjaman' => 1000000,
                'bunga_persen' => 5,
                'tenor' => 10,
                'tanggal_pinjaman' => '2026-10-31',
            ])
            ->assertRedirect();

        $pinjaman = Pinjaman::where('karyawan_id', $karyawan->id)->firstOrFail();

        $this->assertSame('5.00', $pinjaman->bunga_persen);
        $this->assertSame('1050000.00', $pinjaman->sisa_pinjaman);
        $this->assertSame('105000.00', $pinjaman->cicilan_per_bulan);
        $this->assertSame(1050000.0, $pinjaman->total_pengembalian);
        $this->assertSame(50000.0, $pinjaman->jumlah_bunga);
    }

    public function test_interest_rate_cannot_exceed_one_hundred_percent(): void
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

        $this->actingAs($user)
            ->from(route('pinjaman.create'))
            ->post(route('pinjaman.store'), [
                'karyawan_id' => $karyawan->id,
                'jumlah_pinjaman' => 1000000,
                'bunga_persen' => 101,
                'tenor' => 10,
                'tanggal_pinjaman' => '2026-10-31',
            ])
            ->assertSessionHasErrors('bunga_persen');

        $this->assertDatabaseCount('pinjamans', 0);
    }
}
