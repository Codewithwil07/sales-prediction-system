<?php

namespace App\Http\Controllers;

use App\Models\Penjualan; // Import Model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule untuk validasi unique
use Filament\Notifications\Notification; // Import Filament Notifications
use Illuminate\Support\Facades\Redirect; // Import Redirect

class PenjualanController extends Controller
{
    /**
     * Menampilkan daftar data penjualan (halaman index).
     */
    public function index(Request $request)
    {
        // 1. Mulai query
        $query = Penjualan::query();

        // 2. Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('bulan', 'like', '%' . $request->search . '%')
                ->orWhere('tahun', 'like', '%' . $request->search . '%');
        }

        // 3. Ambil data, urutkan dari terbaru, paginasi, dan sertakan query string
        $dataPenjualan = $query->latest()->paginate(10)->withQueryString();

        // 4. Kirim data ke view
        return view('penjualan.index', [
            'dataPenjualan' => $dataPenjualan
        ]);
    }

    /**
     * Tampilkan form tambah data (tidak dipakai, kita pakai modal).
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'bulan' => [
                'required',
                'numeric',
                'min:1',
                'max:12',
                Rule::unique('penjualan')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun);
                }),
            ],
            'tahun' => 'required|numeric|digits:4|min:2000',
            'jumlah_terjual' => 'required|numeric|min:0',
        ], [
            // Pesan error kustom (bahasa Indonesia)
            'bulan.required' => 'Bulan wajib diisi.',
            'bulan.numeric' => 'Bulan harus berupa angka (1-12).',
            'bulan.min' => 'Bulan minimal adalah 1.',
            'bulan.max' => 'Bulan maksimal adalah 12.',
            'bulan.unique' => 'Data untuk Bulan dan Tahun ini sudah ada.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.digits' => 'Tahun harus 4 digit (contoh: 2024).',
            'jumlah_terjual.required' => 'Jumlah terjual wajib diisi.',
            'jumlah_terjual.numeric' => 'Jumlah terjual harus berupa angka.',
        ]);

        // 2. Simpan ke Database
        Penjualan::create($validated);

        // 3. Kirim Notifikasi Sukses
        return redirect()->route('penjualan.index')->with('notification', [
            'type' => 'success',
            'title' => 'Data Berhasil Disimpan',
            'body' => 'Data penjualan baru telah ditambahkan.'
        ]);

        // 4. Kembali ke halaman index
        // Kita gunakan withInput() untuk jaga-jaga, tapi seharusnya tidak perlu
        return Redirect::route('penjualan.index')->withInput();
    }

    /**
     * Tampilkan detail (tidak kita gunakan di metode modal).
     */
    public function show(Penjualan $penjualan)
    {
        return abort(404);
    }

    /**
     * Tampilkan form edit (tidak dipakai, kita pakai modal).
     */
    public function edit(Penjualan $penjualan)
    {
        return abort(404);
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'bulan' => [
                'required',
                'numeric',
                'min:1',
                'max:12',
                Rule::unique('penjualan')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun);
                })->ignore($penjualan->id),
            ],
            'tahun' => 'required|numeric|digits:4|min:2000',
            'jumlah_terjual' => 'required|numeric|min:0',
        ], [
            'bulan.required' => 'Bulan wajib diisi.',
            'bulan.numeric' => 'Bulan harus berupa angka (1-12).',
            'bulan.min' => 'Bulan minimal adalah 1.',
            'bulan.max' => 'Bulan maksimal adalah 12.',
            'bulan.unique' => 'Data untuk Bulan dan Tahun ini sudah ada.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.digits' => 'Tahun harus 4 digit (contoh: 2024).',
            'jumlah_terjual.required' => 'Jumlah terjual wajib diisi.',
            'jumlah_terjual.numeric' => 'Jumlah terjual harus berupa angka.',
        ]);

        // 2. Update data di Database
        $penjualan->update($validated);

        // 3. Kirim Notifikasi Sukses
        return redirect()->route('penjualan.index')->with('notification', [
            'type' => 'success',
            'title' => 'Data Berhasil Diubah',
            'body' => 'Data penjualan baru telah ditambahkan.'
        ]);

        // 4. Kembali ke halaman index
        return Redirect::route('penjualan.index');
    }

    /**
     * Hapus data dari database.
     */
    public function destroy(Penjualan $penjualan)
    {
        // 1. Hapus Data
        $penjualan->delete();

        // 2. Kirim Notifikasi
        return redirect()->route('penjualan.index')->with('notification', [
            'type' => 'danger',
            'title' => 'Data Berhasil Dihapus',
            'body' => 'Data penjualan telah hapus.'
        ]);

        // 3. Kembali ke halaman index
        return Redirect::route('penjualan.index');
    }
}
