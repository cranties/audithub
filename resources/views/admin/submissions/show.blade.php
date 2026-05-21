<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.surveys.submissions.index', $survey) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Submissions</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-semibold text-xl text-gray-800">Submission Detail</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Meta --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-wrap gap-6">
                <div>
                    <p class="text-xs text-gray-400 mb-1">UUID</p>
                    <p class="text-sm font-mono text-gray-700">{{ $submission->uuid }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Submitted</p>
                    <p class="text-sm text-gray-700">{{ $submission->submitted_at?->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">IP</p>
                    <p class="text-sm text-gray-500">{{ $submission->submitter_ip }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Report</p>
                    <livewire:admin.download-pdf-button :submission="$submission" />
                </div>
            </div>

            {{-- Answers --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">Answers</h3>

                @php $fields = $survey->schema['fields'] ?? []; @endphp

                <dl class="space-y-4">
                    @foreach($fields as $field)
                        @php
                            $answer = $submission->answers[$field['id']] ?? null;
                            $isFile = $field['type'] === 'file';
                        @endphp
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <dt class="text-xs font-medium text-gray-500 mb-1">
                                {{ $field['label'] ?: $field['id'] }}
                                <span class="ml-1 px-1.5 py-0.5 bg-gray-100 text-gray-400 rounded text-xs">{{ $field['type'] }}</span>
                            </dt>
                            <dd class="text-sm text-gray-800">
                                @if($isFile && $answer)
                                    @php
                                        $fileRecord = $submission->files->firstWhere('field_key', $field['id']);
                                    @endphp
                                    @if($fileRecord)
                                        <a href="{{ route('admin.files.download', $fileRecord) }}"
                                            class="text-indigo-600 hover:underline">
                                            {{ $fileRecord->original_name }}
                                        </a>
                                    @else
                                        <em class="text-gray-400">Attachment (no record)</em>
                                    @endif
                                @elseif(is_array($answer))
                                    {{ implode(', ', $answer) }}
                                @elseif($answer !== null && $answer !== '')
                                    {{ $answer }}
                                @else
                                    <em class="text-gray-400">—</em>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>

        </div>
    </div>
</x-app-layout>
