@props(['label' => null, 'name' => null])

@php
    $messages = $name ? $errors->get($name) : [];
    $hasError = count($messages) > 0;
@endphp

<div>
    @if ($label)
        <label @if($name) for="{{ $name }}" @endif class="block text-xs font-semibold text-slate-300 mb-1.5">{{ $label }}</label>
    @endif

    <input
        @if($name) id="{{ $name }}" name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => 'w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-500 px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 transition '
                . ($hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-700 focus:border-cyan-500 focus:ring-cyan-500'),
        ]) }}
    />

    @if ($hasError)
        <ul class="mt-1.5 text-[11px] text-rose-400 space-y-0.5">
            @foreach ($messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    @endif
</div>
