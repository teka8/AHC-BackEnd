<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Total messages') }}</p>
            <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('New messages') }}</p>
            <p class="mt-3 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ number_format($stats['new']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Read') }}</p>
            <p class="mt-3 text-2xl font-semibold text-gray-600 dark:text-gray-400">{{ number_format($stats['read']) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Replied') }}</p>
            <p class="mt-3 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['replied']) }}</p>
        </div>
    </div>

    <div class="mt-8">
        @livewire('datatable.contact-message-datatable', ['lazy' => true])
    </div>
</x-layouts.backend-layout>
