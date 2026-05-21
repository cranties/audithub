<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.surveys.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Surveys</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-semibold text-xl text-gray-800">{{ $survey->title }} — Submissions</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($submissions->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <p class="text-4xl mb-4">📭</p>
                    <p>No submissions yet.</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">UUID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Submitted</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">PDF</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ Str::limit($sub->uuid, 12) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $sub->submitted_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-400">{{ $sub->submitter_ip }}</td>
                                    <td class="px-6 py-4">
                                        <livewire:admin.download-pdf-button :submission="$sub" :key="'pdf-'.$sub->id" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.surveys.submissions.show', [$survey, $sub]) }}"
                                            class="text-sm text-indigo-600 hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $submissions->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
