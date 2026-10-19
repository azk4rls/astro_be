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
        Schema::create('detail_pengajuan_perjalanan', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->foreignId('id_pengajuan')->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->foreignId('id_kategori')->constrained('kategori_pengeluaran', 'id_kategori')->cascadeOnDelete();
            $table->decimal('estimasi_biaya', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengajuan_perjalanan');
    }
};
