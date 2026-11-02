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
        // Hitungan kata yang dicari per hari, tanpa user_id/IP: bahan sugesti dan riset pencarian.
        Schema::create('pencarian_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kata', 200); // huruf kecil, spasi dirapikan
            $table->unsignedInteger('jumlah')->default(0);
            $table->unsignedInteger('jumlah_tanpa_hasil')->default(0);
            $table->unique(['tanggal', 'kata']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencarian_harian');
    }
};
