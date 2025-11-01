<nav class="flex-1 overflow-y-auto py-4">
    <ul class="space-y-1 px-4">

        <li>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('penjualan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium
              {{ request()->routeIs('penjualan.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="database" class="w-5 h-5"></i>
                <span>Data Penjualan</span>
            </a>
        </li>

        <li>
            <a href="{{route('peramalan.index')}}" class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium
                      {{ request()->routeIs('peramalan.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="line-chart" class="w-5 h-5"></i>
                <span>Peramalan</span>
            </a>
        </li>

        <li>
            <a href="{{route('laporan.index')}}" class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium
                      {{ request()->routeIs('laporan.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Laporan</span>
            </a>
        </li>
    </ul>
</nav>