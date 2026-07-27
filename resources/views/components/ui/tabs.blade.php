@props(['tabs' => [], 'model' => 'tab'])

@foreach ($tabs as $t)
    <button
        type="button"
        @click="{{ $model }} = '{{ $t['id'] }}'"
        :class="{{ $model }} === '{{ $t['id'] }}' ? 'bg-cyan-500 text-slate-950 shadow-md' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'"
        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap shrink-0"
    >
        @if (! empty($t['icon']))
            <x-ui.icon :name="$t['icon']" class="w-3.5 h-3.5" />
        @endif
        <span>{{ $t['label'] }}</span>
    </button>
@endforeach
