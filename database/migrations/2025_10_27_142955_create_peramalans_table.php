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
        // Tabel master untuk menyimpan sesi perhitungan
        Schema::create('peramalan', function (Blueprint $table) {
            $table->id('id_peramalan'); // Sesuai diagram
            $table->string('metode'); // "Trend Moment"
            $table->string('periode_perhitungan'); // Misal: "Jan 2023 - Des 2024"
            $table->double('nilai_a'); // Hasil 'a'
            $table->double('nilai_b'); // Hasil 'b'
            $table->string('persamaan'); // "Y = a + bX"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peramalan');
    }
};