<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Setting\UpdateSettingsRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
    public function __construct(private readonly SettingService $settingService)
    {
    }

    /**
     * Settings list.
     *
     * @tags Settings
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Setting::class);
        $settings = $this->settingService->getAllSettings(
            $request->input('search'),
            $request->integer('autoload')
        );

        return $this->resourceResponse(
            SettingResource::collection($settings),
            'Settings retrieved successfully'
        );
    }

    /**
     * Show Setting.
     *
     * @tags Settings
     */
    public function show(string $option_name): JsonResponse
    {
        $setting = $this->settingService->getSettingByKey($option_name);

        if (! $setting) {
            return $this->errorResponse('Setting not found', 404);
        }

        $this->authorize('view', $setting);

        return $this->resourceResponse(
            new SettingResource($setting),
            'Setting retrieved successfully'
        );
    }

    /**
     * Update Settings.
     *
     * @tags Settings
     */
    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $this->authorize('update', Setting::class);

        $settings = $request->input('settings', []);
        $updatedSettings = [];

        foreach ($settings as $key => $value) {
            $setting = $this->settingService->updateOrCreateSetting((string) $key, $value);
            $updatedSettings[] = $setting;
        }

        $this->logAction('Settings Updated', null, ['updated_keys' => array_keys($settings)]);

        return $this->resourceResponse(
            SettingResource::collection(collect($updatedSettings)),
            'Settings updated successfully'
        );
    }
    /**
     * Get public company information.
     *
     * @tags Settings
     */
    public function publicCompanyInfo(): JsonResponse
    {
        $keys = [
            'company_name',
            'company_email',
            'company_phone',
            'company_address',
            'social_facebook',
            'social_twitter',
            'social_linkedin',
            'social_instagram',
            'social_youtube',
        ];

        $settings = Setting::whereIn('option_name', $keys)
            ->pluck('option_value', 'option_name');

        return $this->successResponse(
            $settings,
            'Company info retrieved successfully'
        );
    }

    /**
     * Get Frontend Google Analytics configuration.
     * Public endpoint for frontend to initialize GA4 tracking.
     *
     * @tags Settings
     */
    public function frontendGoogleAnalyticsConfig(): JsonResponse
    {
        $keys = [
            'frontend_ga_enabled',
            'frontend_ga_measurement_id',
            'frontend_ga_anonymize_ip',
            'frontend_ga_cookie_consent_required',
        ];

        $settings = Setting::whereIn('option_name', $keys)
            ->pluck('option_value', 'option_name');

        // Convert string boolean values to actual booleans
        $config = [
            'enabled' => (bool) ($settings['frontend_ga_enabled'] ?? false),
            'measurement_id' => $settings['frontend_ga_measurement_id'] ?? '',
            'anonymize_ip' => (bool) ($settings['frontend_ga_anonymize_ip'] ?? true),
            'cookie_consent_required' => (bool) ($settings['frontend_ga_cookie_consent_required'] ?? true),
        ];

        return $this->successResponse(
            $config,
            'Google Analytics configuration retrieved successfully'
        );
    }
}
