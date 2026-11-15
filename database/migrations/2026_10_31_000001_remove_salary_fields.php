<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('karyawans', 'gaji_pokok')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('gaji_pokok');
            });
        }

        if (Schema::hasColumn('potongans', 'gaji_bersih')) {
            Schema::table('potongans', function (Blueprint $table) {
                $table->dropColumn('gaji_bersih');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('karyawans', 'gaji_pokok')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->decimal('gaji_pokok', 15, 2)->default(0);
            });
        }

        if (!Schema::hasColumn('potongans', 'gaji_bersih')) {
            Schema::table('potongans', function (Blueprint $table) {
                $table->decimal('gaji_bersih', 15, 2)->default(0);
            });
        }
    }
};
