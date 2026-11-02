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
        // Tagihan langganan via QRIS/transfer, dicek manual oleh admin.
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedInteger('nominal'); // harga × bulan + kode unik 3 digit
            $table->string('status', 12)->default('menunggu')->index();
            $table->string('metode', 10)->nullable(); // qris, transfer
            $table->string('pengirim', 100)->nullable();
            $table->string('bukti')->nullable(); // path di disk local (privat)
            $table->timestamp('dilaporkan_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
