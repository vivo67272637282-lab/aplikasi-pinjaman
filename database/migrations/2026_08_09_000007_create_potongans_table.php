<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('restrict');
            $table->decimal('potongan_pinjaman', 15, 2)->default(0);
            $table->decimal('potongan_kasbon', 15, 2)->default(0);
            $table->decimal('potongan_simpanan_wajib', 15, 2)->default(0);
            $table->decimal('potongan_simpanan_sukarela', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->string('keterangan_lainnya')->nullable();
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potongans');
    }
};
