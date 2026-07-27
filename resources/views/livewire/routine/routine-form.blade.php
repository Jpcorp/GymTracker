<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-white">{{ $routine ? __('routines.form.edit_title') : __('routines.form.new_title') }}</h1>
        <a href="{{ route('clients.routines.index', $client) }}" wire:navigate class="text-xs font-semibold text-slate-400 hover:text-slate-200">{{ __('routines.form.back_to_routines') }}</a>
    </div>

    <x-ui.card>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-ui.input label="{{ __('routines.name') }}" name="name" wire:model="name" type="text" />
            </div>

            <div class="sm:col-span-2">
                <x-ui.textarea label="{{ __('routines.form.description') }}" name="description" wire:model="description" rows="3" />
            </div>

            <x-ui.input label="{{ __('routines.weekly_frequency') }}" name="weekly_frequency" wire:model="weekly_frequency" type="number" min="1" max="7" />

            <div class="flex items-end pb-2">
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300">
                    <input type="checkbox" wire:model="is_active" class="rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500" />
                    {{ __('routines.form.active_routine') }}
                </label>
            </div>

            <x-ui.input label="{{ __('routines.form.start_date') }}" name="start_date" wire:model="start_date" type="date" />
            <x-ui.input label="{{ __('routines.form.end_date') }}" name="end_date" wire:model="end_date" type="date" />

            <div class="sm:col-span-2 flex justify-end">
                <x-ui.button type="submit" variant="primary">{{ __('routines.form.save') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
