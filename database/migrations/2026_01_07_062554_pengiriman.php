<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->foreignId('id_transaksi')->constrained('transaksi','id_transaksi');
            $table->foreignId('id_kurir')->constrained('kurir','id_kurir')->nullable();
            $table->text('alamat_tujuan');
            $table->timestamp('tanggal_pengiriman')->nullable();
            $table->enum('status_pengiriman', ['dijadwalkan', 'dalam perjalanan', 'terkirim', 'gagal'])->default('dijadwalkan');
            $table->string('foto_bukti_pengiriman')->nullable();
            $table->text('catatan_kurir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};