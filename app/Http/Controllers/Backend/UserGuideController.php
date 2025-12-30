<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;

class UserGuideController extends Controller
{
    public function index(): Renderable
    {
        $path = base_path('AHC_User_Guide.md');

        abort_unless(is_file($path), 404);

        $markdown = file_get_contents($path) ?: '';
        $html = Str::markdown($markdown);

        $breadcrumbs = [
            'title' => __('User Guide'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('User Guide'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.user-guide.index', compact('breadcrumbs', 'html'));
    }

    public function download(): Response
    {
        $path = base_path('AHC_User_Guide.md');

        abort_unless(is_file($path), 404);

        // Set higher limits for PDF generation
        ini_set('memory_limit', '768M');
        set_time_limit(600); // 10 minutes
        ignore_user_abort(true); // Don't abort if user disconnects

        $markdown = file_get_contents($path) ?: '';
        
        // Check if markdown is too large for processing
        if (strlen($markdown) > 1000000) { // 1MB limit
            return response()->json([
                'error' => 'User guide is too large to generate as PDF. Please contact support for a copy.',
            ], 413);
        }

        $html = Str::markdown($markdown);
        $documentHtml = view('backend.pages.user-guide.pdf', compact('html'))->render();

        try {
            // Try DomPDF first (more reliable on servers without Chrome)
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false); // Disable remote resources for security
            $options->set('isHtml5ParserEnabled', true);
            $options->set('fontDir', storage_path('fonts'));
            $options->set('fontCache', storage_path('fonts'));
            $options->set('tempDir', storage_path('app/temp'));
            $options->set('logOutputFile', storage_path('logs/dompdf.log'));
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($documentHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $pdfOutput = $dompdf->output();
            
            // Clear memory
            unset($dompdf, $options, $documentHtml, $html, $markdown);
            
            return response($pdfOutput, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="AHC_User_Guide.pdf"',
                'Content-Length' => strlen($pdfOutput),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $path,
                'markdown_size' => strlen($markdown ?? ''),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a more user-friendly error
            return response()->json([
                'error' => 'PDF generation failed. The user guide may be temporarily unavailable. Please try again later or contact support.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
