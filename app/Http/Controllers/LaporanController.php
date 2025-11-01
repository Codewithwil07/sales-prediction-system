<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peramalan;
use App\Models\Penjualan;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        // 1. Ambil data peramalan terbaru (sesi perhitungan terakhir)
        $peramalanTerbaru = Peramalan::with('hasil')->latest()->first();

        // Jika belum ada data peramalan, tampilkan view kosong
        if (!$peramalanTerbaru) {
            return view('laporan.index', [
                'peramalanTerbaru' => null,
                'dataPerbandingan' => [], // Data historis yang dibandingkan
                'dataPrediksiMurni' => [], // Data prediksi murni (masa depan)
                'metrics' => null,
                'chartData' => null
            ]);
        }

        $hasilPrediksi = $peramalanTerbaru->hasil;

        // Inisialisasi array untuk data
        $dataPerbandingan = []; // Untuk data yang ADA aktualnya
        $dataPrediksiMurni = []; // Untuk data yang BELUM ADA aktualnya

        // Inisialisasi variabel untuk kalkulasi metrik
        $totalError = 0;
        $totalErrorSquare = 0;
        $totalPercentageError = 0;
        $totalActual = 0;
        $n = 0; // Jumlah data yang dibandingkan

        // Inisialisasi data untuk Grafik
        $chartLabels = [];
        $chartAktual = [];
        $chartPrediksi = [];

        // 4. Loop setiap hasil prediksi
        foreach ($hasilPrediksi as $prediksi) {

            try {
                $date = Carbon::parseFromLocale($prediksi->periode_hasil, 'id');
            } catch (\Exception $e) {
                continue;
            }

            // Cari data penjualan (aktual) di database
            $dataAktual = Penjualan::where('bulan', $date->month)
                ->where('tahun', $date->year)
                ->first();

            $prediksiVal = $prediksi->nilai_peramalan;
            $aktualVal = $dataAktual ? $dataAktual->jumlah_terjual : null;

            // Masukkan data ke grafik (semua periode prediksi)
            $chartLabels[] = $date->isoFormat('MMM YY');
            $chartPrediksi[] = $prediksiVal;
            $chartAktual[] = $aktualVal; // Ini akan berisi null untuk data masa depan

            // 5. PISAHKAN LOGIKA:
            // Jika data aktualnya ADA, masukkan ke tabel perbandingan & hitung metrik
            if ($aktualVal !== null) {
                $errorAbsolut = abs($aktualVal - $prediksiVal);

                $totalError += $errorAbsolut;
                $totalErrorSquare += pow($errorAbsolut, 2);
                if ($aktualVal > 0) {
                    $totalPercentageError += ($errorAbsolut / $aktualVal);
                }

                $n++; // Tambah jumlah data yang dihitung
                $totalActual += $aktualVal;

                $dataPerbandingan[] = [
                    'periode' => $prediksi->periode_hasil,
                    'aktual' => $aktualVal,
                    'peramalan' => $prediksiVal,
                    'error' => $aktualVal - $prediksiVal,
                    'error_square' => pow($aktualVal - $prediksiVal, 2),
                    'percentage_error' => ($aktualVal > 0) ? ($errorAbsolut / $aktualVal) * 100 : 0,
                ];
            }
            // Jika data aktualnya TIDAK ADA (null), masukkan ke tabel prediksi murni
            else {
                $dataPrediksiMurni[] = [
                    'periode' => $prediksi->periode_hasil,
                    'peramalan' => $prediksiVal,
                ];
            }
        }

        // 6. Hitung Metrik Akurasi (HANYA berdasarkan data yang bisa dibandingkan)
        $metrics = null;
        if ($n > 0) { // Pastikan $n (jumlah data pembanding) lebih dari 0
            $metrics = [
                'mad' => $totalError / $n,
                'mse' => $totalErrorSquare / $n,
                'mape' => ($totalPercentageError / $n) * 100,
                'data_compared' => $n // Info berapa data yg dibanding
            ];
        }

        // 7. Siapkan data untuk dikirim ke chart
        $chartData = [
            'labels' => $chartLabels,
            'aktual' => $chartAktual,
            'prediksi' => $chartPrediksi,
        ];

        // 8. Kirim semua data ke view
        return view('laporan.index', [
            'peramalanTerbaru' => $peramalanTerbaru,
            'dataPerbandingan' => $dataPerbandingan, // Data perbandingan
            'dataPrediksiMurni' => $dataPrediksiMurni, // Data prediksi murni
            'metrics' => $metrics,
            'chartData' => $chartData
        ]);
    }
}
