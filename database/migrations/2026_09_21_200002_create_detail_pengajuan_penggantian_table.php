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
        Schema::create('detail_pengajuan_penggantian', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->foreignId('id_penggantian')->constrained('pengajuan_penggantian', 'id_penggantian')->cascadeOnDelete();
            $table->foreignId('id_kategori')->constrained('kategori_pengeluaran', 'id_kategori')->cascadeOnDelete();
            $table->date('tanggal_pengeluaran');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('bukti_file', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengajuan_penggantian');
    }
};
