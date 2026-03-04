<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factory\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'nama_admin',
        'username',
        'password',
        'email',
        'no_hp',
        'role',
        'status_admin',
    ];
    //
}
