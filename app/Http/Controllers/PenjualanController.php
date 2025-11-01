<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::query();

        // Ambil value 'sort' dari URL, default-nya 'terbaru'
        $sort = $request->input('sort', 'terbaru');

        // Logic Search (tetap sama)
        if ($request->has('search') && $request->search != '') {
            $query->where('bulan', 'like', '%' . $request->search . '%')
                ->orWhere('tahun', 'like', '%' . $request->search . '%');
        }

        // --- INI BAGIAN BARUNYA ---
        // Logic Filter/Sort
        if ($sort === 'terlama') {
            // Urutkan dari tahun terlama, lalu bulan terlama
            $query->orderBy('tahun', 'asc')->orderBy('bulan', 'asc');
        } else {
            // Default: 'terbaru'
            // Urutkan dari tahun terbaru, lalu bulan terbaru
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }
        // --- SELESAI BAGIAN BARU ---

        // Paginasi (tambahkan withQueryString agar filter tetap nempel)
        $dataPenjualan = $query->paginate(10)->withQueryString();

        return view('penjualan.index', [
            'dataPenjualan' => $dataPenjualan,
            'sort' => $sort // Kirim balik nilai 'sort' ke view (penting untuk dropdown)
        ]);
    }
    public function create()
    {
        return abort(404);
    }

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

        // KIRIM NOTIFIKASI BARU (format 'notification' standar)
        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'success', // 'success' atau 'danger'
            'title' => 'Data Berhasil Disimpan',
            'body' => 'Data penjualan baru telah ditambahkan.'
        ]);
    }

    public function show(Penjualan $penjualan)
    {
        return abort(404);
    }

    public function edit(Penjualan $penjualan)
    {
        return abort(404);
    }

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

        // KIRIM NOTIFIKASI BARU
        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'success',
            'title' => 'Data Berhasil Diperbarui',
            'body' => 'Perubahan data penjualan telah disimpan.'
        ]);
    }

    public function destroy(Penjualan $penjualan)
    {
        $penjualan->delete();

        // KIRIM NOTIFIKASI BARU
        return Redirect::route('penjualan.index')->with('notification', [
            'type' => 'danger', // Ini akan jadi merah
            'title' => 'Data Berhasil Dihapus',
            'body' => 'Data penjualan telah dihapus dari sistem.'
        ]);
    }
}
