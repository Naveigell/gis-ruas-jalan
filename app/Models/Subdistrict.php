<?php

namespace App\Models;

use App\Models\Concerns\HydrateWithRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    use HasFactory, HydrateWithRelation;

    protected $fillable = ['kab_id', 'kecamatan'];
}
