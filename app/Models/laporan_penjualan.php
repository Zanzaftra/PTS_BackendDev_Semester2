<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class laporan_penjualan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'periode_laporan',
        'total_transaksi',
        'total_pendapatan',
        'produk_terlaris',
        'tanggal_dibuat',
    ];
}
