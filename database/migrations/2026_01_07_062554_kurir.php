<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurir', function (Blueprint $table) {
            $table->id('id_kurir');
            $table->string('nama_kurir');
            $table->string('no_hp', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status_kurir', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('kendaraan')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurir');
    }
};