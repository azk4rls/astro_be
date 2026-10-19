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
        Schema::create('bukti_struk', function (Blueprint $table) {
            $table->bigIncrements('id_bukti_struk');
            $table->foreignId('id_pengeluaran')
                ->constrained('pengeluaran_perjalanan', 'id_pengeluaran')
                ->cascadeOnDelete();
            $table->string('path_file', 255);
            $table->string('nama_asli', 150);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_struk');
    }
};
