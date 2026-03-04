<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_penjualan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->enum('periode_laporan', ['harian', 'mingguan', 'bulanan']);
            $table->unsignedInteger('total_transaksi')->default(0);
            $table->decimal('total_pendapatan', 14, 2)->default(0);
            $table->string('produk_terlaris', 150)->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_penjualan');
    }
};