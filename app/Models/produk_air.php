<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produk_air extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_produk',
        'jenis_kemasan',
        'kapasitas',
        'harga',
        'stok',
        'status_produk',
        'tanggal_ditambahkan',
        'foto_produk',
        'deskripsi',
    ];
}
