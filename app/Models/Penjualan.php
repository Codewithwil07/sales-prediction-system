<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit
     */
    protected $table = 'penjualan';

    /**
     * Atribut yang boleh diisi
     */
    protected $fillable = [
        'bulan',
        'tahun',
        'jumlah_terjual',
    ];
}