<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    
    protected $fillable = [
        'id_transaksi',
        'id_kurir',
        'alamat_tujuan',
        'tanggal_pengiriman',
        'status_pengiriman',
        'foto_bukti_pengiriman',
        'catatan_kurir',
    ];
}
