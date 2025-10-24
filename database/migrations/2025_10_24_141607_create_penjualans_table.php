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
        // Kita pakai 'penjualan' (lowercase) sebagai nama tabel
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('bulan'); // 'Januari', 'Februari', dst.
            $table->year('tahun'); // 2023, 2024, dst.
            $table->integer('jumlah_terjual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};