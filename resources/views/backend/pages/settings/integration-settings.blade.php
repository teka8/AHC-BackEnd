{!! Hook::applyFilters(SettingFilterHook::SETTINGS_INTEGRATIONS_TAB_BEFORE_SECTION_START, '') !!}
<div class="rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 sm:py-5">
        <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
            {{ __('Integration Setting') }}
        </h3>
    </div>
    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
        <div class="relative">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Google Analytics') }}
            </label>
            <textarea name="google_analytics_script" rows="6" placeholder="{{ __('Paste your Google Analytics script here') }}"
                @if (config('app.demo_mode', false)) disabled @endif
                class="form-control h-20"
                data-tooltip-target="tooltip-google-analytics">{{ config('settings.google_analytics_script') ?? '' }}</textarea>

            @if (config('app.demo_mode', false))
            <div id="tooltip-google-analytics" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-md shadow-xs opacity-0 tooltip dark:bg-gray-700">
                {{ __('Editing this script is disabled in demo mode.') }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
            @endif

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                {{ __('Learn more about Google Analytics and how to set it up:') }}
                <a href="https://analytics.google.com/" target="_blank" class="text-primary hover:underline">
                    {{ __('Google Analytics') }}
                </a>
            </p>
        </div>
    </div>

    {!! Hook::applyFilters(SettingFilterHook::SETTINGS_INTEGRATIONS_TAB_BEFORE_SECTION_END, '') !!}
</div>
{!! Hook::applyFilters(SettingFilterHook::SETTINGS_INTEGRATIONS_TAB_AFTER_SECTION_END, '') !!}

{{-- Frontend Google Analytics Section --}}
<div class="mt-6 rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 sm:py-5 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
            {{ __('Frontend Google Analytics (GA4)') }}
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Configure Google Analytics 4 tracking for your AHC frontend website and view analytics in admin dashboard.') }}
        </p>
    </div>
    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
        
        {{-- Enable Toggle --}}
        <div class="flex items-center justify-between p-4 rounded-md bg-gray-50 dark:bg-gray-900/20">
            <div class="flex-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Enable Frontend Tracking') }}
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Enable or disable Google Analytics tracking on your frontend website') }}
                </p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="frontend_ga_enabled" value="1" class="sr-only peer"
                    {{ config('settings.frontend_ga_enabled') ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </div>

        {{-- GA4 Measurement ID --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('GA4 Measurement ID') }}
                <span class="text-red-500">*</span>
            </label>
            <input type="text" name="frontend_ga_measurement_id" 
                placeholder="G-XXXXXXXXXX"
                value="{{ config('settings.frontend_ga_measurement_id') ?? '' }}"
                class="form-control">
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Find this in Google Analytics: Admin → Data Streams → Select your stream → Measurement ID') }}
            </p>
        </div>

        {{-- GA4 Property ID --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('GA4 Property ID') }}
                <span class="text-red-500">*</span>
            </label>
            <input type="text" name="frontend_ga_property_id" 
                placeholder="123456789 or properties/123456789"
                value="{{ config('settings.frontend_ga_property_id') ?? '' }}"
                class="form-control">
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Enter the numeric Property ID from Google Analytics. You can enter it with or without "properties/" prefix.') }}
            </p>
        </div>

        {{-- Service Account File Upload --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Service Account JSON Key') }}
                <span class="text-red-500">*</span>
            </label>
            <input type="file" name="frontend_ga_service_account" 
                accept=".json"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
            
            @if(config('settings.frontend_ga_service_account_path'))
                <div class="mt-2 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Service account configured') }}: {{ basename(config('settings.frontend_ga_service_account_path')) }}
                </div>
            @endif

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Upload the JSON key file for your Google Cloud service account') }}
            </p>
        </div>

        {{-- Privacy Options --}}
        <div class="space-y-4 p-4 rounded-md bg-gray-50 dark:bg-gray-900/20">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Privacy Settings') }}
            </h4>

            {{-- Anonymize IP --}}
            <div class="flex items-center">
                <input type="checkbox" name="frontend_ga_anonymize_ip" value="1" 
                    id="frontend_ga_anonymize_ip"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    {{ config('settings.frontend_ga_anonymize_ip') ? 'checked' : '' }}>
                <label for="frontend_ga_anonymize_ip" class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Anonymize IP Addresses') }}
                </label>
            </div>

            {{-- Cookie Consent --}}
            <div class="flex items-center">
                <input type="checkbox" name="frontend_ga_cookie_consent_required" value="1" 
                    id="frontend_ga_cookie_consent_required"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    {{ config('settings.frontend_ga_cookie_consent_required') ? 'checked' : '' }}>
                <label for="frontend_ga_cookie_consent_required" class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Require Cookie Consent (GDPR Compliant)') }}
                </label>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Privacy-friendly options to comply with GDPR and other privacy regulations') }}
            </p>
        </div>

        {{-- Setup Guide --}}
        <div class="p-4 rounded-md bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">
                📖 {{ __('Setup Guide') }}
            </h4>
            <ol class="text-sm text-blue-800 dark:text-blue-400 space-y-1 list-decimal list-inside">
                <li>{{ __('Create a GA4 property in Google Analytics') }}</li>
                <li>{{ __('Get Measurement ID from Admin → Data Streams') }}</li>
                <li>{{ __('Create a service account in Google Cloud Console') }}</li>
                <li>{{ __('Enable "Google Analytics Data API" in your GCP project') }}</li>
                <li>{{ __('Download JSON key for the service account') }}</li>
                <li>{{ __('Grant service account "Viewer" access to your GA4 property') }}</li>
                <li>{{ __('Upload the JSON file above') }}</li>
                <li>{{ __('View analytics in: Admin Menu → AHC Google Analytics') }}</li>
            </ol>
            <div class="mt-3 flex gap-3">
                <a href="https://analytics.google.com/" target="_blank" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                    {{ __('Google Analytics') }} →
                </a>
                <a href="https://console.cloud.google.com/" target="_blank" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                    {{ __('Google Cloud Console') }} →
                </a>
            </div>
        </div>

    </div>
</div>

<div class="mt-6">
    @include('backend.pages.settings.ai-settings')
</div>