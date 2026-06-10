<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class riwayat_stock extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stock';
    protected $primaryKey = 'id_riwayat';
    
    protected $fillable = [
        'id_produk',
        'jenis_perubahan',
        'jumlah',
        'tanggal_perubahan',
        'keterangan',
    ];
}
