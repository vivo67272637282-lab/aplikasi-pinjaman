<?php

namespace Tests\Feature;

use App\Models\Kasbon;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasbonSettlementTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_kasbon_can_be_marked_as_fully_paid(): void
    {
        [$user, $kasbon] = $this->createKasbon('disetujui');

        $this->actingAs($user)
            ->post(route('kasbon.lunas', $kasbon))
            ->assertRedirect()
            ->assertSessionHas('success', "Kasbon {$kasbon->no_kasbon} ditandai sudah lunas.");

        $this->assertDatabaseHas('kasbons', [
            'id' => $kasbon->id,
            'status' => 'lunas',
            'sisa_kasbon' => 0,
        ]);
    }

    public function test_pending_kasbon_cannot_be_marked_as_paid(): void
    {
        [$user, $kasbon] = $this->createKasbon('pending');

        $this->actingAs($user)
            ->post(route('kasbon.lunas', $kasbon))
            ->assertStatus(422);

        $this->assertDatabaseHas('kasbons', [
            'id' => $kasbon->id,
            'status' => 'pending',
            'sisa_kasbon' => 50000,
        ]);
    }

    public function test_account_cannot_settle_another_accounts_kasbon(): void
    {
        [$owner, $kasbon] = $this->createKasbon('disetujui');
        $otherUser = User::create([
            'name' => 'Operator Lain',
            'username' => 'operatorlain',
            'email' => 'lain@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->actingAs($otherUser)
            ->post(route('kasbon.lunas', $kasbon))
            ->assertNotFound();

        $this->assertDatabaseHas('kasbons', [
            'id' => $kasbon->id,
            'user_id' => $owner->id,
            'status' => 'disetujui',
            'sisa_kasbon' => 50000,
        ]);
    }

    private function createKasbon(string $status): array
    {
        $user = User::create([
            'name' => 'Operator Kasbon',
            'username' => 'operator' . $status,
            'email' => $status . '@example.test',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'KK' . $user->id,
            'nama' => 'Karyawan Kasbon',
            'is_active' => true,
        ]);
        $kasbon = Kasbon::create([
            'user_id' => $user->id,
            'no_kasbon' => 'KSB-202611-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'karyawan_id' => $karyawan->id,
            'jumlah_kasbon' => 50000,
            'sisa_kasbon' => 50000,
            'tanggal_kasbon' => '2026-11-01',
            'status' => $status,
        ]);

        return [$user, $kasbon];
    }
}
