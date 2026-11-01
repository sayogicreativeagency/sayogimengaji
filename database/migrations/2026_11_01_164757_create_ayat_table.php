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
        Schema::create('ayat', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('surat_nomor');
            $table->unsignedSmallInteger('nomor');
            $table->text('teks_arab');
            $table->text('teks_latin');
            $table->text('terjemahan');
            $table->timestamps();

            $table->unique(['surat_nomor', 'nomor']);
            $table->foreign('surat_nomor')->references('nomor')->on('surat')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayat');
    }
};
