<?php

namespace Tests\Feature;

use App\Models\Kasbon;
use App\Models\Karyawan;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_account_cannot_see_previous_accounts_data(): void
    {
        $previousUser = User::create([
            'name' => 'Akun Sebelumnya',
            'username' => 'akunlama',
            'email' => 'lama@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);
        $employee = Karyawan::create([
            'user_id' => $previousUser->id,
            'nik' => 'KK001',
            'nama' => 'Karyawan Akun Lama',
            'is_active' => true,
        ]);
        Pinjaman::create([
            'user_id' => $previousUser->id,
            'no_pinjaman' => 'PIN-202611-0001',
            'karyawan_id' => $employee->id,
            'jumlah_pinjaman' => 100000,
            'bunga_persen' => 0,
            'tenor' => 2,
            'cicilan_per_bulan' => 50000,
            'sisa_pinjaman' => 100000,
            'tanggal_pinjaman' => '2026-11-01',
            'status' => 'pending',
        ]);
        Kasbon::create([
            'user_id' => $previousUser->id,
            'no_kasbon' => 'KSB-202611-0001',
            'karyawan_id' => $employee->id,
            'jumlah_kasbon' => 50000,
            'sisa_kasbon' => 50000,
            'tanggal_kasbon' => '2026-11-01',
            'status' => 'pending',
        ]);

        $this->post(route('register.submit'), [
            'name' => 'Akun Baru',
            'username' => 'akunbaru',
            'email' => 'baru@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('pinjamanTerbaru', fn($items) => $items->isEmpty())
            ->assertViewHas('kasbonTerbaru', fn($items) => $items->isEmpty())
            ->assertViewHas('totalKaryawan', 0)
            ->assertDontSee('Karyawan Akun Lama');

        $this->get(route('karyawan.index'))
            ->assertOk()
            ->assertDontSee('Karyawan Akun Lama');

        $this->get(route('karyawan.show', $employee))
            ->assertNotFound();

        $this->assertSame(0, Karyawan::count());
        $this->assertSame(0, Pinjaman::count());
        $this->assertSame(0, Kasbon::count());
    }

    public function test_new_records_are_owned_by_the_authenticated_account(): void
    {
        $user = User::create([
            'name' => 'Operator',
            'username' => 'operatorbaru',
            'email' => 'operator@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->actingAs($user);
        $employee = Karyawan::create([
            'nik' => 'KK001',
            'nama' => 'Karyawan Baru',
            'is_active' => true,
        ]);

        $this->assertSame($user->id, $employee->user_id);
    }
}
