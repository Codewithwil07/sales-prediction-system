@props(['name', 'title'])

<div
    x-show="{{ $name }}"
    x-on:keydown.escape.window="{{ $name }} = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/75"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;">
    <div
        x-show="{{ $name }}"
        x-on:click.away="{{ $name }} = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="w-full max-w-lg bg-white rounded-lg shadow-xl p-6">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold">{{ $title }}</h3>
            <button @click="{{ $name }} = false" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="mt-4">
            {{ $slot }}
        </div>
    </div>
</div>