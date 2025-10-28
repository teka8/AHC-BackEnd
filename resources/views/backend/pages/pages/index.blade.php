@php
use App\Enums\Hooks\PageFilterHook;
@endphp

<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('status'))
                    <span class="badge">{{ ucfirst(request('status')) }}</span>
                @endif
                @if (request('section'))
                    <span class="badge">{{ __('Section: :section', ['section' => request('section')]) }}</span>
                @endif
                @if (request('tag'))
                    <span class="badge">{{ __('Tag: :tag', ['tag' => request('tag')]) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>

    <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-6 md:gap-6">
            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:files" class="text-2xl text-blue-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Total Pages') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:check-circle" class="text-2xl text-green-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Published') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:edit" class="text-2xl text-yellow-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Draft') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:archive" class="text-2xl text-red-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Archived') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['archived'] }}</p>
                    </div>
                </div>
            </div>

            

        </div>

    {!! Hook::applyFilters(PageFilterHook::PAGE_AFTER_BREADCRUMBS, '', App\Models\Page::class) !!}

    @livewire('datatable.page-datatable', ['lazy' => true])

    {!! Hook::applyFilters(PageFilterHook::PAGE_AFTER_TABLE, '', App\Models\Page::class) !!}
</x-layouts.backend-layout>