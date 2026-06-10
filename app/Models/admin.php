<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    
    protected $fillable = [
        'nama_admin',
        'username',
        'password',
        'email',
        'no_hp',
        'kode_perusahaan',
        'role',
        'status_admin',
    ];
    //
}
