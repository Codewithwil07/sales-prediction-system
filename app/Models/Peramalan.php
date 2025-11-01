<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peramalan extends Model
{
    use HasFactory;
    
    // Tentukan nama tabel & primary key
    protected $table = 'peramalan';
    protected $primaryKey = 'id_peramalan';

    protected $fillable = [
        'metode',
        'periode_perhitungan',
        'nilai_a',
        'nilai_b',
        'persamaan',
    ];

    // Relasi: Satu 'Peramalan' punya banyak 'HasilPeramalan'
    public function hasil()
    {
        return $this->hasMany(HasilPeramalan::class, 'id_peramalan', 'id_peramalan');
    }
}