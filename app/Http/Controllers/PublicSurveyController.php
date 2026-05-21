<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Jobs\GenerateSurveyReportPdf;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicSurveyController extends Controller
{
    /**
     * Render the public survey form.
     * Route model binding resolves by `public_token`.
     */
    public function show(Survey $survey): View
    {
        // A survey without a public_token has never been published.
        abort_if(is_null($survey->public_token), 404);

        return view('survey.show', compact('survey'));
    }

    /**
     * Show the thank-you page after a successful submission.
     */
    public function thankYou(Survey $survey): View
    {
        abort_if(is_null($survey->public_token), 404);

        return view('survey.thank-you', compact('survey'));
    }

    /**
     * Store a new submission.
     * Rate-limited upstream via throttle:5,1.
     */
    public function store(Survey $survey, StoreSubmissionRequest $request): RedirectResponse
    {
        abort_if(is_null($survey->public_token), 404);

        $fields  = $survey->schema['fields'] ?? [];
        $answers = [];
        $submissionUuid = (string) \Illuminate\Support\Str::uuid();

        // ── Process each field ─────────────────────────────────────────────
        foreach ($fields as $field) {
            $key   = $field['id'];
            $type  = $field['type'];
            $value = $request->validated($key);

            if ($type === 'file' && $request->hasFile($key)) {
                $uploadedFile = $request->file($key);
                $ext          = $uploadedFile->getClientOriginalExtension();
                $path         = "uploads/{$submissionUuid}/{$key}.{$ext}";

                Storage::disk('private')->put(
                    $path,
                    file_get_contents($uploadedFile->getRealPath())
                );

                $answers[$key] = $path; // store relative private path
            } else {
                $answers[$key] = $value;
            }
        }

        // ── Persist submission ─────────────────────────────────────────────
        $submission = Submission::create([
            'uuid'          => $submissionUuid,
            'survey_id'     => $survey->id,
            'answers'       => $answers,
            'submitter_ip'  => $request->ip(),
            'submitted_at'  => now(),
        ]);

        // ── Record file metadata ───────────────────────────────────────────
        foreach ($fields as $field) {
            $key = $field['id'];
            if ($field['type'] === 'file' && isset($answers[$key])) {
                $uploadedFile = $request->file($key);
                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'field_key'     => $key,
                    'path'          => $answers[$key],
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type'     => $uploadedFile->getMimeType(),
                ]);
            }
        }

        // ── Dispatch async PDF generation ──────────────────────────────────
        GenerateSurveyReportPdf::dispatch($submission);

        return redirect()->route('survey.thankyou', $survey->public_token);
    }
}
