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
        // Satu baris per sesi putar murottal, bahan halaman Statistik murottal; terhapus setelah 90 hari.
        Schema::create('putar_murottal', function (Blueprint $table) {
            $table->id();
            $table->uuid('sesi')->unique(); // dibuat browser; laporan berikutnya memperbarui baris yang sama
            $table->string('ip', 45);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('surat_nomor');
            $table->foreign('surat_nomor')->references('nomor')->on('surat')->cascadeOnDelete();
            $table->unsignedSmallInteger('ayat_mulai'); // 0 = basmalah
            $table->unsignedSmallInteger('ayat_terakhir');
            $table->boolean('selesai')->default(false);
            $table->unsignedInteger('detik')->default(0); // lama audio benar-benar berjalan
            $table->string('qori', 30);
            $table->string('sumber', 10); // surat | beranda
            $table->string('perangkat', 10); // HP | Komputer
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('putar_murottal');
    }
};
