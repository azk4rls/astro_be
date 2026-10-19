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
        Schema::create('dokumen_pendukung', function (Blueprint $table) {
            $table->bigIncrements('id_dokumen');
            $table->foreignId('id_pengajuan')
                ->constrained('pengajuan_perjalanan_tugas', 'id_pengajuan')
                ->cascadeOnDelete();
            $table->string('path_file', 255);
            $table->string('nama_asli', 150);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran');
            $table->string('jenis_dokumen', 50);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pendukung');
    }
};
