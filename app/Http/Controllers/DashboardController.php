<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Peramalan;
use App\Models\HasilPeramalan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Set locale Carbon ke Indonesia
        Carbon::setLocale('id');

        // ==========================================================
        // 1. DATA UNTUK CARD (4 KARTU STATISTIK)
        // ==========================================================
        $now = Carbon::now();

        // --- Card 1: Penjualan Bulan Ini ---
        $penjualanBulanIni = Penjualan::where('tahun', $now->year)
            ->where('bulan', $now->month)
            ->sum('jumlah_terjual');

        $prevMonth = $now->copy()->subMonth();
        $penjualanBulanLalu = Penjualan::where('tahun', $prevMonth->year)
            ->where('bulan', $prevMonth->month)
            ->sum('jumlah_terjual');

        $persenPerubahan = 0;
        if ($penjualanBulanLalu > 0) {
            $persenPerubahan = (($penjualanBulanIni - $penjualanBulanLalu) / $penjualanBulanLalu) * 100;
        } elseif ($penjualanBulanIni > 0) {
            $persenPerubahan = 100; // Jika bulan lalu 0, dan sekarang ada, anggap naik 100%
        }

        // --- Card 2: Prediksi Bulan Depan ---
        $peramalanTerbaru = Peramalan::with('hasil')->latest()->first();
        $prediksiBulanDepan = 0;
        if ($peramalanTerbaru) {
            $nextMonthDate = Carbon::now()->addMonth();
            $nextMonthString = $nextMonthDate->isoFormat('MMMM YYYY');

            $prediksi = $peramalanTerbaru->hasil
                ->where('periode_hasil', $nextMonthString)
                ->first();

            $prediksiBulanDepan = $prediksi ? $prediksi->nilai_peramalan : 0;
        }

        // --- Card 3: Akurasi (MAPE) ---
        // (Logika ini diambil dari LaporanController, untuk data yang sudah ada)
        $mape = 0;
        if ($peramalanTerbaru) {
            $totalPercentageError = 0;
            $n = 0;
            foreach ($peramalanTerbaru->hasil as $prediksi) {
                try {
                    $date = Carbon::parseFromLocale($prediksi->periode_hasil, 'id');
                } catch (\Exception $e) {
                    continue;
                }

                $dataAktual = Penjualan::where('bulan', $date->month)
                    ->where('tahun', $date->year)
                    ->first();

                if ($dataAktual) {
                    $aktualVal = $dataAktual->jumlah_terjual;
                    $prediksiVal = $prediksi->nilai_peramalan;

                    if ($aktualVal > 0) {
                        $errorAbsolut = abs($aktualVal - $prediksiVal);
                        $totalPercentageError += ($errorAbsolut / $aktualVal);
                        $n++;
                    }
                }
            }
            if ($n > 0) {
                $mape = ($totalPercentageError / $n) * 100;
            }
        }

        // --- Card 4: Data Historis ---
        $totalDataHistoris = Penjualan::count();


        // ==========================================================
        // 2. DATA UNTUK GRAFIK
        // ==========================================================
        $chartLabels = [];
        $chartDataAktual = [];
        $chartDataPrediksi = [];

        if ($peramalanTerbaru) {
            // Kita ambil 6 data terakhir dari hasil peramalan untuk dashboard
            $hasilPrediksi = $peramalanTerbaru->hasil->take(6);

            foreach ($hasilPrediksi as $prediksi) {
                try {
                    $date = Carbon::parseFromLocale($prediksi->periode_hasil, 'id');
                } catch (\Exception $e) {
                    continue;
                }

                $dataAktual = Penjualan::where('bulan', $date->month)
                    ->where('tahun', $date->year)
                    ->first();

                $chartLabels[] = $date->isoFormat('MMM YY');
                $chartDataAktual[] = $dataAktual ? $dataAktual->jumlah_terjual : null;
                $chartDataPrediksi[] = $prediksi->nilai_peramalan;
            }
        }

        $chartData = [
            'labels' => $chartLabels,
            'aktual' => $chartDataAktual,
            'prediksi' => $chartDataPrediksi,
        ];

        // ==========================================================
        // 3. DATA UNTUK LIST PENJUALAN TERBARU
        // ==========================================================
        $penjualanTerbaru = Penjualan::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->take(4) // Ambil 4 data terbaru
            ->get();


        // ==========================================================
        // 4. KIRIM SEMUA DATA KE VIEW
        // ==========================================================
        return view('dashboard', [
            'penjualanBulanIni' => $penjualanBulanIni,
            'persenPerubahan' => $persenPerubahan,
            'prediksiBulanDepan' => $prediksiBulanDepan,
            'mape' => round($mape, 2),
            'totalDataHistoris' => $totalDataHistoris,
            'penjualanTerbaru' => $penjualanTerbaru,
            'chartData' => $chartData
        ]);
    }
}
