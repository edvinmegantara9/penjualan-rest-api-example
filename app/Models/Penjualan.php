<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_penjualan',
        'pelanggan',
        'keterangan'
    ];

    public function penjualanDetails()
    {
        return $this->hasMany('App\Models\PenjualanDetail');
    }
}
