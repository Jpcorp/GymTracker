<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-white">
            {{ $client ? __('clients.edit_client') : __('clients.new_client') }}
        </h1>
        <a href="{{ route('clients.index') }}" wire:navigate class="text-xs font-semibold text-slate-400 hover:text-slate-200">
            {{ __('clients.back_to_list') }}
        </a>
    </div>

    <x-ui.card>
        <form wire:submit="save" class="space-y-4">
            <x-ui.input label="{{ __('clients.name') }}" name="name" wire:model="name" type="text" />

            <x-ui.input label="{{ __('clients.email') }}" name="email" wire:model="email" type="email" />

            <x-ui.input label="{{ __('clients.phone') }}" name="phone" wire:model="phone" type="text" />

            <x-ui.input label="{{ __('clients.birth_date') }}" name="birth_date" wire:model="birth_date" type="date" />

            <x-ui.select label="{{ __('clients.gender') }}" name="gender" wire:model="gender">
                <option value="">{{ __('clients.select_placeholder') }}</option>
                <option value="male">{{ __('clients.genders.male') }}</option>
                <option value="female">{{ __('clients.genders.female') }}</option>
                <option value="other">{{ __('clients.genders.other') }}</option>
            </x-ui.select>

            <x-ui.input label="{{ __('clients.start_date') }}" name="start_date" wire:model="start_date" type="date" />

            <x-ui.textarea label="{{ __('clients.goal') }}" name="goal" wire:model="goal" rows="3" />

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('clients.index') }}" wire:navigate class="text-xs font-semibold text-slate-400 hover:text-slate-200">
                    {{ __('clients.cancel') }}
                </a>
                <x-ui.button type="submit" variant="primary">
                    {{ __('clients.save') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
