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
        // Kata per ayat (rasm Utsmani, Quran.com) untuk highlight per kata di fitur belajar.
        Schema::create('kata', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('surat_nomor');
            $table->unsignedSmallInteger('ayat_nomor');
            $table->unsignedSmallInteger('posisi');
            $table->string('teks', 100);

            $table->unique(['surat_nomor', 'ayat_nomor', 'posisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kata');
    }
};
