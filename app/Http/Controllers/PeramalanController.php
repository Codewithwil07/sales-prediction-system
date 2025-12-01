<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Peramalan;
use App\Models\HasilPeramalan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class PeramalanController extends Controller
{
    /**
     * Menampilkan halaman utama peramalan.
     */
    public function index()
    {
        $hasil = session('hasil_peramalan');
        return view('peramalan.index', [
            'hasil' => $hasil
        ]);
    }

    /**
     * Memproses perhitungan peramalan (Trend Moment).
     * VERSI BARU (SESUAI PROPOSAL FIX.pdf)
     */
    public function hitung(Request $request)
    {
        // 1. Ambil semua data penjualan
        $dataPenjualan = Penjualan::orderBy('tahun')->orderBy('bulan')->get();

        if ($dataPenjualan->count() < 2) {
            return redirect()->route('peramalan.index')
                ->withErrors(['data' => 'Data penjualan historis tidak cukup (minimal 2 data).']);
        }

        // 2. Inisialisasi variabel perhitungan
        $n = $dataPenjualan->count();
        $totalX = 0;
        $totalY = 0;
        $totalXY = 0;
        $totalXSquare = 0;
        $dataPerhitungan = [];

        // 3. Loop data (METODE X BARU: 0, 1, 2, ...)
        foreach ($dataPenjualan as $key => $item) {
            
            // INI PERUBAHANNYA: X dimulai dari 0 (key array)
            $X = $key; 
            
            $Y = $item->jumlah_terjual;
            $XY = $X * $Y;
            $XSquare = $X * $X; // atau pow($X, 2)

            // Akumulasi total
            $totalX += $X;
            $totalY += $Y;
            $totalXY += $XY;
            $totalXSquare += $XSquare;

            $dataPerhitungan[] = [
                'bulan' => $item->bulan, 'tahun' => $item->tahun,
                'X' => $X, 'Y' => $Y, 'XY' => $XY, 'XSquare' => $XSquare,
            ];
        }

        // 4. Hitung nilai 'a' dan 'b' (RUMUS FULL BARU)
        
        // Hitung penyebut (denominator) b
        $denominator_b = ($n * $totalXSquare) - ($totalX * $totalX); // ($totalX * $totalX) adalah (ΣX)²

        if ($denominator_b == 0) {
            return redirect()->route('peramalan.index')
                ->withErrors(['data' => 'Terjadi kesalahan perhitungan: Pembagi bernilai nol.']);
        }
        
        // Hitung pembilang (numerator) b
        $numerator_b = ($n * $totalXY) - ($totalX * $totalY);

        // Hitung b
        $b = $numerator_b / $denominator_b;

        // Hitung a
        $a = ($totalY - ($b * $totalX)) / $n;
        
        $persamaan = "Y = " . round($a, 2) . " + " . round($b, 2) . "X";

        // 5. Buat data peramalan 12 bulan ke depan
        $peramalanBerikutnya = [];
        
        // X terakhir adalah key terakhir
        $X_terakhir = $n - 1; // Jika n=12, X terakhir = 11
        
        $bulanTerakhir = $dataPenjualan->last()->bulan;
        $tahunTerakhir = $dataPenjualan->last()->tahun;

        for ($i = 1; $i <= 12; $i++) {
            
            // INI PERUBAHANNYA: Pola X baru adalah +1
            $X_next = $X_terakhir + $i;
            
            $Y_forecast = $a + ($b * $X_next);
            $carbonDate = Carbon::create($tahunTerakhir, $bulanTerakhir, 1)->addMonths($i);
            
            $peramalanBerikutnya[] = [
                'periode' => $carbonDate->isoFormat('MMMM YYYY'),
                'X_next' => $X_next,
                'Y_forecast' => round($Y_forecast)
            ];
        }

        // Variabel untuk notifikasi
        $notification = [];

        // 6. PROSES SIMPAN KE DATABASE (Logic ini masih sama)
        DB::beginTransaction();
        try {
            $dataPertama = $dataPenjualan->first();
            $dataTerakhir = $dataPenjualan->last();
            $periodeHitung = Carbon::create($dataPertama->tahun, $dataPertama->bulan)->isoFormat('MMM YYYY') .
                ' - ' .
                Carbon::create($dataTerakhir->tahun, $dataTerakhir->bulan)->isoFormat('MMM YYYY');

            $masterPeramalan = Peramalan::create([
                'metode' => 'Trend Moment (Sequential)', // Update nama metode
                'periode_perhitungan' => $periodeHitung,
                'nilai_a' => $a,
                'nilai_b' => $b,
                'persamaan' => $persamaan,
            ]);

            foreach ($peramalanBerikutnya as $hasil) {
                HasilPeramalan::create([
                    'id_peramalan' => $masterPeramalan->id_peramalan,
                    'periode_hasil' => $hasil['periode'],
                    'nilai_x' => $hasil['X_next'],
                    'nilai_peramalan' => $hasil['Y_forecast'],
                ]);
            }
            DB::commit();
            
            $notification = [
                'type' => 'success',
                'title' => 'Perhitungan Berhasil',
                'body' => 'Hasil peramalan (metode baru) telah dihitung dan disimpan.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            $notification = [
                'type' => 'danger',
                'title' => 'Perhitungan Gagal Disimpan',
                'body' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
            return Redirect::route('peramalan.index')->with('notification', $notification);
        }

        // 7. Kumpulkan semua hasil untuk ditampilkan di view
        $hasilTampil = [
            'dataPerhitungan' => $dataPerhitungan,
            'totalX' => $totalX, // Kirim total X baru
            'totalY' => $totalY,
            'totalXY' => $totalXY,
            'totalXSquare' => $totalXSquare,
            'n' => $n,
            'nilai_a' => $a,
            'nilai_b' => $b,
            'persamaan' => $persamaan,
            'peramalanBerikutnya' => $peramalanBerikutnya,
        ];

        // 8. Simpan hasil di session DAN notifikasi, lalu redirect
        return Redirect::route('peramalan.index')
            ->with('hasil_peramalan', $hasilTampil)
            ->with('notification', $notification);
    }
}