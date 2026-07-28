@props(['text'])

{{-- ponytail: native `title` attribute gives a free browser tooltip on hover/focus — no JS, no popover
     library. tabindex makes it reachable (and so the tooltip triggerable) via keyboard, not just mouse. --}}
<span
    tabindex="0"
    title="{{ $text }}"
    role="note"
    aria-label="{{ $text }}"
    class="inline-flex items-center justify-center w-4 h-4 rounded-full text-slate-500 hover:text-cyan-400 focus:text-cyan-400 focus:outline-none cursor-help align-middle"
>
    <x-ui.icon name="info" class="w-4 h-4" />
</span>
