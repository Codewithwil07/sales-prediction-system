<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel detail untuk menyimpan 12 baris hasil prediksi
        Schema::create('hasil_peramalan', function (Blueprint $table) {
            $table->id('id_hasil'); // Sesuai diagram
            
            // Foreign key ke tabel master 'peramalan'
            $table->foreignId('id_peramalan')
                  ->constrained('peramalan', 'id_peramalan') // referensi ke kolom 'id_peramalan' di tabel 'peramalan'
                  ->onDelete('cascade'); // Jika master dihapus, detail ikut terhapus

            $table->string('periode_hasil'); // Misal: "Januari 2025"
            $table->double('nilai_x'); // Nilai X yang dipakai
            $table->double('nilai_peramalan'); // Hasil Y (prediksi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_peramalan');
    }
};