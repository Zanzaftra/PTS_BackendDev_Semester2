<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class langganan extends Model
{
    use HasFactory;

    protected $table = 'langganan';
    protected $primaryKey = 'id_langganan';
    
    protected $fillable = [
        'id_pelanggan',
        'id_produk',
        'periode_pengantaran',
        'tanggal_mulai',
        'tanggal_berakhir',
        'jumlah_pesanan',
        'status_langganan',
    ];
}
