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
        Schema::create('gudang', function (Blueprint $table) {
            $table->id('id_gudang');
            $table->string('nama_gudang');
            $table->string('lokasi')->nullable();
            $table->unsignedInteger('kapasitas_total')->default(0);
            $table->unsignedInteger('stok_saat_ini')->default(0);
            $table->enum('status_gudang', ['aktif', 'penuh', 'maintenance'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang');
    }
};