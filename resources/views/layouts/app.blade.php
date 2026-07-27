<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 selection:bg-cyan-500 selection:text-slate-950">
        <div class="min-h-screen flex flex-col">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    {{ $header }}
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-900 py-4 text-center text-[11px] text-slate-500">
                GymTracker <span class="text-cyan-400 font-semibold">PRO</span>
            </footer>
        </div>
    </body>
</html>
