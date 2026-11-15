<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('no_pinjaman')->unique();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor')->comment('Jumlah cicilan bulan');
            $table->decimal('cicilan_per_bulan', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->date('tanggal_pinjaman');
            $table->text('keperluan')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'lunas'])->default('pending');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
