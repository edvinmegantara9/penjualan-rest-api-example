<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'qty',
        'harga_beli',
        'harga_jual'
    ];

    public function barang()
    {
        return $this->belongsTo('App\Models\Barang');
    }
}
