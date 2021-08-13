<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'merk',
        'kategori',
        'harga_beli',
        'harga_jual',
        'satuan'
    ];

    public function pembelianDetails()
    {
        return $this->hasMany('App\Models\PembelianDetail');
    }
}
