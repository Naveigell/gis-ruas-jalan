<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'paths', 'desa_id', 'nama_ruas', 'kode_ruas', 'panjang', 'lebar', 'eksisting_id',
        'kondisi_id', 'jenisjalan_id', 'keterangan',
    ];
}
