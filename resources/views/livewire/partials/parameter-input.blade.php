<div class="bg-gray-900/50 rounded-lg p-3 md:p-4 border border-gray-700">
    <label class="block text-xs md:text-sm font-medium text-gray-300 mb-1.5 md:mb-2">{{ $label }}</label>
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="flex gap-2 flex-1">
            <input type="number" 
                id="input_{{ $name }}"
                value="{{ $value ?? 0 }}"
                step="0.001"
                class="flex-1 min-w-0 bg-gray-800/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none"
                placeholder="0.000">
            @if($unit)
            <span class="text-gray-400 py-2 flex items-center text-sm whitespace-nowrap">{{ $unit }}</span>
            @endif
        </div>
        <button 
            onclick="updateBmsParam('{{ $name }}')"
            class="px-3 md:px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold rounded-lg transition shadow-lg shadow-cyan-500/50 whitespace-nowrap text-sm">
            Update
        </button>
    </div>
</div>
