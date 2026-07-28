<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 selection:bg-cyan-500 selection:text-slate-950">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-slate-900">
                <div class="max-w-3xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                        <x-ui.icon name="dumbbell" class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-base tracking-tight text-white">GymTracker</span>
                        <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">PRO</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-6 space-y-6">
                @yield('content')
            </main>

            <footer class="border-t border-slate-900 py-4 text-center text-[11px] text-slate-500">
                GymTracker <span class="text-cyan-400 font-semibold">PRO</span> &middot; Vista de solo lectura para el cliente
            </footer>
        </div>
    </body>
</html>
