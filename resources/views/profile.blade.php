<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card padding="p-4 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </x-ui.card>

        <x-ui.card padding="p-4 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </x-ui.card>

        <x-ui.card padding="p-4 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
