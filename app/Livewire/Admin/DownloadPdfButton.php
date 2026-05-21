<?php

namespace App\Livewire\Admin;

use App\Models\Submission;
use App\Services\PdfReportService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadPdfButton extends Component
{
    public Submission $submission;

    /**
     * Generate the PDF on-demand if needed, then serve it as a file download.
     *
     * We bypass Flysystem metadata entirely by resolving the relative path to
     * an absolute filesystem path and calling response()->download().
     *
     * Storage::disk()->download() internally calls Flysystem fileSize() to set
     * the Content-Length header; that call fails with UnableToRetrieveMetadata
     * when the file has restrictive permissions or the stat cache is stale.
     * response()->download($absolutePath) uses Symfony's BinaryFileResponse
     * which reads the file directly via PHP without any Flysystem metadata calls.
     */
    public function download(PdfReportService $service): BinaryFileResponse
    {
        $disk = Storage::disk('private');

        // Resolve stored path to an absolute OS path for reliable checks.
        $storedPath = $this->submission->pdf_path;
        $absPath    = $storedPath ? $disk->path($storedPath) : null;

        // Regenerate when: path is missing, file does not exist, or file is
        // empty (a previous failed write can leave a 0-byte file on disk).
        $needsRegen = ! $absPath
            || ! is_file($absPath)
            || filesize($absPath) === 0;

        if ($needsRegen) {
            $newPath = $service->generate($this->submission);
            $absPath = $disk->path($newPath);
        }

        $filename = 'report-' . $this->submission->uuid . '.pdf';

        // response()->download() creates a BinaryFileResponse served directly
        // by PHP/Symfony — no Flysystem metadata calls, no output buffering.
        return response()->download($absPath, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        return view('livewire.admin.download-pdf-button');
    }
}
