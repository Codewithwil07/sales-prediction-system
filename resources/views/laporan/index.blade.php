<x-admin-layout>
    <x-slot name="header">
        Laporan Akurasi & Hasil Peramalan
    </x-slot>

    <div class="space-y-6">

        @if($peramalanTerbaru)

        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Metrik Akurasi (Perhitungan Terakhir)
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Periode Perhitungan: {{ $peramalanTerbaru->periode_perhitungan }}
            </p>

            @if($metrics)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">MAD</div>
                    <div class="text-2xl font-bold text-gray-800" title="Rata-rata selisih absolut">
                        {{ round($metrics['mad'], 3) }}
                    </div>
                </div>
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">MSE</div>
                    <div class="text-2xl font-bold text-gray-800" title="Rata-rata kuadrat selisih">
                        {{ round($metrics['mse'], 3) }}
                    </div>
                </div>
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">MAPE</div>
                    <div class="text-2xl font-bold text-indigo-600" title="Rata-rata persentase error">
                        {{ round($metrics['mape'], 3) }} %
                    </div>
                </div>
                <div class="bg-white p-4 border rounded-md">
                    <div class="text-xs text-gray-500">Data Pembanding</div>
                    <div class="text-2xl font-bold text-gray-800" title="Jumlah data yang dibandingkan">
                        {{ $metrics['data_compared'] }} Bulan
                    </div>
                </div>
            </div>
            @else
            <p class="text-gray-600">Metrik akurasi belum dapat dihitung. Belum ada data penjualan aktual yang cocok dengan periode peramalan.</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Grafik Perbandingan Aktual vs Peramalan
            </h3>
            <div class="h-80 w-full">
                <canvas id="laporan-chart" data-chart='@json($chartData)'></canvas>
            </div>
        </div>

        <h3 class="text-xl font-semibold text-gray-900 pt-4">Tabel Perbandingan Akurasi (Data Historis)</h3>
        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktual (Y)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peramalan (Y')</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih (Y - Y')</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih²</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Error</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dataPerbandingan as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $data['periode'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $data['aktual'] }}</td>
                        <td class="px-6 py-4 text-sm text-indigo-600 font-medium">{{ $data['peramalan'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ round($data['error'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ round($data['error_square'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-red-500">{{ round($data['percentage_error'], 2) }} %</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data historis yang dapat dibandingkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="text-xl font-semibold text-gray-900 pt-4">Tabel Prediksi Murni (Masa Depan)</h3>
        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Peramalan (Pcs)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dataPrediksiMurni as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $data['periode'] }}</td>
                        <td class="px-6 py-4 text-sm text-indigo-600 font-medium">{{ $data['peramalan'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data prediksi masa depan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        @else
        <div class="text-center text-gray-500 py-12 border border-gray-200 rounded-lg bg-white">
            <i data-lucide="file-warning" class="w-12 h-12 text-gray-400 mx-auto"></i>
            <p class="mt-3 text-lg font-medium">Belum Ada Laporan</p>
            <p class="mt-1 text-sm">
                Silakan pergi ke menu <a href="{{ route('peramalan.index') }}" class="text-indigo-600 font-medium hover:underline">Peramalan</a>
                dan jalankan perhitungan terlebih dahulu.
            </p>
        </div>
        @endif

    </div>
</x-admin-layout>


<script>
    // Kita bungkus DOMContentLoaded di sini biar aman
    document.addEventListener('DOMContentLoaded', function() {

        // Ambil elemen canvas
        const chartCanvas = document.querySelector("#dashboard-chart");

        // Cek jika elemen ada
        if (chartCanvas) {
            // Ambil data dari atribut 'data-chart'
            const chartData = JSON.parse(chartCanvas.dataset.chart || '{}');

            // Cek datanya kosong atau nggak
            if (chartData && chartData.labels && chartData.labels.length > 0) {
                const ctx = chartCanvas.getContext('2d');

                new Chart(ctx, {
                    type: 'line', // Tipe grafik
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                                label: 'Penjualan Aktual',
                                data: chartData.aktual,
                                borderColor: '#312e81', // Indigo-900
                                backgroundColor: 'rgba(49, 46, 129, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3 // Bikin melengkung
                            },
                            {
                                label: 'Hasil Peramalan',
                                data: chartData.prediksi,
                                borderColor: '#4f46e5', // Indigo-600
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 2,
                                borderDash: [5, 5], // Bikin putus-putus
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Penting agar pas di div h-80
                        scales: {
                            y: {
                                beginAtZero: false, // Angka (Y-Axis) otomatis muncul
                                grid: {
                                    drawBorder: false,
                                    borderDash: [5, 5] // INI FITUR YANG LO MINTA (Garis putus-putus)
                                }
                            },
                            x: {
                                grid: {
                                    display: false // Sembunyikan garis X
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end'
                            }
                        }
                    }
                });
            } else {
                // Kalo datanya kosong, kita tulis placeholder
                chartCanvas.parentElement.innerHTML = '<div class="h-80 w-full flex items-center justify-center text-center text-gray-400 bg-gray-50 rounded-md border"><p>Data grafik kosong.<br>Jalankan perhitungan di menu "Peramalan" dulu.</p></div>';
            }
        }
    });
</script>