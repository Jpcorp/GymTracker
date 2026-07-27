<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <x-ui.card>
        <p class="text-sm text-slate-300">
            {{ __("You're logged in!") }}
        </p>
    </x-ui.card>
</x-app-layout>
