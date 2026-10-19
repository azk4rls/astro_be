<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_perjalanan_tugas', function (Blueprint $table) {
            $table->bigIncrements('id_pengajuan');
            $table->string('nomor_pengajuan', 50)->unique();
            $table->foreignId('id_karyawan')->constrained('karyawan', 'id_karyawan')->cascadeOnDelete();
            $table->foreignId('id_proyek')->nullable()->constrained('proyek', 'id_proyek')->nullOnDelete();
            $table->foreignId('id_tujuan')->constrained('tujuan', 'id_tujuan')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('maksud_tujuan');
            $table->decimal('estimasi_biaya', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->string('status', 50)->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perjalanan_tugas');
    }
};
