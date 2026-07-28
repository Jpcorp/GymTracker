@props(['color' => 'slate'])

@php
    $colors = [
        'emerald' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        'amber' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
        'rose' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
        'cyan' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
        'violet' => 'bg-violet-500/20 text-violet-400 border-violet-500/30',
        'slate' => 'bg-slate-800 text-slate-300 border-slate-700',
    ];
    $classes = $colors[$color] ?? $colors['slate'];
@endphp

<span {{ $attributes->merge(['class' => "text-[10px] font-bold px-2 py-0.5 rounded-full border whitespace-nowrap {$classes}"]) }}>
    {{ $slot }}
</span>
