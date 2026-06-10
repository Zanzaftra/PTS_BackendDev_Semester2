<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kurir extends Model
{
    use HasFactory;

    protected $table = 'kurir';
    protected $primaryKey = 'id_kurir';
    
    protected $fillable = [
        'nama_kurir',
        'no_hp',
        'alamat',
        'status_kurir',
        'kendaraan',
        'plat_nomor',
        'catatan',
    ];
}
