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
        Schema::create('pengajuan_penggantian', function (Blueprint $table) {
            $table->bigIncrements('id_penggantian');
            $table->string('nomor_penggantian', 50)->unique();
            $table->foreignId('id_pengajuan')->unique()->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0);
            $table->string('kondisi_penggantian', 50)->default('sesuai');
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
        Schema::dropIfExists('pengajuan_penggantian');
    }
};
