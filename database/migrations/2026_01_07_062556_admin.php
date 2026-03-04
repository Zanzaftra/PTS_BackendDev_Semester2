<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id('id_admin');
            $table->string('nama_admin');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->enum('role', ['super admin', 'staff'])->default('staff');
            $table->enum('status_admin', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};