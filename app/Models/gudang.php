<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gudang extends Model
{
    use HasFactory;

    protected $table = 'gudang';
    protected $primaryKey = 'id_gudang';
    
    protected $fillable = [
        'nama_gudang',
        'lokasi',
        'kapasitas_total',
        'stok_saat_ini',
        'status_gudang',
    ];
}
