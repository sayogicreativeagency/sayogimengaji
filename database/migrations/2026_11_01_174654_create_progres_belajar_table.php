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
        Schema::create('progres_belajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('surat_nomor');
            $table->unsignedSmallInteger('ayat_nomor');
            $table->timestamp('didengar_at')->nullable();
            $table->timestamp('dihafal_at')->nullable();
            $table->timestamps(); // updated_at terbaru = posisi terakhir belajar

            $table->unique(['user_id', 'surat_nomor', 'ayat_nomor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_belajar');
    }
};
