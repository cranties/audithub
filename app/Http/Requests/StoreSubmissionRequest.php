<?php

namespace App\Http\Requests;

use App\Models\Survey;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Public survey forms are open to unauthenticated users.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Build validation rules dynamically from the survey's JSON schema.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Survey $survey */
        $survey = $this->route('survey');
        $fields = $survey->schema['fields'] ?? [];
        $rules  = [];

        foreach ($fields as $field) {
            $key      = $field['id'];
            $type     = $field['type'];
            $required = (bool) ($field['required'] ?? false);

            $fieldRules = [$required ? 'required' : 'nullable'];

            switch ($type) {
                case 'text':
                case 'textarea':
                case 'radio':
                case 'select':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';
                    break;

                case 'number':
                    $fieldRules[] = 'numeric';
                    break;

                case 'date':
                    $fieldRules[] = 'date';
                    break;

                case 'checkbox':
                    // checkbox can submit an array of values
                    $fieldRules = [$required ? 'required' : 'nullable', 'array'];
                    $rules["{$key}.*"] = ['string', 'max:255'];
                    break;

                case 'rating':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = 'min:1';
                    $fieldRules[] = 'max:5';
                    break;

                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimes:jpeg,jpg,png,pdf';
                    $fieldRules[] = 'max:5120'; // 5 MB
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }
}

