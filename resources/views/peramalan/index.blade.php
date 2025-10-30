<x-admin-layout>
    <x-slot name="header">
        Peramalan Penjualan (Metode Trend Moment)
    </x-slot>

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">

            <p class="text-sm text-gray-600">
                Klik tombol untuk memproses seluruh data penjualan historis <br>
                dan menghitung peramalan untuk 12 bulan ke depan.
            </p>

            <form action="{{ route('peramalan.hitung') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                    <span>Jalankan Perhitungan</span>
                </button>
            </form>
        </div>
        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="h-5 w-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($hasil)

        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 space-y-2">
            <h3 class="text-lg font-semibold text-gray-900">Hasil Perhitungan (Trend Moment)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">Nilai 'a' (Konstanta)</div>
                    <div class="text-2xl font-bold text-gray-800">{{ round($hasil['nilai_a'], 3) }}</div>
                </div>
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">Nilai 'b' (Slope)</div>
                    <div class="text-2xl font-bold text-gray-800">{{ round($hasil['nilai_b'], 3) }}</div>
                </div>
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">Persamaan Peramalan</div>
                    <div class="text-lg font-bold text-indigo-600">{{ $hasil['persamaan'] }}</div>
                </div>
            </div>
        </div>

        <h3 class="text-xl font-semibold text-gray-900 pt-4">Tabel Data Perhitungan Historis</h3>
        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Y (Penjualan)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">X</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">XY</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">X²</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hasil['dataPerhitungan'] as $data)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::create()->month($data['bulan'])->isoFormat('MMMM') }} {{ $data['tahun'] }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $data['Y'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $data['X'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $data['XY'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $data['XSquare'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-medium">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-right text-sm text-gray-700">Total (n = {{ $hasil['n'] }})</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $hasil['totalY'] }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900">-</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $hasil['totalXY'] }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $hasil['totalXSquare'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <h3 class="text-xl font-semibold text-gray-900 pt-4">Hasil Peramalan 12 Bulan ke Depan</h3>
        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">X (Nilai Berikutnya)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Y (Hasil Peramalan)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hasil['peramalanBerikutnya'] as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $data['periode'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $data['X_next'] }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ $data['Y_forecast'] }} Pcs</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
        <div class="text-center text-gray-500 py-12 border border-gray-200 rounded-lg bg-white">
            <i data-lucide="line-chart" class="w-12 h-12 text-gray-400 mx-auto"></i>
            <p class="mt-3 text-lg font-medium">Belum ada peramalan yang dihitung.</p>
            <p class="mt-1 text-sm">Klik tombol "Jalankan Perhitungan" di atas untuk memproses data.</p>
        </div>
        @endif

    </div>
</x-admin-layout>