<?php

declare(strict_types=1);

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private ?BetaAnalyticsDataClient $client = null;
    private ?string $propertyId = null;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize the Google Analytics client
     */
    private function initialize(): void
    {
        try {
            $serviceAccountPath = config('settings.frontend_ga_service_account_path');
            $this->propertyId = config('settings.frontend_ga_property_id');

            if (!$serviceAccountPath || !$this->propertyId) {
                return;
            }

            $credentialsPath = storage_path('app/' . $serviceAccountPath);

            if (!file_exists($credentialsPath)) {
                Log::error('Google Analytics service account file not found', [
                    'path' => $credentialsPath
                ]);
                return;
            }

            $this->client = new BetaAnalyticsDataClient([
                'credentials' => $credentialsPath,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Analytics client', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if the service is properly configured
     */
    public function isConfigured(): bool
    {
        return $this->client !== null && $this->propertyId !== null;
    }

    /**
     * Get real-time active users
     */
    public function getRealTimeUsers(): array
    {
        if (!$this->isConfigured()) {
            return ['active_users' => 0];
        }

        try {
            return Cache::remember('ga_realtime', 60, function () {
                $request = (new RunRealtimeReportRequest())
                    ->setProperty($this->propertyId)
                    ->setMetrics([new Metric(['name' => 'activeUsers'])]);

                $response = $this->client->runRealtimeReport($request);
                $activeUsers = $response->getRows()[0]->getMetricValues()[0]->getValue() ?? 0;

                return ['active_users' => (int) $activeUsers];
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch real-time users', ['error' => $e->getMessage()]);
            return ['active_users' => 0];
        }
    }

    /**
     * Get overview data for dashboard
     */
    public function getOverviewData(int $days = 30): array
    {
        if (!$this->isConfigured()) {
            return $this->getEmptyOverviewData();
        }

        try {
            $cacheKey = "ga_overview_{$days}days";
            $cacheTtl = $days <= 7 ? 1800 : 3600; // 30 min for recent, 1 hour for older

            return Cache::remember($cacheKey, $cacheTtl, function () use ($days) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => "{$days}daysAgo",
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'newUsers']),
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'averageSessionDuration']),
                        new Metric(['name' => 'bounceRate']),
                        new Metric(['name' => 'engagementRate']),
                        new Metric(['name' => 'sessionsPerUser']),
                    ]);

                $response = $this->client->runReport($request);

                if ($response->getRows()->count() === 0) {
                    return $this->getEmptyOverviewData();
                }

                $row = $response->getRows()[0];
                $metrics = $row->getMetricValues();

                return [
                    'total_users' => (int) $metrics[0]->getValue(),
                    'new_users' => (int) $metrics[1]->getValue(),
                    'sessions' => (int) $metrics[2]->getValue(),
                    'page_views' => (int) $metrics[3]->getValue(),
                    'avg_session_duration' => (float) $metrics[4]->getValue(),
                    'bounce_rate' => (float) $metrics[5]->getValue() * 100,
                    'engagement_rate' => (float) $metrics[6]->getValue() * 100,
                    'sessions_per_user' => (float) $metrics[7]->getValue(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch overview data', ['error' => $e->getMessage()]);
            return $this->getEmptyOverviewData();
        }
    }

    /**
     * Get users trend over time
     */
    public function getUsersTrend(int $days = 30): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $cacheKey = "ga_users_trend_{$days}days";
            $cacheTtl = $days <= 7 ? 1800 : 3600;

            return Cache::remember($cacheKey, $cacheTtl, function () use ($days) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => "{$days}daysAgo",
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'date'])])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                    ])
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'dimension' => new \Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy([
                                'dimension_name' => 'date',
                            ]),
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $date = $row->getDimensionValues()[0]->getValue();
                    $users = (int) $row->getMetricValues()[0]->getValue();
                    $sessions = (int) $row->getMetricValues()[1]->getValue();
                    $pageViews = (int) $row->getMetricValues()[2]->getValue();

                    // Format date as YYYY-MM-DD
                    $formattedDate = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);

                    $data[] = [
                        'date' => $formattedDate,
                        'users' => $users,
                        'sessions' => $sessions,
                        'page_views' => $pageViews,
                    ];
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch users trend', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get top pages
     */
    public function getTopPages(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_top_pages', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([
                        new Dimension(['name' => 'pagePath']),
                        new Dimension(['name' => 'pageTitle']),
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'averageSessionDuration']),
                    ])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'screenPageViews',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'path' => $row->getDimensionValues()[0]->getValue(),
                        'title' => $row->getDimensionValues()[1]->getValue(),
                        'views' => (int) $row->getMetricValues()[0]->getValue(),
                        'users' => (int) $row->getMetricValues()[1]->getValue(),
                        'avg_time' => (float) $row->getMetricValues()[2]->getValue(),
                    ];
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch top pages', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get top events
     */
    public function getTopEvents(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_top_events', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'eventName'])])
                    ->setMetrics([new Metric(['name' => 'eventCount'])])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'eventCount',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $eventName = $row->getDimensionValues()[0]->getValue();
                    
                    // Skip default GA4 events, show only custom events
                    if (!in_array($eventName, ['session_start', 'first_visit', 'page_view', 'user_engagement'])) {
                        $data[] = [
                            'event_name' => $eventName,
                            'count' => (int) $row->getMetricValues()[0]->getValue(),
                        ];
                    }
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch top events', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get traffic sources
     */
    public function getTrafficSources(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_traffic_sources', 1800, function () {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([
                        new Dimension(['name' => 'sessionSource']),
                        new Dimension(['name' => 'sessionMedium']),
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                    ])
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'activeUsers',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $total = 0;
                $sources = [];

                foreach ($response->getRows() as $row) {
                    $source = $row->getDimensionValues()[0]->getValue();
                    $medium = $row->getDimensionValues()[1]->getValue();
                    $users = (int) $row->getMetricValues()[0]->getValue();
                    $sessions = (int) $row->getMetricValues()[1]->getValue();

                    $total += $users;

                    // Combine source and medium for better clarity
                    $sourceName = $source . ' / ' . $medium;

                    $sources[] = [
                        'source' => $sourceName,
                        'users' => $users,
                        'sessions' => $sessions,
                    ];
                }

                // Calculate percentages
                foreach ($sources as &$source) {
                    $source['percentage'] = $total > 0 ? round(($source['users'] / $total) * 100, 1) : 0;
                }

                return array_slice($sources, 0, 10); // Top 10 sources
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch traffic sources', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get top countries
     */
    public function getTopCountries(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_top_countries', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'country'])])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                    ])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'activeUsers',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $total = 0;
                $countries = [];

                foreach ($response->getRows() as $row) {
                    $users = (int) $row->getMetricValues()[0]->getValue();
                    $total += $users;

                    $countries[] = [
                        'country' => $row->getDimensionValues()[0]->getValue(),
                        'users' => $users,
                        'sessions' => (int) $row->getMetricValues()[1]->getValue(),
                    ];
                }

                // Calculate percentages
                foreach ($countries as &$country) {
                    $country['percentage'] = $total > 0 ? round(($country['users'] / $total) * 100, 1) : 0;
                }

                return $countries;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch top countries', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get device categories
     */
    public function getDeviceCategories(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_devices', 1800, function () {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'deviceCategory'])])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                    ])
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'activeUsers',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $total = 0;
                $devices = [];

                foreach ($response->getRows() as $row) {
                    $users = (int) $row->getMetricValues()[0]->getValue();
                    $total += $users;

                    $devices[] = [
                        'device' => ucfirst($row->getDimensionValues()[0]->getValue()),
                        'users' => $users,
                        'sessions' => (int) $row->getMetricValues()[1]->getValue(),
                    ];
                }

                // Calculate percentages
                foreach ($devices as &$device) {
                    $device['percentage'] = $total > 0 ? round(($device['users'] / $total) * 100, 1) : 0;
                }

                return $devices;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch device categories', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get browser statistics
     */
    public function getBrowsers(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_browsers', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'browser'])])
                    ->setMetrics([new Metric(['name' => 'activeUsers'])])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'activeUsers',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'browser' => $row->getDimensionValues()[0]->getValue(),
                        'users' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch browsers', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get operating systems
     */
    public function getOperatingSystems(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_operating_systems', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'operatingSystem'])])
                    ->setMetrics([new Metric(['name' => 'activeUsers'])])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'activeUsers',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'os' => $row->getDimensionValues()[0]->getValue(),
                        'users' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch operating systems', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get landing pages
     */
    public function getLandingPages(int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            return Cache::remember('ga_landing_pages', 1800, function () use ($limit) {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ]),
                    ])
                    ->setDimensions([new Dimension(['name' => 'landingPage'])])
                    ->setMetrics([
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'bounceRate']),
                    ])
                    ->setLimit($limit)
                    ->setOrderBys([
                        new \Google\Analytics\Data\V1beta\OrderBy([
                            'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                                'metric_name' => 'sessions',
                            ]),
                            'desc' => true,
                        ]),
                    ]);

                $response = $this->client->runReport($request);

                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'page' => $row->getDimensionValues()[0]->getValue(),
                        'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                        'bounce_rate' => (float) $row->getMetricValues()[1]->getValue() * 100,
                    ];
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch landing pages', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get empty overview data structure
     */
    private function getEmptyOverviewData(): array
    {
        return [
            'total_users' => 0,
            'new_users' => 0,
            'sessions' => 0,
            'page_views' => 0,
            'avg_session_duration' => 0,
            'bounce_rate' => 0,
            'engagement_rate' => 0,
            'sessions_per_user' => 0,
        ];
    }
}
