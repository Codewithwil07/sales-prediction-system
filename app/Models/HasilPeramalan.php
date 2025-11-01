<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPeramalan extends Model
{
    use HasFactory;

    // Tentukan nama tabel & primary key
    protected $table = 'hasil_peramalan';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_peramalan',
        'periode_hasil',
        'nilai_x',
        'nilai_peramalan',
    ];

    // Relasi: Satu 'HasilPeramalan' dimiliki oleh satu 'Peramalan'
    public function peramalan()
    {
        return $this->belongsTo(Peramalan::class, 'id_peramalan', 'id_peramalan');
    }
}