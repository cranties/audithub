<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Form Builder</h1>
            @if($survey->is_locked)
                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    🔒 LOCKED — schema is immutable
                </span>
            @endif
        </div>

        <div class="flex gap-2">
            @if($survey->is_locked)
                <button wire:click="duplicateSurvey"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition">
                    Duplicate to Edit
                </button>
            @else
                <button wire:click="saveSchema"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm transition">
                    Save Draft
                </button>
                <button wire:click="publishSurvey"
                    wire:confirm="Publish this survey? The schema will be locked and cannot be changed."
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow transition">
                    Publish Survey
                </button>
            @endif
        </div>
    </div>

    {{-- ── Flash messages ── --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @error('locked') <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ $message }}</div> @enderror
    @error('publish') <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ $message }}</div> @enderror

    {{-- Public link when published --}}
    @if($survey->public_token)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm font-medium text-blue-800 mb-1">Public survey URL:</p>
            <a href="{{ route('survey.show', $survey->public_token) }}" target="_blank"
                class="text-sm text-blue-600 underline break-all">
                {{ route('survey.show', $survey->public_token) }}
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left panel: Survey meta + field type palette ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Title & Description --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-3">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Survey Info</h2>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                    <input type="text" wire:model="title"
                        @if($survey->is_locked) disabled @endif
                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @if($survey->is_locked) bg-gray-50 @endif"
                        placeholder="Survey title…">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea wire:model="description" rows="3"
                        @if($survey->is_locked) disabled @endif
                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @if($survey->is_locked) bg-gray-50 @endif"
                        placeholder="Optional description…"></textarea>
                </div>
            </div>

            {{-- Field type palette --}}
            @if(!$survey->is_locked)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Add Field</h2>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($fieldTypes as $type => $label)
                        <button wire:click="addField('{{ $type }}')"
                            class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-700 border border-gray-200 rounded-lg transition">
                            <span class="text-base leading-none">
                                @switch($type)
                                    @case('text')     📝 @break
                                    @case('textarea') 📄 @break
                                    @case('number')   🔢 @break
                                    @case('date')     📅 @break
                                    @case('select')   ▼  @break
                                    @case('radio')    🔘 @break
                                    @case('checkbox') ✅ @break
                                    @case('file')     📎 @break
                                    @case('rating')   ⭐ @break
                                @endswitch
                            </span>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ── Right panel: Field list (sortable) ── --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">
                    Fields <span class="ml-2 font-normal text-gray-400">({{ count($fields) }})</span>
                </h2>

                @if(empty($fields))
                    <p class="text-sm text-gray-400 text-center py-12">
                        {{ $survey->is_locked ? 'No fields in schema.' : 'Add fields from the left panel.' }}
                    </p>
                @else
                    {{-- Sortable container — Alpine.js + Sortable.js handle drag order --}}
                    <div
                        x-data="{
                            initSortable() {
                                Sortable.create(this.$el, {
                                    animation: 150,
                                    handle: '.drag-handle',
                                    ghostClass: 'opacity-40',
                                    disabled: {{ $survey->is_locked ? 'true' : 'false' }},
                                    onEnd: (evt) => {
                                        const ids = Array.from(this.$el.querySelectorAll('[data-field-id]'))
                                            .map(el => el.dataset.fieldId);
                                        $wire.reorderFields(ids);
                                    }
                                });
                            }
                        }"
                        x-init="initSortable()"
                        class="space-y-3"
                    >
                        @foreach($fields as $index => $field)
                            <div wire:key="field-{{ $field['id'] }}"
                                data-field-id="{{ $field['id'] }}"
                                class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-white transition">

                                {{-- Field header --}}
                                <div class="flex items-center gap-3 mb-3">
                                    @if(!$survey->is_locked)
                                    <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-600 text-lg select-none">⠿</span>
                                    @endif

                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-700 uppercase">
                                        {{ $field['type'] }}
                                    </span>

                                    <span class="flex-1 text-sm font-medium text-gray-700 truncate">
                                        {{ $field['label'] ?: '(no label)' }}
                                    </span>

                                    @if(!$survey->is_locked)
                                    <button wire:click="removeField('{{ $field['id'] }}')"
                                        wire:confirm="Remove this field?"
                                        class="text-red-400 hover:text-red-600 text-sm transition">✕</button>
                                    @endif
                                </div>

                                @if(!$survey->is_locked)
                                {{-- Label --}}
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Label *</label>
                                    <input type="text"
                                        wire:model="fields.{{ $index }}.label"
                                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Question label…">
                                </div>

                                {{-- Placeholder (for text/textarea/number) --}}
                                @if(in_array($field['type'], ['text', 'textarea', 'number']))
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Placeholder</label>
                                    <input type="text"
                                        wire:model="fields.{{ $index }}.placeholder"
                                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Input placeholder…">
                                </div>
                                @endif

                                {{-- Options (for select/radio/checkbox) --}}
                                @if(in_array($field['type'], ['select', 'radio', 'checkbox']))
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Options</label>
                                    @foreach(($field['options'] ?? []) as $optIdx => $option)
                                        <div class="flex gap-2 mb-1">
                                            <input type="text"
                                                wire:model="fields.{{ $index }}.options.{{ $optIdx }}"
                                                class="flex-1 text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Option {{ $optIdx + 1 }}…">
                                            <button wire:click="removeOption('{{ $field['id'] }}', {{ $optIdx }})"
                                                class="text-red-400 hover:text-red-600 text-xs px-2">✕</button>
                                        </div>
                                    @endforeach
                                    <button wire:click="addOption('{{ $field['id'] }}')"
                                        class="text-xs text-indigo-600 hover:underline mt-1">+ Add option</button>
                                </div>
                                @endif

                                {{-- Required toggle --}}
                                <div class="flex items-center gap-2 mt-2">
                                    <input type="checkbox" id="req-{{ $field['id'] }}"
                                        wire:model="fields.{{ $index }}.required"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="req-{{ $field['id'] }}" class="text-xs text-gray-600">Required field</label>
                                </div>
                                @endif {{-- /!is_locked --}}

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
