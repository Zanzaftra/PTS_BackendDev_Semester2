<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('produk_air', function (Blueprint $table) {
                $table->id('id_produk');
                $table->string('nama_produk', 150);
                $table->enum('jenis_kemasan', ['galon', 'botol', 'refill', 'gelas']);
                $table->enum('kapasitas', ['1500ml', '600ml', '300ml', '220ml']);
                $table->decimal('harga');
                $table->unsignedInteger('stok')->default(0);
                $table->enum('status_produk', ['tersedia', 'habis', 'maintenance'])->default('tersedia');
                $table->dateTime('tanggal_ditambahkan')->nullable();
                $table->string('foto_produk')->nullable();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('produk_air');
        }
    };