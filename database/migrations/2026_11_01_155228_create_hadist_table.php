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
        Schema::create('hadist', function (Blueprint $table) {
            $table->id();
            $table->string('kitab', 20);
            $table->string('nomor', 20);
            $table->string('nomor_arab', 20)->nullable();
            $table->unsignedInteger('bab_nomor')->nullable();
            $table->string('bab_judul')->nullable();
            $table->longText('teks_arab')->nullable();
            $table->text('terjemahan');
            $table->json('derajat');
            $table->string('derajat_ringkas', 10)->nullable()->index();
            $table->timestamp('dikoreksi_at')->nullable();
            $table->timestamps();

            $table->unique(['kitab', 'nomor']);
            $table->fullText(['terjemahan', 'bab_judul']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadist');
    }
};
