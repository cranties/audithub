<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Survey;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function index(Survey $survey): View
    {
        $this->authorize('view', $survey);

        $submissions = $survey->submissions()
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.submissions.index', compact('survey', 'submissions'));
    }

    public function show(Survey $survey, Submission $submission): View
    {
        $this->authorize('view', $survey);

        $submission->load('files');

        return view('admin.submissions.show', compact('survey', 'submission'));
    }

    /**
     * Serve the generated PDF report through a protected route.
     * The file is read from private storage — never exposed via a public URL.
     */
    public function downloadPdf(Survey $survey, Submission $submission): Response
    {
        $this->authorize('view', $survey);

        abort_unless($submission->pdf_path, 404, 'PDF not yet generated.');

        $disk = \Storage::disk('private');

        abort_unless($disk->exists($submission->pdf_path), 404, 'PDF file not found.');

        return response(
            $disk->get($submission->pdf_path),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="report-' . $submission->uuid . '.pdf"',
            ]
        );
    }

    /**
     * Serve an uploaded attachment through a protected route.
     */
    public function downloadFile(SubmissionFile $file): Response
    {
        $this->authorize('view', $file->submission->survey);

        $disk = \Storage::disk('private');

        abort_unless($disk->exists($file->path), 404, 'File not found.');

        return response(
            $disk->get($file->path),
            200,
            [
                'Content-Type'        => $file->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
            ]
        );
    }
}
