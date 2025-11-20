<?php

namespace App\Services;

use App\Models\ScholarshipApplication;
use Spatie\Browsershot\Browsershot;

class ScholarshipApplicationPdfService
{
    public function generatePdf(ScholarshipApplication $application): string
    {
        $html = view('backend.pages.scholarship-applications.pdf', [
            'application' => $application,
        ])->render();

        // Use Browsershot to generate PDF
        // We use noSandbox() to ensure it runs in various environments
        return Browsershot::html($html)
            ->noSandbox()
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->pdf();
    }
}
