{{--
  Partial: bms-setting-row
  Props: id (param key), label, value, unit, step, min, max, hint
  Renders a single row like the JK-BMS app: Label | Input | OK button
--}}
@php
    $rowId    = 'bmsval_' . $id;
    $labelEsc = addslashes($label);
@endphp

<div class="flex items-center gap-3 py-2.5 px-1 border-b border-gray-800/50 last:border-0 group hover:bg-gray-800/20 rounded-lg transition-colors">
    {{-- Label --}}
    <div class="flex-1 min-w-0">
        <div class="text-sm text-gray-300 font-medium truncate">{{ $label }}</div>
        @if(!empty($hint))
        <div class="text-[10px] text-gray-600 mt-0.5 truncate">{{ $hint }}</div>
        @endif
    </div>

    {{-- Input + Unit --}}
    <div class="flex items-center gap-1.5 shrink-0">
        <div class="relative flex items-center bg-gray-950/80 border border-gray-700 rounded-lg focus-within:border-cyan-500 focus-within:ring-1 focus-within:ring-cyan-500/30 transition-all">
            <input type="number"
                   id="{{ $rowId }}"
                   name="{{ $rowId }}"
                   value="{{ $value }}"
                   step="{{ $step ?? '0.001' }}"
                   min="{{ $min ?? '' }}"
                   max="{{ $max ?? '' }}"
                   class="w-24 sm:w-28 bg-transparent text-right text-sm font-mono font-bold text-green-400 px-2 py-1.5 outline-none appearance-none [&::-webkit-inner-spin-button]:opacity-0 [&::-webkit-outer-spin-button]:opacity-0"
                   onkeydown="if(event.key==='Enter') bmsAskWrite('{{ $id }}', '{{ $labelEsc }}')"
            >
            @if(!empty($unit))
            <span class="text-xs text-gray-500 pr-2 font-medium">{{ $unit }}</span>
            @endif
        </div>

        {{-- OK Button --}}
        <button type="button"
                id="ok_{{ $id }}"
                onclick="bmsAskWrite('{{ $id }}', '{{ $labelEsc }}')"
                class="px-3 py-1.5 bg-cyan-600/80 hover:bg-cyan-500 text-white text-xs font-bold rounded-lg transition-all active:scale-95 shrink-0">
            OK
        </button>
    </div>
</div>
