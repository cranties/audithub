<?php

namespace App\Services;

use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfReportService
{
    /**
     * Generate the PDF report for a submission, persist it to private storage
     * and update the submission's pdf_path field.
     *
     * Safe to call both from a queued Job and synchronously during an HTTP request.
     */
    public function generate(Submission $submission): string
    {
        $submission->loadMissing(['survey', 'files']);

        $survey = $submission->survey;
        $fields = $survey->schema['fields'] ?? [];

        // Build label-keyed answer rows for the template.
        $rows = [];
        foreach ($fields as $field) {
            $answer = $submission->answers[$field['id']] ?? null;

            if ($field['type'] === 'checkbox' && is_array($answer)) {
                $answer = implode(', ', $answer);
            }

            $rows[] = [
                'label'  => $field['label'] ?: $field['id'],
                'type'   => $field['type'],
                'answer' => $answer,
            ];
        }

        $html = view('pdf.survey-report', [
            'survey'     => $survey,
            'submission' => $submission,
            'rows'       => $rows,
        ])->render();

        // Disable remote resource loading so DomPDF never blocks on external URLs.
        $pdf = Pdf::setOptions([
            'isRemoteEnabled'     => false,
            'isHtml5ParserEnabled' => true,
            'defaultFont'         => 'DejaVu Sans',
        ])->loadHtml($html)->setPaper('a4', 'portrait');

        // render() first so any DomPDF exception surfaces here rather than
        // being swallowed inside output().
        $pdf->render();

        // Capture the binary string before writing — do NOT inline output()
        // inside put() so we can verify it is non-empty.
        $content = $pdf->output();

        $path = 'reports/' . $submission->uuid . '.pdf';

        Storage::disk('private')->put($path, $content);

        $submission->update(['pdf_path' => $path]);

        return $path;
    }
}
