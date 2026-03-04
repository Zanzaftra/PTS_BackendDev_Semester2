<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->enum('jenis_pelanggan', ['individu', 'lembaga']);
            $table->string('nama_pelanggan')->unique();
            $table->string('nama_lembaga')->nullable();
            $table->string('penanggung_jawab');
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20);
            $table->string('email')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->enum('status_pelanggan', ['aktif', 'tidak aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};