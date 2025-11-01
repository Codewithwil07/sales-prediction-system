<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Peramalan;
use App\Models\HasilPeramalan;
use Illuminate\Support\Facades\DB;
// Ganti import Notifikasi ke Toastify
// use Filament\Notifications\Notification; // <-- HAPUS INI
use Illuminate\Support\Facades\Redirect; // <-- GANTI PAKAI INI
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
        $totalY = 0;
        $totalXY = 0;
        $totalXSquare = 0;
        $dataPerhitungan = [];
        $isGanjil = ($n % 2 != 0);

        // 3. Loop data (Ini sudah benar)
        foreach ($dataPenjualan as $key => $item) {
            if ($isGanjil) {
                $X = $key - floor($n / 2);
            } else {
                $X = ($key * 2) + 1 - $n;
            }
            $Y = $item->jumlah_terjual;
            $XY = $X * $Y;
            $XSquare = $X * $X;
            $totalY += $Y;
            $totalXY += $XY;
            $totalXSquare += $XSquare;
            $dataPerhitungan[] = [
                'bulan' => $item->bulan,
                'tahun' => $item->tahun,
                'X' => $X,
                'Y' => $Y,
                'XY' => $XY,
                'XSquare' => $XSquare,
            ];
        }

        // 4. Hitung nilai 'a' dan 'b' (Ini sudah benar)
        if ($totalXSquare == 0) {
            return redirect()->route('peramalan.index')
                ->withErrors(['data' => 'Terjadi kesalahan: Total X^2 adalah nol.']);
        }
        $a = $totalY / $n;
        $b = $totalXY / $totalXSquare;
        $persamaan = "Y = " . round($a, 2) . " + " . round($b, 2) . "X";

        // 5. Buat data peramalan 12 bulan ke depan (Ini sudah benar)
        $peramalanBerikutnya = [];
        $X_terakhir = end($dataPerhitungan)['X'];
        $bulanTerakhir = $dataPenjualan->last()->bulan;
        $tahunTerakhir = $dataPenjualan->last()->tahun;

        for ($i = 1; $i <= 12; $i++) {
            $X_next = $isGanjil ? ($X_terakhir + $i) : ($X_terakhir + ($i * 2));
            $Y_forecast = $a + ($b * $X_next);
            $carbonDate = Carbon::create($tahunTerakhir, $bulanTerakhir, 1)->addMonths($i);
            $peramalanBerikutnya[] = [
                'periode' => $carbonDate->isoFormat('MMMM YYYY'),
                'X_next' => $X_next,
                'Y_forecast' => round($Y_forecast)
            ];
        }

        // Variabel untuk menampung notifikasi
        $notification = [];

        // 6. PROSES SIMPAN KE DATABASE
        DB::beginTransaction();
        try {
            $dataPertama = $dataPenjualan->first();
            $dataTerakhir = $dataPenjualan->last();
            $periodeHitung = Carbon::create($dataPertama->tahun, $dataPertama->bulan)->isoFormat('MMM YYYY') .
                ' - ' .
                Carbon::create($dataTerakhir->tahun, $dataTerakhir->bulan)->isoFormat('MMM YYYY');

            $masterPeramalan = Peramalan::create([
                'metode' => 'Trend Moment',
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

            // D. Jika semua berhasil, simpan permanen
            DB::commit();

            // --- INI PERUBAHANNYA ---
            // Kita siapkan notifikasi sukses, tapi JANGAN return dulu
            $notification = [
                'type' => 'success', // <-- Perbaiki typo 'succes' jadi 'success'
                'title' => 'Perhitungan Berhasil',
                'body' => 'Hasil peramalan telah dihitung dan disimpan ke database.'
            ];
            // HAPUS 'return redirect()' DARI SINI
            // --- SELESAI PERUBAHAN ---

        } catch (\Exception $e) {
            DB::rollBack();

            // Siapkan notifikasi GAGAL
            $notification = [
                'type' => 'danger',
                'title' => 'Perhitungan Gagal Disimpan',
                // Tampilkan error-nya biar jelas
                'body' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];

            // Jika gagal, BARU kita return langsung
            return Redirect::route('peramalan.index')->with('notification', $notification);
        }

        // 7. Kumpulkan semua hasil untuk ditampilkan di view
        $hasilTampil = [
            'dataPerhitungan' => $dataPerhitungan,
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
        // --- INI ADALAH FIX UTAMANYA ---
        return Redirect::route('peramalan.index')
            ->with('hasil_peramalan', $hasilTampil) // <-- Kirim data tabel
            ->with('notification', $notification); // <-- Kirim notifikasi sukses
    }
}
