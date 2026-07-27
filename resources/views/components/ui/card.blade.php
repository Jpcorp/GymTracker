@props(['padding' => 'p-5 sm:p-6'])

<div {{ $attributes->merge(['class' => "bg-slate-900 border border-slate-800 rounded-2xl shadow-xl {$padding}"]) }}>
    {{ $slot }}
</div>
