<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $survey->title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">

<div class="w-full max-w-2xl">
    {{-- Survey header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $survey->title }}</h1>
        @if($survey->description)
            <p class="mt-2 text-gray-500">{{ $survey->description }}</p>
        @endif
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-semibold text-red-700 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('survey.store', $survey->public_token) }}"
        enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        @foreach($survey->schema['fields'] ?? [] as $field)
            @php
                $id       = $field['id'];
                $label    = $field['label'] ?? 'Question';
                $required = (bool) ($field['required'] ?? false);
                $type     = $field['type'];
                $ph       = $field['placeholder'] ?? '';
                $opts     = $field['options'] ?? [];
            @endphp

            <div>
                <label for="field-{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $label }}
                    @if($required) <span class="text-red-500">*</span> @endif
                </label>

                @switch($type)

                    @case('text')
                        <input type="text" id="field-{{ $id }}" name="{{ $id }}"
                            value="{{ old($id) }}"
                            placeholder="{{ $ph }}"
                            {{ $required ? 'required' : '' }}
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error($id) border-red-400 @enderror">
                        @break

                    @case('textarea')
                        <textarea id="field-{{ $id }}" name="{{ $id }}" rows="4"
                            placeholder="{{ $ph }}"
                            {{ $required ? 'required' : '' }}
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error($id) border-red-400 @enderror">{{ old($id) }}</textarea>
                        @break

                    @case('number')
                        <input type="number" id="field-{{ $id }}" name="{{ $id }}"
                            value="{{ old($id) }}"
                            placeholder="{{ $ph }}"
                            {{ $required ? 'required' : '' }}
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error($id) border-red-400 @enderror">
                        @break

                    @case('date')
                        <input type="date" id="field-{{ $id }}" name="{{ $id }}"
                            value="{{ old($id) }}"
                            {{ $required ? 'required' : '' }}
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error($id) border-red-400 @enderror">
                        @break

                    @case('select')
                        <select id="field-{{ $id }}" name="{{ $id }}"
                            {{ $required ? 'required' : '' }}
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error($id) border-red-400 @enderror">
                            <option value="">— Select —</option>
                            @foreach($opts as $opt)
                                <option value="{{ $opt }}" {{ old($id) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @break

                    @case('radio')
                        <div class="space-y-2 mt-1">
                            @foreach($opts as $opt)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="radio" name="{{ $id }}" value="{{ $opt }}"
                                        {{ old($id) === $opt ? 'checked' : '' }}
                                        {{ $required ? 'required' : '' }}
                                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $opt }}
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('checkbox')
                        <div class="space-y-2 mt-1">
                            @foreach($opts as $opt)
                                @php $checked = in_array($opt, old($id, [])); @endphp
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="{{ $id }}[]" value="{{ $opt }}"
                                        {{ $checked ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $opt }}
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('file')
                        <input type="file" id="field-{{ $id }}" name="{{ $id }}"
                            accept=".jpg,.jpeg,.png,.pdf"
                            {{ $required ? 'required' : '' }}
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, PDF — max 5 MB</p>
                        @break

                    @case('rating')
                        <div x-data="{ rating: {{ old($id, 0) }}, hovered: 0 }" class="flex gap-1 mt-1">
                            @for($star = 1; $star <= 5; $star++)
                                <button type="button"
                                    @click="rating = {{ $star }}"
                                    @mouseenter="hovered = {{ $star }}"
                                    @mouseleave="hovered = 0"
                                    :class="(hovered || rating) >= {{ $star }} ? 'text-amber-400' : 'text-gray-300'"
                                    class="text-3xl transition-colors focus:outline-none">★</button>
                            @endfor
                            <input type="hidden" name="{{ $id }}" :value="rating"
                                {{ $required ? 'required' : '' }}>
                        </div>
                        @break

                @endswitch

                @error($id)
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="pt-2">
            <button type="submit"
                class="w-full py-3 px-6 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition">
                Submit
            </button>
        </div>
    </form>

    <p class="mt-6 text-center text-xs text-gray-400">Powered by AuditHub</p>
</div>

</body>
</html>
