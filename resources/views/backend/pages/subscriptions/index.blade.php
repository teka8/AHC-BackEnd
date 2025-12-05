<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="title">
        <div class="flex items-center justify-between">
            <span>{{ __('Subscribers') }}</span>
            <a href="{{ route('admin.subscriptions.export') }}" 
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <iconify-icon icon="lucide:download" width="16" height="16"></iconify-icon>
                {{ __('Export to Excel') }}
            </a>
        </div>
    </x-slot>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Total subscribers') }}</p>
            <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Active subscribers') }}</p>
            <p class="mt-3 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Unsubscribed') }}</p>
            <p class="mt-3 text-2xl font-semibold text-rose-600 dark:text-rose-400">{{ number_format($stats['unsubscribed']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:col-span-2 xl:col-span-3">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Interest breakdown') }}</p>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('News') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['wants_news']) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Events') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['wants_events']) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Announcements') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['wants_announcements']) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Scholarships') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['wants_scholarships']) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-8">
        @livewire('datatable.email-subscription-datatable', ['lazy' => true])
    </div>
</x-layouts.backend-layout>
