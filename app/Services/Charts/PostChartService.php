<?php

declare(strict_types=1);

namespace App\Services\Charts;

use App\Enums\PostStatus;
use App\Models\Post;
use Carbon\Carbon;

class PostChartService
{
    /**
     * Get post/news statistics for the chart
     */
    public function getPostActivityData(string $period = 'last_6_months'): array
    {
        // Determine date range
        $dateRange = $this->getDateRangeFromPeriod($period);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $interval = $dateRange['interval'];

        // Initialize arrays
        $labels = [];
        $createdData = [];
        $reviewedData = [];
        $approvedData = [];
        $publishedData = [];
        $archivedData = [];

        // Loop through each interval
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if ($interval === 'month') {
                $labels[] = $currentDate->format('M Y');
                $nextDate = $currentDate->copy()->addMonth();

                $rangeStart = $currentDate->startOfMonth()->toDateTimeString();
                $rangeEnd = $currentDate->endOfMonth()->toDateTimeString();
            } elseif ($interval === 'week') {
                $weekEnd = $currentDate->copy()->addDays(6);
                $labels[] = $currentDate->format('d M').' - '.$weekEnd->format('d M');
                $nextDate = $currentDate->copy()->addWeek();

                $rangeStart = $currentDate->startOfDay()->toDateTimeString();
                $rangeEnd = $weekEnd->endOfDay()->toDateTimeString();
            } else {
                $labels[] = $currentDate->format('d M');
                $nextDate = $currentDate->copy()->addDay();

                $rangeStart = $currentDate->startOfDay()->toDateTimeString();
                $rangeEnd = $currentDate->endOfDay()->toDateTimeString();
            }

            // Count posts by status
            $createdCount = Post::where('status', PostStatus::CREATED->value)
                ->where('post_type', 'News')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->count();

           $reviewedCount = Post::where('post_type', 'News')
    ->where(function($query) {
        $query->where('status', PostStatus::REVIEWED->value)
              ->orWhere('status', PostStatus::EDITED->value);
    })
    ->whereBetween('updated_at', [$rangeStart, $rangeEnd])
    ->count();
            $approvedCount = Post::where('status', PostStatus::APPROVED->value)
                ->where('post_type', 'News')
                ->whereBetween('updated_at', [$rangeStart, $rangeEnd])
                ->count();

            $publishedCount = Post::where('status', PostStatus::PUBLISHED->value)
                ->where('post_type', 'News')
                ->whereBetween('updated_at', [$rangeStart, $rangeEnd])
                ->count();

            $archivedCount = Post::where('status', PostStatus::ARCHIVED->value)
                ->where('post_type', 'News')
                ->whereBetween('updated_at', [$rangeStart, $rangeEnd])
                ->count();

            // Push data
            $createdData[] = $createdCount;
            $reviewedData[] = $reviewedCount;
            $approvedData[] = $approvedCount;
            $publishedData[] = $publishedCount;
            $archivedData[] = $archivedCount;

            $currentDate = $nextDate;
        }

        return [
            'labels' => $labels,
            'created' => $createdData,
            'reviewed' => $reviewedData,
            'approved' => $approvedData,
            'published' => $publishedData,
            'archived' => $archivedData,
        ];
    }

    /**
     * Get date range based on selected period
     */
    private function getDateRangeFromPeriod(string $period): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(6)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'interval' => 'day',
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'interval' => 'day',
                ];
            case 'last_3_months':
                return [
                    'start' => $now->copy()->subMonths(2)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'interval' => 'month',
                ];
            case 'last_12_months':
                return [
                    'start' => $now->copy()->subMonths(11)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'interval' => 'month',
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'interval' => 'month',
                ];
            case 'last_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear(),
                    'end' => $now->copy()->subYear()->endOfYear(),
                    'interval' => 'month',
                ];
            case 'last_6_months':
            default:
                return [
                    'start' => $now->copy()->subMonths(5)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'interval' => 'month',
                ];
        }
    }
}
