<div>
    {{-- Idle state --}}
    <button
        wire:click="download"
        wire:loading.attr="disabled"
        wire:target="download"
        type="button"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 disabled:opacity-50 disabled:cursor-wait transition"
    >
        {{-- Spinner (visible only while loading) --}}
        <svg
            wire:loading
            wire:target="download"
            class="animate-spin h-4 w-4 text-indigo-500 shrink-0"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>

        {{-- PDF icon (hidden while loading) --}}
        <svg
            wire:loading.remove
            wire:target="download"
            class="h-4 w-4 shrink-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>

        {{-- Label --}}
        <span wire:loading.remove wire:target="download">Download PDF</span>
        <span wire:loading       wire:target="download">Generazione…</span>
    </button>
</div>
