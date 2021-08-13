<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
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

    public function penjualanDetails()
    {
        return $this->hasMany('App\Models\PenjualanDetail');
    }

    public function pembelianDetails()
    {
        return $this->hasMany('App\Models\PembelianDetail');
    }
}
