<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function indexExists(string $table, string $indexName): bool
    {
        return Schema::hasIndex($table, $indexName);
    }

    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
                $tableBlueprint->dropUnique($indexName);
            });
        }
    }

    public function up(): void
    {
        $tables = ['karyawans', 'pinjamans', 'kasbons', 'simpanans', 'potongans', 'riwayat_simpanans', 'wa_logs'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
                });
            }
        }

        $defaultUserId = DB::table('users')->value('id');
        if ($defaultUserId) {
            foreach ($tables as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                    DB::table($table)->whereNull('user_id')->update(['user_id' => $defaultUserId]);
                }
            }
        }

        if (Schema::hasTable('karyawans') && ! $this->indexExists('karyawans', 'karyawans_user_id_nik_unique')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->unique(['user_id', 'nik']);
            });
        }

        if (Schema::hasTable('pinjamans') && ! $this->indexExists('pinjamans', 'pinjamans_user_id_no_pinjaman_unique')) {
            Schema::table('pinjamans', function (Blueprint $table) {
                $table->unique(['user_id', 'no_pinjaman']);
            });
        }

        if (Schema::hasTable('kasbons') && ! $this->indexExists('kasbons', 'kasbons_user_id_no_kasbon_unique')) {
            Schema::table('kasbons', function (Blueprint $table) {
                $table->unique(['user_id', 'no_kasbon']);
            });
        }

        if (Schema::hasTable('simpanans') && ! $this->indexExists('simpanans', 'simpanans_user_id_karyawan_id_unique')) {
            Schema::table('simpanans', function (Blueprint $table) {
                $table->unique(['user_id', 'karyawan_id']);
            });
        }

        if (Schema::hasTable('potongans') && ! $this->indexExists('potongans', 'potongans_user_id_karyawan_id_periode_id_unique')) {
            Schema::table('potongans', function (Blueprint $table) {
                $table->unique(['user_id', 'karyawan_id', 'periode_id']);
            });
        }
    }

    public function down(): void
    {
        foreach (['karyawans', 'pinjamans', 'kasbons', 'simpanans', 'potongans', 'riwayat_simpanans', 'wa_logs'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropConstrainedForeignId('user_id');
                });
            }
        }
    }
};
