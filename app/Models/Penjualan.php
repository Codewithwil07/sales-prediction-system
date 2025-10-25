<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'bulan',
        'tahun',
        'jumlah_terjual',
    ];

    /**
     * TAMBAHKAN PROPERTI INI
     * * Memberitahu Laravel untuk otomatis mengubah
     * tipe data saat mengambil/menyimpan.
     */
    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'jumlah_terjual' => 'integer',
    ];
}
