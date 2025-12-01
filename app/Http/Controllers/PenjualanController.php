<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
// Kita nggak pakai Filament lagi, jadi hapus import-nya
// use Filament\Notifications\Notification; 

class PenjualanController extends Controller
{
    /**
     * Tampilkan halaman utama (daftar data penjualan).
     */
    public function index(Request $request)
{
    $query = Penjualan::query();

    $sort = $request->input('sort'); // null = default
    $filterTahun = $request->input('filter_tahun');
    $search = $request->input('search');

    if ($request->filled('filter_tahun')) {
        $query->where('tahun', $filterTahun);
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('bulan', 'like', '%' . $search . '%')
              ->orWhere('tahun', 'like', '%' . $search . '%');
        });
    }

    // Sorting: default = tahun ASC (terlama), bulan ASC (Januari = 1 di atas)
    if ($sort === 'terbaru') {
        $query->orderBy('tahun', 'desc')
              ->orderByRaw('CAST(bulan AS UNSIGNED) desc');
    } elseif ($sort === 'terlama') {
        $query->orderBy('tahun', 'asc')
              ->orderByRaw('CAST(bulan AS UNSIGNED) asc');
    } else {
        $query->orderBy('tahun', 'asc')
              ->orderByRaw('CAST(bulan AS UNSIGNED) asc');
    }

    $daftarTahun = Penjualan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
    $dataPenjualan = $query->paginate(10)->withQueryString();

    return view('penjualan.index', compact('dataPenjualan', 'daftarTahun'))
           ->with(['filters' => $request->only(['filter_tahun','search','sort']), 'sort' => $sort]);
}


    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => ['required', 'numeric', 'min:1', 'max:12', Rule::unique('penjualan')->where(fn($query) => $query->where('tahun', $request->tahun))],
            'tahun' => 'required|numeric|digits:4|min:2000',
            'jumlah_terjual' => 'required|numeric|min:0',
        ], [
            'bulan.unique' => 'Data untuk Bulan dan Tahun ini sudah ada.',
            'bulan.max' => 'Bulan harus angka 1-12.',
        ]);

        Penjualan::create($validated);

        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'success',
            'title' => 'Data Berhasil Disimpan',
            'body' => 'Data penjualan baru telah ditambahkan.'
        ]);
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $validated = $request->validate([
            'bulan' => ['required', 'numeric', 'min:1', 'max:12', Rule::unique('penjualan')->where(fn($query) => $query->where('tahun', $request->tahun))->ignore($penjualan->id)],
            'tahun' => 'required|numeric|digits:4|min:2000',
            'jumlah_terjual' => 'required|numeric|min:0',
        ], [
            'bulan.unique' => 'Data untuk Bulan dan Tahun ini sudah ada.',
            'bulan.max' => 'Bulan harus angka 1-12.',
        ]);

        $penjualan->update($validated);

        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'success',
            'title' => 'Data Berhasil Diperbarui',
            'body' => 'Perubahan data penjualan telah disimpan.'
        ]);
    }

    /**
     * Hapus data dari database.
     */
    public function destroy(Penjualan $penjualan)
    {
        $penjualan->delete();

        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'danger',
            'title' => 'Data Berhasil Dihapus',
            'body' => 'Data penjualan telah dihapus dari sistem.'
        ]);
    }

    // --- Method tidak terpakai (karena pakai modal) ---
    public function create()
    {
        return abort(404);
    }
    public function show(Penjualan $penjualan)
    {
        return abort(404);
    }
    public function edit(Penjualan $penjualan)
    {
        return abort(404);
    }
}
