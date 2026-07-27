@props(['variant' => 'secondary', 'href' => null])

@php
    $base = 'inline-flex items-center justify-center gap-1.5 rounded-xl text-xs font-bold transition px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap';
    $variants = [
        'primary' => 'bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow',
        'secondary' => 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700',
        'danger' => 'bg-slate-800 hover:bg-rose-950 text-rose-400 border border-slate-700',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['secondary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
