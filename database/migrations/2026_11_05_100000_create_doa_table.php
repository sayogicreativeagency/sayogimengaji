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
        Schema::create('doa', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary(); // id dari equran.id, dipakai di URL
            $table->string('grup');
            $table->string('nama');
            $table->text('arab');
            $table->text('latin');
            $table->text('arti');
            $table->text('tentang')->nullable(); // keterangan dan rujukan bacaan
            $table->timestamps();
            $table->index('grup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doa');
    }
};
