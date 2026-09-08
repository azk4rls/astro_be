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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->bigIncrements('id_karyawan');
            $table->string('nik', 50)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('email', 150)->unique();
            $table->string('telepon', 30)->nullable();
            $table->foreignId('id_departemen')->constrained('departemen', 'id_departemen')->cascadeOnDelete();
            $table->string('peran', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
