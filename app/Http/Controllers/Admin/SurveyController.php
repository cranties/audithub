<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(): View
    {
        $surveys = Survey::where('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create(): View
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $survey = Survey::create([
            'created_by'  => auth()->id(),
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'schema'      => ['fields' => []],
        ]);

        return redirect()->route('admin.surveys.edit', $survey)
            ->with('status', 'Survey created. Start building your form.');
    }

    public function edit(Survey $survey): View
    {
        $this->authorize('update', $survey);

        return view('admin.surveys.edit', compact('survey'));
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        $this->authorize('delete', $survey);

        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('status', 'Survey deleted.');
    }

    public function duplicate(Survey $survey): RedirectResponse
    {
        $this->authorize('view', $survey);

        $copy = $survey->duplicate();

        return redirect()->route('admin.surveys.edit', $copy)
            ->with('status', 'Survey duplicated as a new draft.');
    }
}
