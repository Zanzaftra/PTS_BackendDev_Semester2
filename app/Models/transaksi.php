<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tanggal_transaksi',
        'metode_pembayaran',
        'total_bayar',
        'status_transaksi',
        'kode_invoice',
        'catatan',
    ];
}
