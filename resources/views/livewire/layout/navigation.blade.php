<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-40 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            <!-- Logo & Brand -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                        <x-ui.icon name="dumbbell" class="w-6 h-6 text-white" />
                    </div>
                    <div class="hidden sm:block">
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">GymTracker</span>
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">PRO</span>
                        </div>
                    </div>
                </a>

                <!-- Navigation tabs -->
                <div class="hidden md:flex items-center gap-1 ml-4 bg-slate-800/80 p-1 rounded-lg border border-slate-700/60">
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-cyan-500 text-slate-950 font-semibold shadow' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('clients.index') }}" wire:navigate
                       class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex items-center gap-1.5 {{ request()->routeIs('clients.*') ? 'bg-cyan-500 text-slate-950 font-semibold shadow' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                        <x-ui.icon name="users" class="w-3.5 h-3.5" />
                        {{ __('Clients') }}
                    </a>
                </div>
            </div>

            <!-- Action buttons & user menu -->
            <div class="flex items-center gap-2">
                <x-ui.button href="{{ route('clients.create') }}" wire:navigate variant="primary" class="hidden sm:inline-flex">
                    <x-ui.icon name="plus" class="w-4 h-4 stroke-[3]" />
                    <span>{{ __('clients.new_client') }}</span>
                </x-ui.button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 hover:border-cyan-500/50 flex items-center justify-center text-xs font-bold text-cyan-400 transition">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>

                <!-- Hamburger -->
                <button @click="open = ! open" class="md:hidden p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                    <x-ui.icon x-show="! open" name="menu" class="w-5 h-5" />
                    <x-ui.icon x-show="open" x-cloak name="x" class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-cloak class="md:hidden border-t border-slate-800 px-4 py-3 space-y-1">
        <a href="{{ route('dashboard') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-500 text-slate-950' : 'text-slate-300 hover:bg-slate-800' }}">
            {{ __('Dashboard') }}
        </a>
        <a href="{{ route('clients.index') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('clients.*') ? 'bg-cyan-500 text-slate-950' : 'text-slate-300 hover:bg-slate-800' }}">
            {{ __('Clients') }}
        </a>
        <a href="{{ route('clients.create') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-medium text-cyan-400 hover:bg-slate-800">
            {{ __('clients.new_client') }}
        </a>

        <div class="border-t border-slate-800 mt-2 pt-2">
            <div class="px-3 py-1 text-xs text-slate-500">{{ auth()->user()->email }}</div>
            <a href="{{ route('profile') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
                {{ __('Profile') }}
            </a>
            <button wire:click="logout" class="w-full text-start px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
                {{ __('Log Out') }}
            </button>
        </div>
    </div>
</nav>
