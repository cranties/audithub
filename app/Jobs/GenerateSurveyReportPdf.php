<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Services\PdfReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSurveyReportPdf implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly Submission $submission)
    {
        //
    }

    /**
     * Delegate PDF generation to PdfReportService so the logic is DRY.
     */
    public function handle(PdfReportService $service): void
    {
        $service->generate($this->submission);
    }
}

