<?php

namespace App\Livewire\Admin;

use App\Models\Survey;
use Illuminate\Support\Str;
use Livewire\Component;

class FormBuilder extends Component
{
    public Survey $survey;

    public string $title       = '';
    public string $description = '';
    public array  $fields      = [];

    /** Field types available to add */
    public array $fieldTypes = [
        'text'     => 'Short Text',
        'textarea' => 'Long Text',
        'number'   => 'Number',
        'date'     => 'Date',
        'select'   => 'Dropdown',
        'radio'    => 'Single Choice',
        'checkbox' => 'Multi Choice',
        'file'     => 'File Upload',
        'rating'   => 'Rating (1–5)',
    ];

    public function mount(Survey $survey): void
    {
        $this->survey      = $survey;
        $this->title       = $survey->title;
        $this->description = $survey->description ?? '';
        $this->fields      = $survey->schema['fields'] ?? [];
    }

    // ── Mutations (all guarded by lock) ────────────────────────────────────

    public function addField(string $type): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        $this->fields[] = [
            'id'          => (string) Str::uuid(),
            'type'        => $type,
            'label'       => '',
            'required'    => false,
            'placeholder' => '',
            'options'     => [],   // for select / radio / checkbox
        ];
    }

    public function removeField(string $id): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        $this->fields = array_values(
            array_filter($this->fields, fn($f) => $f['id'] !== $id)
        );
    }

    /**
     * Called by Sortable.js after drag-end; receives the new ordered UUIDs.
     */
    public function reorderFields(array $orderedIds): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        $indexed      = collect($this->fields)->keyBy('id');
        $this->fields = array_values(
            collect($orderedIds)
                ->filter(fn($id) => $indexed->has($id))
                ->map(fn($id) => $indexed[$id])
                ->toArray()
        );
    }

    public function addOption(string $fieldId): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        foreach ($this->fields as &$field) {
            if ($field['id'] === $fieldId) {
                $field['options'][] = '';
                break;
            }
        }
    }

    public function removeOption(string $fieldId, int $optionIndex): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        foreach ($this->fields as &$field) {
            if ($field['id'] === $fieldId) {
                array_splice($field['options'], $optionIndex, 1);
                break;
            }
        }
    }

    // ── Persistence ────────────────────────────────────────────────────────

    public function saveSchema(): void
    {
        if ($this->survey->is_locked) {
            $this->addError('locked', 'This survey is locked and cannot be modified.');
            return;
        }

        $this->validate(['title' => 'required|string|max:255']);

        $this->survey->update([
            'title'       => $this->title,
            'description' => $this->description ?: null,
            'schema'      => ['fields' => $this->fields],
        ]);

        session()->flash('status', 'Draft saved.');
    }

    public function publishSurvey(): void
    {
        if ($this->survey->is_locked) {
            return;
        }

        if (empty($this->fields)) {
            $this->addError('publish', 'Add at least one field before publishing.');
            return;
        }

        $this->survey->update([
            'title'       => $this->title,
            'description' => $this->description ?: null,
            'schema'      => ['fields' => $this->fields],
        ]);

        $this->survey->publishAndLock();
        $this->survey->refresh();

        session()->flash('status', 'Survey published! Share the public link.');
    }

    public function duplicateSurvey(): void
    {
        $newSurvey = $this->survey->duplicate();

        $this->redirect(route('admin.surveys.edit', $newSurvey), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.form-builder');
    }
}
