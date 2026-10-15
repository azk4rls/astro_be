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
        Schema::create('delegasi', function (Blueprint $table) {
            $table->bigIncrements('id_delegasi');
            $table->foreignId('id_pemberi')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('id_penerima')->constrained('users', 'id')->cascadeOnDelete();
            $table->date('dari_tgl');
            $table->date('sampai_tgl');
            $table->string('alasan', 200)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegasi');
    }
};
