@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-800 text-sm text-slate-200 placeholder-slate-500 border-slate-700 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg shadow-sm']) }}>
