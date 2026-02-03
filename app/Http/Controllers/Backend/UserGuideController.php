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
        try {
            $path = base_path('AHC_User_Guide.md');

            abort_unless(is_file($path), 404);

            $markdown = file_get_contents($path);
            
            // For now, return the markdown file with proper headers
            // This avoids the timeout issues with PDF generation
            return response($markdown, 200, [
                'Content-Type' => 'text/markdown; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="AHC_User_Guide.md"',
            ]);
            
        } catch (\Throwable $e) {
            \Log::error('User guide download failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'error' => 'Download failed. Please contact support.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    private function simplifyHtmlForPdf(string $html): string
    {
        // Remove complex CSS and elements that might cause issues with DomPDF
        $html = preg_replace('/<style\b[^<]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<script\b[^<]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/<svg\b[^<]*>(.*?)<\/svg>/is', '', $html);
        
        // Simplify table structures
        $html = str_replace(['<thead>', '</thead>', '<tbody>', '</tbody>'], '', $html);
        
        return $html;
    }

    private function createMinimalPdfHtml(string $markdown): string
    {
        // Convert markdown to very simple HTML for PDF
        $lines = explode("\n", $markdown);
        $html = '<!DOCTYPE html><html><head><title>AHC User Guide</title><style>body{font-family:Arial,sans-serif;margin:20px;line-height:1.6}h1,h2,h3{margin-top:20px;margin-bottom:10px}p{margin-bottom:10px}ul,ol{margin-bottom:10px}li{margin:5px 0}code{background:#f4f4f4;padding:2px 4px;border-radius:3px}pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow:auto}</style></head><body>';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (str_starts_with($line, '# ')) {
                $html .= '<h1>' . htmlspecialchars(substr($line, 2)) . '</h1>';
            } elseif (str_starts_with($line, '## ')) {
                $html .= '<h2>' . htmlspecialchars(substr($line, 3)) . '</h2>';
            } elseif (str_starts_with($line, '### ')) {
                $html .= '<h3>' . htmlspecialchars(substr($line, 4)) . '</h3>';
            } elseif (str_starts_with($line, '- ') || str_starts_with($line, '* ')) {
                $html .= '<li>' . htmlspecialchars(substr($line, 2)) . '</li>';
            } elseif (str_starts_with($line, '1. ') || preg_match('/^\d+\. /', $line)) {
                $html .= '<li>' . htmlspecialchars(preg_replace('/^\d+\.\s/', '', $line)) . '</li>';
            } else {
                $html .= '<p>' . htmlspecialchars($line) . '</p>';
            }
        }
        
        $html .= '</body></html>';
        return $html;
    }
}
