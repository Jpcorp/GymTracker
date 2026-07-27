@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-slate-300 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
