<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    
    {{-- Real-time Active Users Bar --}}
    <div class="mb-6 rounded-md border border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-800">
        <div class="px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-3 w-3 bg-green-500 rounded-full animate-pulse"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('Real-time Activity') }}
                </h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active Users:') }}</span>
                <div id="realtime-users" class="text-3xl font-bold text-green-600 dark:text-green-400">
                    --
                </div>
            </div>
        </div>
    </div>

    {{-- Time Range Selector --}}
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Time Range:') }}
            </label>
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button type="button" class="btn-period active px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-blue-400 dark:hover:bg-gray-700" data-days="7">
                    {{ __('Last 7 Days') }}
                </button>
                <button type="button" class="btn-period px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700" data-days="30">
                    {{ __('Last 30 Days') }}
                </button>
                <button type="button" class="btn-period px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700" data-days="90">
                    {{ __('Last 90 Days') }}
                </button>
            </div>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span>{{ __('Last updated:') }}</span>
            <span id="last-updated">{{ __('Loading...') }}</span>
        </div>
    </div>

    {{-- Overview Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'total-users',
            'title' => __('Total Users'),
            'icon' => 'users',
            'color' => 'blue'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'page-views',
            'title' => __('Page Views'),
            'icon' => 'eye',
            'color' => 'green'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'avg-session',
            'title' => __('Avg. Session Duration'),
            'icon' => 'clock',
            'color' => 'purple'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'bounce-rate',
            'title' => __('Bounce Rate'),
            'icon' => 'trending-down',
            'color' => 'orange'
        ])
    </div>

    {{-- Additional Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'new-users',
            'title' => __('New Users'),
            'icon' => 'users',
            'color' => 'indigo'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'sessions',
            'title' => __('Sessions'),
            'icon' => 'activity',
            'color' => 'cyan'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'engagement-rate',
            'title' => __('Engagement Rate'),
            'icon' => 'activity',
            'color' => 'pink'
        ])
        @include('backend.pages.analytics.partials.stat-card', [
            'id' => 'sessions-per-user',
            'title' => __('Sessions/User'),
            'icon' => 'users',
            'color' => 'teal'
        ])
    </div>

    {{-- Users Trend Chart --}}
    <div class="mb-6 rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                {{ __('Users & Sessions Over Time') }}
            </h3>
        </div>
        <div class="p-5">
            <div id="users-trend-chart" style="min-height: 350px;"></div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Top Pages --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Top Pages') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="top-pages-container">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Events --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Top Events') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="top-events-chart" style="min-height: 350px;"></div>
            </div>
        </div>

    </div>

    {{-- Three Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        {{-- Traffic Sources --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Traffic Sources') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="traffic-sources-chart" style="min-height: 300px;"></div>
            </div>
        </div>

        {{-- Top Countries --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Top Countries') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="geography-container">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Device Categories --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Devices') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="devices-chart" style="min-height: 300px;"></div>
            </div>
        </div>

    </div>

    {{-- Two Column Layout - Browsers and OS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Browsers --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Top Browsers') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="browsers-container">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Operating Systems --}}
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('Operating Systems') }}
                </h3>
            </div>
            <div class="p-5">
                <div id="os-container">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Landing Pages --}}
    <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                {{ __('Top Landing Pages') }}
            </h3>
        </div>
        <div class="p-5">
            <div id="landing-pages-container">
                <div class="text-center py-8 text-gray-500">
                    <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Loading...') }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('backend.pages.analytics.partials.scripts')
    @endpush
</x-layouts.backend-layout>
