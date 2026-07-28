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

            <div class="space-y-4 pt-2 border-t border-slate-800">
                <h2 class="text-xs font-bold text-slate-300 pt-4">{{ __('clients.goal_smart_title') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-ui.select label="{{ __('clients.goal_metric_label') }}" name="goal_metric" wire:model="goal_metric">
                        <option value="">{{ __('clients.goal_no_target') }}</option>
                        <option value="weight_kg">{{ __('clients.goal_metric_weight') }}</option>
                        <option value="body_fat_percentage">{{ __('clients.goal_metric_body_fat') }}</option>
                    </x-ui.select>

                    <x-ui.input label="{{ __('clients.goal_target_value') }}" name="goal_target_value" wire:model="goal_target_value" type="number" step="0.01" />

                    <x-ui.input label="{{ __('clients.goal_target_date') }}" name="goal_target_date" wire:model="goal_target_date" type="date" />
                </div>
            </div>

            <div class="space-y-4 pt-2 border-t border-slate-800">
                <h2 class="text-xs font-bold text-slate-300 pt-4">{{ __('clients.nutrition_target_title') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-ui.input label="{{ __('clients.nutrition_target_kcal') }}" name="nutrition_target_kcal" wire:model="nutrition_target_kcal" type="number" min="0" />

                    <x-ui.input label="{{ __('clients.nutrition_target_protein') }}" name="nutrition_target_protein_g" wire:model="nutrition_target_protein_g" type="number" min="0" />

                    <x-ui.input label="{{ __('clients.nutrition_target_notes') }}" name="nutrition_target_notes" wire:model="nutrition_target_notes" type="text" />
                </div>
            </div>

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
