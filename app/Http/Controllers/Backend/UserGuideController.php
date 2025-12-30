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

        $markdown = file_get_contents($path) ?: '';
        $html = Str::markdown($markdown);

        $documentHtml = view('backend.pages.user-guide.pdf', compact('html'))->render();

        try {
            // First try Browsershot (Chrome-based) for better quality
            $chromePaths = [
                'C:\Program Files\Google\Chrome\Application\chrome.exe',
                'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
                'C:\Users\\' . get_current_user() . '\\AppData\\Local\\Google\\Chrome\\Application\\chrome.exe',
                'C:\Program Files\Chromium\Application\chromium.exe',
                'C:\Program Files (x86)\Chromium\Application\chromium.exe',
            ];
            
            $browsershot = Browsershot::html($documentHtml)
                ->format('A4')
                ->margins(12, 12, 12, 12)
                ->showBackground()
                ->timeout(60); // Increased timeout
            
            // Set Chrome path if found
            $chromeFound = false;
            foreach ($chromePaths as $chromePath) {
                if (file_exists($chromePath)) {
                    $browsershot->setChromePath($chromePath);
                    $chromeFound = true;
                    break;
                }
            }
            
            if (!$chromeFound) {
                throw new \Exception('Chrome not found in any expected location');
            }
            
            $pdf = $browsershot->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="AHC_User_Guide.pdf"',
            ]);
        } catch (\Exception $e) {
            // Log the Browsershot error
            \Log::warning('Browsershot PDF generation failed, trying DomPDF: ' . $e->getMessage());
            
            try {
                // Increase memory limit for large documents
                ini_set('memory_limit', '512M');
                set_time_limit(300); // 5 minutes
                
                // Fallback to DomPDF
                $options = new Options();
                $options->set('defaultFont', 'Arial');
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('fontDir', public_path('fonts'));
                $options->set('fontCache', public_path('fonts'));
                
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($documentHtml);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                $pdfOutput = $dompdf->output();
                
                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="AHC_User_Guide.pdf"',
                ]);
            } catch (\Exception $dompdfException) {
                // Log both errors
                \Log::error('Both PDF generation methods failed: ', [
                    'browsershot_error' => $e->getMessage(),
                    'dompdf_error' => $dompdfException->getMessage(),
                    'file' => $path,
                    'markdown_size' => strlen($markdown),
                    'html_size' => strlen($documentHtml)
                ]);
                
                // Final fallback - return HTML as PDF content-type (better than MD)
                return response($documentHtml, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="AHC_User_Guide.html"',
                ]);
            }
        }
    }
}
