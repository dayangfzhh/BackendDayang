<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Member;

class Outlet extends Model
{
    protected $table = 'outlet';
    protected $primaryKey = 'id_outlet';
    protected $fillable = ['nama_outlet', 'alamat'];
}