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
        Schema::create('pengeluaran_perjalanan', function (Blueprint $table) {
            $table->bigIncrements('id_pengeluaran');
            $table->unsignedBigInteger('id_pengajuan')->nullable()->index();
            $table->foreignId('id_kategori')->constrained('kategori_pengeluaran', 'id_kategori')->cascadeOnDelete();
            $table->date('tanggal_pengeluaran');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->string('metode_pembayaran', 50);
            $table->boolean('sudah_diganti')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_perjalanan');
    }
};
