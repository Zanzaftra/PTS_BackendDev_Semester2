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
        Schema::create('langganan', function (Blueprint $table) {
            $table->id('id_langganan');
            $table->foreignId('id_pelanggan')->references('id_pelanggan')->on('pelanggan');
            $table->foreignId('id_produk')->constrained('produk_air','id_produk');
            $table->enum('periode_pengantaran', ['harian', 'mingguan', 'bulanan']);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->unsignedInteger('jumlah_pesanan')->default(1);
            $table->enum('status_langganan', ['aktif', 'berhenti', 'tertunda'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langganan');
    }
};