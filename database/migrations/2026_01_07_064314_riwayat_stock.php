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
        Schema::create('riwayat_stock', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_produk')->constrained('produk_air','id_produk');
            $table->enum('jenis_perubahan', ['masuk', 'keluar', 'koreksi']);
            $table->integer('jumlah')->default(0);
            $table->timestamp('tanggal_perubahan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stock');
    }
};