<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @filamentStyles
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100">

        <aside class="w-64 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">
            <div class="h-16 flex items-center justify-center border-b">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-bold text-indigo-600">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                    <span>JAYA FARID</span>
                </a>
            </div>

            @include('layouts.sidebar')
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-end px-6">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        <span>{{ Auth::user()->username }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border py-1 z-10"
                        style="display: none;">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Log Out</span>
                            </a>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">

                @isset($header)
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">
                    {{ $header }}
                </h1>
                @endisset

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
    @filamentScripts

    @livewire('notifications')

    @stack('scripts')
</body>

</html>