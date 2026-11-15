<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Potongan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalaryRemovalTest extends TestCase
{
    public function test_migration_drops_salary_columns_from_existing_tables(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge();

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
        });
        Schema::create('potongans', function (Blueprint $table) {
            $table->id();
            $table->decimal('gaji_bersih', 15, 2)->default(0);
        });

        $migration = require base_path('database/migrations/2026_10_31_000001_remove_salary_fields.php');
        $migration->up();

        $this->assertFalse(Schema::hasColumn('karyawans', 'gaji_pokok'));
        $this->assertFalse(Schema::hasColumn('potongans', 'gaji_bersih'));
    }

    public function test_salary_fields_are_no_longer_mass_assignable_or_calculated(): void
    {
        $this->assertNotContains('gaji_pokok', (new Karyawan())->getFillable());
        $this->assertNotContains('gaji_bersih', (new Potongan())->getFillable());

        $potongan = new Potongan([
            'potongan_pinjaman' => 100,
            'potongan_kasbon' => 50,
            'potongan_simpanan_wajib' => 25,
            'potongan_simpanan_sukarela' => 15,
            'potongan_lainnya' => 10,
        ]);

        $potongan->hitungTotal();

        $this->assertEquals(200, $potongan->total_potongan);
    }
}
