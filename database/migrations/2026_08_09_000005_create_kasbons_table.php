<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->string('no_kasbon')->unique();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->decimal('jumlah_kasbon', 15, 2);
            $table->decimal('sisa_kasbon', 15, 2);
            $table->date('tanggal_kasbon');
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
        Schema::dropIfExists('kasbons');
    }
};
