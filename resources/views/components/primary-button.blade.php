<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 border border-transparent rounded-xl font-bold text-xs text-slate-950 tracking-wide shadow focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
