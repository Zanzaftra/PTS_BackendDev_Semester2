<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factory\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    
    protected $fillable = [
        'jenis_pelanggan',
        'nama_pelanggan',
        'nama_lembaga',
        'penanggung_jawab',
        'alamat',
        'no_telepon',
        'email',
        'tanggal_daftar',
        'status_pelanggan',
    ];
}
