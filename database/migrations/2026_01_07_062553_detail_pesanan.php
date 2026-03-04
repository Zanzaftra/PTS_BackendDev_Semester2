<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_transaksi')->constrained('transaksi','id_transaksi');
            $table->foreignId('id_produk')->constrained('produk_air','id_produk');
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('harga_satuan')->default(0);
            $table->decimal('subtotal', 14, 2)->virtualAs('jumlah * harga_satuan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};