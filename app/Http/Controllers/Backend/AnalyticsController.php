<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly GoogleAnalyticsService $analyticsService
    ) {
    }

    /**
     * Display the analytics dashboard
     */
    public function index(): Renderable
    {
        $this->authorize('viewAny', 'analytics');

        // Check if GA is configured
        if (!config('settings.frontend_ga_enabled') || !$this->analyticsService->isConfigured()) {
            return view('backend.pages.analytics.not-configured')
                ->with([
                    'breadcrumbs' => [
                        'title' => __('AHC Google Analytics'),
                    ],
                ]);
        }

        return view('backend.pages.analytics.index')
            ->with([
                'breadcrumbs' => [
                    'title' => __('AHC Google Analytics'),
                ],
            ]);
    }

    /**
     * Get real-time active users
     */
    public function getRealTime(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getRealTimeUsers();

        return response()->json($data);
    }

    /**
     * Get overview statistics
     */
    public function getOverview(Request $request): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $days = $request->integer('days', 30);

        // Limit to reasonable values
        $days = min(max($days, 1), 365);

        $data = $this->analyticsService->getOverviewData($days);

        return response()->json($data);
    }

    /**
     * Get users trend data for chart
     */
    public function getUsersTrend(Request $request): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $days = $request->integer('days', 30);
        $days = min(max($days, 1), 365);

        $data = $this->analyticsService->getUsersTrend($days);

        return response()->json($data);
    }

    /**
     * Get top pages
     */
    public function getTopPages(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getTopPages(10);

        return response()->json($data);
    }

    /**
     * Get top events
     */
    public function getTopEvents(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getTopEvents(10);

        return response()->json($data);
    }

    /**
     * Get traffic sources
     */
    public function getTrafficSources(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getTrafficSources();

        return response()->json($data);
    }

    /**
     * Get geography data (countries)
     */
    public function getGeography(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getTopCountries(10);

        return response()->json($data);
    }

    /**
     * Get device categories
     */
    public function getDevices(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getDeviceCategories();

        return response()->json($data);
    }

    /**
     * Get browsers
     */
    public function getBrowsers(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getBrowsers(10);

        return response()->json($data);
    }

    /**
     * Get operating systems
     */
    public function getOperatingSystems(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getOperatingSystems(10);

        return response()->json($data);
    }

    /**
     * Get landing pages
     */
    public function getLandingPages(): JsonResponse
    {
        $this->authorize('viewAny', 'analytics');

        $data = $this->analyticsService->getLandingPages(10);

        return response()->json($data);
    }
}
