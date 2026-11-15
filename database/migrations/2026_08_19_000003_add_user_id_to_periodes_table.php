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

    public function up(): void
    {
        Schema::table('periodes', function (Blueprint $table) {
            if (!Schema::hasColumn('periodes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });

        $defaultUserId = DB::table('users')->value('id');
        if ($defaultUserId) {
            DB::table('periodes')->whereNull('user_id')->update(['user_id' => $defaultUserId]);
        }

        if (! $this->indexExists('periodes', 'periodes_user_id_bulan_tahun_unique')) {
            Schema::table('periodes', function (Blueprint $table) {
                $table->unique(['user_id', 'bulan', 'tahun'], 'periodes_user_id_bulan_tahun_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('periodes', 'periodes_user_id_bulan_tahun_unique')) {
            Schema::table('periodes', function (Blueprint $table) {
                $table->dropUnique('periodes_user_id_bulan_tahun_unique');
            });
        }

        if (Schema::hasColumn('periodes', 'user_id')) {
            Schema::table('periodes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
