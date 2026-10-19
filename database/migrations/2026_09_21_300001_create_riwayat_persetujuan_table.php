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
        Schema::create('riwayat_persetujuan', function (Blueprint $table) {
            $table->bigIncrements('id_persetujuan');
            $table->string('approvable_type', 100)->index();
            $table->unsignedBigInteger('approvable_id')->index();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_approver', 150)->nullable();
            $table->string('peran_approver', 100)->nullable();
            $table->string('status', 50);
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_persetujuan');
    }
};
