<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="rounded-md border border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20 p-8 text-center">
        <div class="flex justify-center mb-4">
            <svg class="h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
            {{ __('Google Analytics Not Configured') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-2xl mx-auto">
            {{ __('Please configure Google Analytics settings to start tracking your frontend website and view analytics dashboard. You need to set up a GA4 property and upload a service account JSON key.') }}
        </p>
        
        <div class="bg-white dark:bg-gray-900/50 rounded-lg p-6 max-w-2xl mx-auto mb-6 text-left">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('Quick Setup Steps:') }}</h4>
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <li>{{ __('Create a Google Analytics 4 property') }}</li>
                <li>{{ __('Get your Measurement ID (G-XXXXXXXXXX)') }}</li>
                <li>{{ __('Create a service account in Google Cloud Console') }}</li>
                <li>{{ __('Download the service account JSON key') }}</li>
                <li>{{ __('Upload the key in Settings → Integrations') }}</li>
            </ol>
        </div>

        <a href="{{ route('admin.settings.index', ['tab' => 'integrations']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            {{ __('Go to Settings') }}
        </a>
    </div>
</x-layouts.backend-layout>
