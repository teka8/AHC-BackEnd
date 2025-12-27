<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\Response;

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
            $pdf = Browsershot::html($documentHtml)
                ->format('A4')
                ->margins(12, 12, 12, 12)
                ->showBackground()
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="AHC_User_Guide.pdf"',
            ]);
        } catch (\Throwable) {
            return response()->download($path, 'AHC_User_Guide.md', [
                'Content-Type' => 'text/markdown; charset=UTF-8',
            ]);
        }
    }
}
