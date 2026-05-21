<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Surveys</h2>
            <a href="{{ route('admin.surveys.create') }}"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition">
                + New Survey
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-6 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if($surveys->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <p class="text-4xl mb-4">📋</p>
                    <p class="text-lg font-medium">No surveys yet.</p>
                    <a href="{{ route('admin.surveys.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">Create your first survey →</a>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Submissions</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Created</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($surveys as $survey)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $survey->title }}
                                        @if($survey->description)
                                            <p class="text-xs text-gray-400 font-normal mt-0.5">{{ Str::limit($survey->description, 60) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($survey->is_locked)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Published</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <a href="{{ route('admin.surveys.submissions.index', $survey) }}" class="hover:underline text-indigo-600">
                                            {{ $survey->submissions_count ?? $survey->submissions()->count() }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400">{{ $survey->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('admin.surveys.edit', $survey) }}"
                                            class="text-indigo-600 hover:underline mr-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.surveys.duplicate', $survey) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-500 hover:underline mr-3">Duplicate</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}" class="inline"
                                            onsubmit="return confirm('Delete this survey?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $surveys->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
