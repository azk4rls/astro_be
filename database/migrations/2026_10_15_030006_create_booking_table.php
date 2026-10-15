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
        Schema::create('booking', function (Blueprint $table) {
            $table->bigIncrements('id_booking');
            $table->foreignId('id_pengajuan')->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')->cascadeOnDelete();
            $table->string('jenis', 50);
            $table->string('detail', 200);
            $table->string('nomor_ref', 100)->nullable();
            $table->string('vendor', 150)->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
