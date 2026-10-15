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
        Schema::create('settlement', function (Blueprint $table) {
            $table->bigIncrements('id_settlement');
            $table->foreignId('id_pengajuan')->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->string('status', 30)->default('diajukan');
            $table->decimal('total_realisasi', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement');
    }
};
