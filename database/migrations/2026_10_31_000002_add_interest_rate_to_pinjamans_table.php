<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pinjamans', 'bunga_persen')) {
            Schema::table('pinjamans', function (Blueprint $table) {
                $table->decimal('bunga_persen', 5, 2)->default(0)->after('jumlah_pinjaman');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pinjamans', 'bunga_persen')) {
            Schema::table('pinjamans', function (Blueprint $table) {
                $table->dropColumn('bunga_persen');
            });
        }
    }
};
