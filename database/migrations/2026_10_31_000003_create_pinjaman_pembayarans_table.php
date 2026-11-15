<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pinjaman_pembayarans')) {
            Schema::create('pinjaman_pembayarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pinjaman_id')->constrained('pinjamans')->cascadeOnDelete();
                $table->unsignedSmallInteger('pembayaran_ke');
                $table->decimal('jumlah_bayar', 15, 2);
                $table->date('tanggal_bayar');
                $table->timestamps();
                $table->unique(['pinjaman_id', 'pembayaran_ke']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman_pembayarans');
    }
};
