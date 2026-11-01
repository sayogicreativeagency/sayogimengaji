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
        // Kapan tiap kata dibaca di audio per ayat, per qori (milidetik dari awal file ayat).
        Schema::create('waktu_kata', function (Blueprint $table) {
            $table->id();
            $table->string('qori', 20);
            $table->unsignedSmallInteger('surat_nomor');
            $table->unsignedSmallInteger('ayat_nomor');
            $table->unsignedSmallInteger('posisi');
            $table->unsignedInteger('mulai_ms');
            $table->unsignedInteger('selesai_ms');

            $table->unique(['qori', 'surat_nomor', 'ayat_nomor', 'posisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waktu_kata');
    }
};
