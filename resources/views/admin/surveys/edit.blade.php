<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.surveys.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Surveys</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $survey->title }}</h2>
        </div>
    </x-slot>

    <livewire:admin.form-builder :survey="$survey" />

</x-app-layout>
