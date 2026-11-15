<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->decimal('simpanan_pokok', 15, 2)->default(0)->comment('Simpanan pokok satu kali saat masuk');
            $table->decimal('simpanan_wajib', 15, 2)->default(0)->comment('Simpanan wajib per bulan');
            $table->decimal('simpanan_sukarela', 15, 2)->default(0)->comment('Simpanan sukarela per bulan');
            $table->decimal('total_simpanan', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanans');
    }
};
