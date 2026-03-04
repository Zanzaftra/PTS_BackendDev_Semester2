<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_pelanggan')->constrained('pelanggan','id_pelanggan');
            $table->foreignId('id_langganan')->nullable()->constrained('langganan','id_langganan')->nullOnDelete();
            $table->timestamp('tanggal_transaksi')->nullable();
            $table->enum('metode_pembayaran', ['transfer', 'tunai', 'e-wallet']);
            $table->decimal('total_bayar', 14, 2)->default(0);
            $table->enum('status_transaksi', ['menunggu', 'dibayar', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->string('kode_invoice')->unique();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};