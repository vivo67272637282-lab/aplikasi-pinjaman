<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryawanCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_kode_karyawan_in_sequence_with_kk_prefix(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => 'secret123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'KK001',
            'nama' => 'Karyawan Pertama',
            'is_active' => true,
        ]);

        $this->assertSame('KK002', Karyawan::generateKodeKaryawan($user->id));
        $this->assertSame('KK001', Karyawan::generateKodeKaryawan($user->id + 1));
    }

    public function test_it_keeps_jabatan_and_excludes_departemen_from_mass_assignment(): void
    {
        $fillable = (new Karyawan())->getFillable();

        $this->assertContains('jabatan', $fillable);
        $this->assertNotContains('departemen', $fillable);
    }
}
