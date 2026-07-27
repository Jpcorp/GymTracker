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
    <body class="font-sans antialiased bg-slate-950 text-slate-100">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 selection:bg-cyan-500 selection:text-slate-950">
            <a href="/" wire:navigate class="flex items-center gap-2 mb-8 group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="dumbbell" class="w-6 h-6 text-white" />
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">GymTracker</span>
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">PRO</span>
                </div>
            </a>

            <div class="w-full sm:max-w-md">
                <x-ui.card>
                    {{ $slot }}
                </x-ui.card>
            </div>
        </div>
    </body>
</html>
