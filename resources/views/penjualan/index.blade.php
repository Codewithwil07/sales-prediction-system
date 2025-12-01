<x-admin-layout>
    <x-slot name="header">
        Data Penjualan Historis
    </x-slot>

    <div
        x-data="{
            createModal: {{ $errors->any() && old('form_type') === 'create' ? 'true' : 'false' }},
            editModal: {{ $errors->any() && old('form_type') === 'edit' ? 'true' : 'false' }},
            deleteModal: false,
            editItem: { 
                id: '{{ old('id') }}', 
                bulan: '{{ old('bulan') }}', 
                tahun: '{{ old('tahun') }}', 
                jumlah_terjual: '{{ old('jumlah_terjual') }}' 
            },
            deleteUrl: '',
            editUrl: '{{ $errors->any() && old('form_type') === 'edit' ? route('penjualan.update', old('id')) : '' }}'
        }"
        class="space-y-6">

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">

            <form action="{{ route('penjualan.index') }}" method="GET" class="flex-grow">
                <div class="flex flex-col sm:flex-row items-center gap-2">

                    <div class="relative flex-grow w-full sm:w-auto">
                        <label for="search" class="sr-only">Cari</label>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Cari bulan/tahun..." value="{{ $filters['search'] ?? '' }}">
                    </div>

                    <div class="w-full sm:w-auto">
                        <label for="filter_tahun" class="sr-only">Filter Tahun</label>
                        <select name="filter_tahun" id="filter_tahun"
                            class="block w-full py-2 pl-3 pr-8 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Semua Tahun</option>
                            @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun }}" @selected($filters['filter_tahun'] ?? ''==$tahun)>
                                {{ $tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-auto">
                        <label for="sort" class="sr-only">Urutkan</label>
                        <select name="sort" id="sort"
                            class="block w-full py-2 pl-3 pr-8 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="terbaru" @selected($sort==='terbaru' )>Urutan Terbaru</option>
                            <option value="terlama" @selected($sort==='terlama' )>Urutan Terlama</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>

                    <a href="{{ route('penjualan.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Reset
                    </a>

                </div>
            </form>

            <div class="flex-shrink-0">
                <button
                    @click="createModal = true"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Data</span>
                </button>
            </div>
        </div>
        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah (Pcs)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dataPenjualan as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration + $dataPenjualan->firstItem() - 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $item->bulan }} ({{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }})
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tahun }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->jumlah_terjual }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                            <button
                                @click="
                                    editModal = true;
                                    editUrl = '{{ route('penjualan.update', $item->id) }}';
                                    editItem = { 
                                        id: '{{ $item->id }}', 
                                        bulan: '{{ $item->bulan }}', 
                                        tahun: '{{ $item->tahun }}', 
                                        jumlah_terjual: '{{ $item->jumlah_terjual }}' 
                                    };
                                "
                                class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                            <button
                                @click="
                                    deleteModal = true;
                                    deleteUrl = '{{ route('penjualan.destroy', $item->id) }}';
                                "
                                class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="database-zap" class="w-12 h-12 text-gray-400"></i>
                                <p class="mt-3 text-lg font-medium">Data tidak ditemukan.</p>
                                <p class="mt-1 text-sm">Coba ubah filter atau tambahkan data baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $dataPenjualan->links() }}
        </div>

        <x-modal name="createModal" title="Tambah Data Penjualan Baru">
            <form action="{{ route('penjualan.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div>
                    <x-input-label for="create_bulan" :value="__('Bulan (Angka 1-12)')" />
                    <x-text-input
                        id="create_bulan" class="block mt-1 w-full"
                        type="number" name="bulan" :value="old('bulan')"
                        required autofocus min="1" max="12"
                        placeholder="Contoh: 1 (untuk Januari)" />
                    <x-input-error :messages="$errors->get('bulan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="create_tahun" :value="__('Tahun')" />
                    <x-text-input id="create_tahun" class="block mt-1 w-full" type="number" name="tahun" :value="old('tahun')" required placeholder="Contoh: 2024" />
                    <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="create_jumlah" :value="__('Jumlah Terjual (Pcs)')" />
                    <x-text-input id="create_jumlah" class="block mt-1 w-full" type="number" name="jumlah_terjual" :value="old('jumlah_terjual')" required />
                    <x-input-error :messages="$errors->get('jumlah_terjual')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button @click="createModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <x-primary-button>
                        Simpan Data
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="editModal" title="Edit Data Penjualan">
            <form x-bind:action="editUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="id" x-bind:value="editItem.id">
                <div>
                    <x-input-label for="edit_bulan" :value="__('Bulan (Angka 1-12)')" />
                    <x-text-input
                        id="edit_bulan" class="block mt-1 w-full"
                        type="number" name="bulan" x-bind:value="editItem.bulan"
                        required autofocus min="1" max="12"
                        placeholder="Contoh: 1 (untuk Januari)" />
                    <x-input-error :messages="$errors->get('bulan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="edit_tahun" :value="__('Tahun')" />
                    <x-text-input id="edit_tahun" class="block mt-1 w-full" type="number" name="tahun" x-bind:value="editItem.tahun" required placeholder="Contoh: 2024" />
                    <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="edit_jumlah" :value="__('Jumlah Terjual (Pcs)')" />
                    <x-text-input id="edit_jumlah" class="block mt-1 w-full" type="number" name="jumlah_terjual" x-bind:value="editItem.jumlah_terjual" required />
                    <x-input-error :messages="$errors->get('jumlah_terjual')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button @click="editModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <x-primary-button>
                        Update Data
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="deleteModal" title="Hapus Data Penjualan">
            <form x-bind:action="deleteUrl" method="POST">
                @csrf
                @method('DELETE')
                <p class="text-gray-600">
                    Apakah Anda yakin ingin menghapus data ini?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3 pt-6">
                    <button @click="deleteModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </x-modal>

    </div>
</x-admin-layout>