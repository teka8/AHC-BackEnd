<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
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
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
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
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Newsletters') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['wants_newsletters']) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-8">
        @livewire('datatable.email-subscription-datatable', ['lazy' => true])
    </div>
</x-layouts.backend-layout>
