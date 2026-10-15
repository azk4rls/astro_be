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
        Schema::create('uang_muka', function (Blueprint $table) {
            $table->bigIncrements('id_uang_muka');
            $table->foreignId('id_pengajuan')->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->decimal('diajukan', 15, 2)->default(0);
            $table->decimal('disetujui', 15, 2)->default(0);
            $table->string('status', 30)->default('diajukan');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_muka');
    }
};
