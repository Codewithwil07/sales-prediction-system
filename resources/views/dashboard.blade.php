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
                1,250 Pcs </p>
            <p class="text-xs text-green-600 mt-1">
                +5.2% dari bulan lalu
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Prediksi (Bln Depan)</h3>
                <i data-lucide="trending-up" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-indigo-600">
                1,312 Pcs </p>
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
                92.5% </p>
            <p class="text-xs text-gray-500 mt-1">
                Rata-rata 12 bulan terakhir
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-sm font-medium text-gray-500">Data Historis</h3>
                <i data-lucide="database" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-3xl font-semibold text-gray-900">
                24 Bulan </p>
            <p class="text-xs text-gray-500 mt-1">
                Total data yang terekam
            </p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Grafik Tren Penjualan vs Peramalan
            </h3>
            <div id="dashboard-chart" class="h-80"></div>
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Data Penjualan Terbaru
                </h3>
                <a href="#" class="text-sm font-medium text-indigo-600 hover:underline">
                    Lihat semua
                </a>
            </div>

            <div class="space-y-5">

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Oktober 2025</p>
                        <p class="text-sm text-gray-500">1,250 Pcs</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">September 2025</p>
                        <p class="text-sm text-gray-500">1,180 Pcs</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Agustus 2025</p>
                        <p class="text-sm text-gray-500">1,210 Pcs</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Juli 2025</p>
                        <p class="text-sm text-gray-500">1,150 Pcs</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>


@push('scripts')
<script>
    // Pastikan DOM sudah dimuat
    document.addEventListener('DOMContentLoaded', function() {

        // Opsi untuk grafik (ala shadcn)
        var options = {
            chart: {
                type: 'area', // Tipe grafik
                height: 320, // Tinggi grafik
                toolbar: {
                    show: false // Sembunyikan toolbar
                },
                zoom: {
                    enabled: false
                }
            },
            series: [{
                    name: 'Penjualan Aktual',
                    // Data dummy (nanti ganti dengan data dari database)
                    data: [1100, 1150, 1210, 1180, 1250]
                },
                {
                    name: 'Hasil Peramalan',
                    // Data dummy (nanti ganti dengan data dari database)
                    data: [1090, 1160, 1200, 1190, 1240]
                }
            ],
            dataLabels: {
                enabled: false // Sembunyikan label data di titik
            },
            stroke: {
                curve: 'smooth', // Buat garis melengkung
                width: 2
            },
            colors: ['#4f46e5', '#10b981'], // Warna Indigo dan Hijau
            xaxis: {
                // Label sumbu X (nanti ganti dengan data dari database)
                categories: ['Juli', 'Agustus', 'September', 'Oktober', 'November'],
                labels: {
                    style: {
                        colors: '#6b7280' // Warna abu-abu
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280'
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb', // Garis grid abu-abu
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                markers: {
                    radius: 12
                }
            }
        };

        // Buat dan render grafik
        var chart = new ApexCharts(document.querySelector("#dashboard-chart"), options);
        chart.render();
    });
</script>
@endpush