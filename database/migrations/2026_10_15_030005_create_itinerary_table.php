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
        Schema::create('itinerary', function (Blueprint $table) {
            $table->bigIncrements('id_itinerary');
            $table->foreignId('id_pengajuan')->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->dateTime('waktu');
            $table->string('aktivitas', 200);
            $table->string('lokasi', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary');
    }
};
