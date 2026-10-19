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
        Schema::create('travel_policy', function (Blueprint $table) {
            $table->bigIncrements('id_policy');
            $table->string('latarbelakang', 100);
            $table->string('grade', 100);
            $table->decimal('limit_biaya', 15, 2)->default(0);
            $table->string('kelas', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_policy');
    }
};
