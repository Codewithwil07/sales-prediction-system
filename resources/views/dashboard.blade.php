<x-admin-layout>

    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Penjualan (Bln Ini)</h3>
                <i data-lucide="shopping-cart" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $penjualanBulanIni }} Pcs
            </p>
            <p class="text-xs {{ $persenPerubahan >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                {{ $persenPerubahan >= 0 ? '+' : '' }}{{ round($persenPerubahan, 1) }}% dari bulan lalu
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Prediksi (Bln Depan)</h3>
                <i data-lucide="trending-up" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-indigo-600">
                {{ $prediksiBulanDepan }} Pcs
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Berdasarkan Metode Trend Moment
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Akurasi (MAPE)</h3>
                <i data-lucide="target" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $mape }}%
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Rata-rata data terbanding
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Data Historis</h3>
                <i data-lucide="database" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-gray-900">
                {{ $totalDataHistoris }} Bulan
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Total data yang terekam
            </p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Grafik Tren Penjualan
            </h3>
            <div class="h-80 w-full">
                <canvas id="dashboard-chart" data-chart='@json($chartData)'></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Data Penjualan Terbaru
                </h3>
                <a href="{{ route('penjualan.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                    Lihat semua
                </a>
            </div>
            <div class="space-y-5">
                @forelse ($penjualanTerbaru as $item)
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }} {{ $item->tahun }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $item->jumlah_terjual }} Pcs</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>
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
                        datasets: [
                            {
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