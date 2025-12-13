<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Enums\Hooks\SettingFilterHook;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CacheService;
use App\Services\EnvWriter;
use App\Services\ImageService;
use App\Services\RecaptchaService;
use App\Services\SettingService;
use App\Support\Facades\Hook;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly EnvWriter $envWriter,
        private readonly CacheService $cacheService,
        private readonly ImageService $imageService,
        private readonly RecaptchaService $recaptchaService,
    ) {
    }

    public function index($tab = null): Renderable
    {
        $this->authorize('manage', Setting::class);

        $tab = $tab ?? request()->input('tab', 'general');

        return view('backend.pages.settings.index', compact('tab'))
            ->with([
                'breadcrumbs' => [
                    'title' => __('Settings'),
                ],
            ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manage', Setting::class);

        // Restrict specific fields in demo mode.
        if (config('app.demo_mode', false)) {
            $restrictedFields = Hook::applyFilters(SettingFilterHook::SETTINGS_RESTRICTED_FIELDS, [
                'app_name',
                'google_analytics_script',
                'recaptcha_site_key',
                'recaptcha_secret_key',
                'recaptcha_enabled_pages',
                'recaptcha_score_threshold',
                'admin_login_route',
                'disable_default_admin_redirect',
            ]);
            $fields = $request->except($restrictedFields);
        } else {
            $fields = $request->all();
        }

        // Validate admin login route if provided
        if ($request->has('admin_login_route')) {
            $request->validate([
                'admin_login_route' => 'required|regex:/^[a-zA-Z0-9\-\_\/]+$/|min:3|max:50',
            ], [
                'admin_login_route.regex' => 'The admin login route can only contain letters, numbers, hyphens, underscores and forward slashes.',
            ]);
        }

        $uploadPath = 'uploads/settings';

        // Handle checkbox fields that might not be present when unchecked
        $checkboxFields = [
            'disable_default_admin_redirect',
            'frontend_ga_enabled',
            'frontend_ga_anonymize_ip',
            'frontend_ga_cookie_consent_required',
        ];
        foreach ($checkboxFields as $checkboxField) {
            // Skip restricted fields in demo mode
            if (config('app.demo_mode', false) && in_array($checkboxField, $restrictedFields ?? [])) {
                continue;
            }

            if (! isset($fields[$checkboxField]) && $request->has('_token')) {
                // If the form was submitted but checkbox wasn't checked, set to 0
                $fields[$checkboxField] = '0';
            }
        }

        // Handle frontend GA property ID format normalization
        if (isset($fields['frontend_ga_property_id']) && ! empty($fields['frontend_ga_property_id'])) {
            $propertyId = trim($fields['frontend_ga_property_id']);

            // If property ID doesn't start with "properties/", add it
            if (! str_starts_with($propertyId, 'properties/')) {
                // Remove any existing "properties/" prefix variations
                $propertyId = preg_replace('/^properties\//i', '', $propertyId);
                $fields['frontend_ga_property_id'] = 'properties/' . $propertyId;
            }
        }

        // Handle frontend GA service account JSON file upload
        if ($request->hasFile('frontend_ga_service_account')) {
            $file = $request->file('frontend_ga_service_account');

            // Validate JSON file
            $request->validate([
                'frontend_ga_service_account' => 'required|file|mimes:json|max:10240', // Max 10MB
            ]);

            // Create directory if it doesn't exist
            $directory = storage_path('app/google');
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Store the file
            $filename = 'ahc-ga-service-account.json';
            $filePath = $file->storeAs('google', $filename);

            // Set proper permissions
            chmod(storage_path('app/' . $filePath), 0600);

            // Save the path in settings
            $fields['frontend_ga_service_account_path'] = $filePath;

            // Remove the file from fields to prevent it from being processed again
            unset($fields['frontend_ga_service_account']);
        }

        // Track if Google Analytics settings were changed
        $gaSettingsChanged = isset($fields['frontend_ga_property_id']) 
            || isset($fields['frontend_ga_measurement_id'])
            || isset($fields['frontend_ga_service_account_path'])
            || isset($fields['frontend_ga_enabled']);

        foreach ($fields as $fieldName => $fieldValue) {
            if ($request->hasFile($fieldName)) {
                $this->imageService->deleteImageFromPublic((string) config($fieldName));
                $fileUrl = $this->imageService->storeImageAndGetUrl($request, $fieldName, $uploadPath);
                $this->settingService->addSetting($fieldName, $fileUrl);
            } elseif ($fieldName === 'recaptcha_enabled_pages') {
                // Validate enabled pages against allowed list.
                $enabledPages = $request->input('recaptcha_enabled_pages', []);
                $validPages = array_keys($this->recaptchaService::getAvailablePages());
                $enabledPages = array_intersect($enabledPages, $validPages);
                $this->settingService->addSetting($fieldName, json_encode(array_values($enabledPages)));
            } else {
                $this->settingService->addSetting($fieldName, $fieldValue);
            }
        }

        // Clear Google Analytics cache if settings changed
        if ($gaSettingsChanged) {
            $this->clearGoogleAnalyticsCache();
            Log::info('Google Analytics settings updated, cache cleared');
        }

        $this->envWriter->batchWriteKeysToEnvFile($fields);

        $this->storeActionLog(ActionType::UPDATED, [
            'settings' => $fields,
        ]);

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    /**
     * Clear Google Analytics cache
     * Called automatically when GA settings are updated
     */
    private function clearGoogleAnalyticsCache(): void
    {
        // List of all GA cache keys
        $cacheKeys = [
            'ga_realtime',
            'ga_top_pages',
            'ga_top_events',
            'ga_traffic_sources',
            'ga_top_countries',
            'ga_devices',
            'ga_browsers',
            'ga_operating_systems',
            'ga_landing_pages',
        ];
        
        // Clear static cache keys
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Clear dynamic cache keys with days parameter
        foreach ([7, 30, 90] as $days) {
            Cache::forget("ga_overview_{$days}");
            Cache::forget("ga_users_trend_{$days}");
        }
    }
}
