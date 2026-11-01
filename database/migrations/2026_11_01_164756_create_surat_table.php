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
        Schema::create('surat', function (Blueprint $table) {
            $table->unsignedSmallInteger('nomor')->primary();
            $table->string('nama', 50);
            $table->string('nama_latin', 50);
            $table->string('arti', 100);
            $table->string('tempat_turun', 20);
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};
