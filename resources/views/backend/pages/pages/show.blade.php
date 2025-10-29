<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(\App\Enums\Hooks\PageFilterHook::PAGE_SHOW_AFTER_BREADCRUMBS, '', \App\Models\Page::class) !!}

    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">

            {{-- Header --}}
            <div
                class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white/90">{{ __('Page Details') }}</h3>
                <div class="flex gap-2">
                    @can('update', $page)
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn-primary flex items-center">
                            <iconify-icon icon="lucide:pencil" class="mr-2"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('admin.pages.index') }}" class="btn-default flex items-center">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="px-5 py-4 sm:px-6 sm:py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">

                    {{-- Section --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:folder" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Section') }}:</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            {{ $page->section ?? '—' }}
                            @if($page->is_custom_section ?? false)
                                <span class="text-xs text-blue-600 dark:text-blue-400 ml-1">({{ __('Custom') }})</span>
                            @endif
                        </span>
                    </div>

                    {{-- Slug --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:link" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Slug') }}:</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            /{{ $page->slug ?? '—' }}
                        </span>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:tag" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Status') }}:</span>
                        <span
                            class="ml-auto px-2 py-1 text-xs rounded {{ function_exists('get_page_status_class') ? get_page_status_class($page->status) : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white' }}">
                            @if($page->status == 'published')
                                {{ __('Published') }}
                            @elseif($page->status == 'draft')
                                {{ __('Draft') }}
                            @elseif($page->status == 'archived')
                                {{ __('Archived') }}
                            @else
                                {{ __('Unknown') }}
                            @endif
                        </span>
                    </div>

                    {{-- Navigation Visibility --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:navigation" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Show in Navigation') }}:</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            @if($page->show_in_nav ?? false)
                                <span class="text-green-600 dark:text-green-400">{{ __('Yes') }}</span>
                            @else
                                <span class="text-gray-500">{{ __('No') }}</span>
                            @endif
                        </span>
                    </div>

                    {{-- Footer Visibility --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:footprints" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Show in Footer') }}:</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            @if($page->show_in_footer ?? false)
                                <span class="text-green-600 dark:text-green-400">{{ __('Yes') }}</span>
                            @else
                                <span class="text-gray-500">{{ __('No') }}</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Short Info Panel --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Section') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">{{ $page->section ?? '—' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Custom Section') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ $page->is_custom_section ? __('Yes') : __('No') }}
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Navigation') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ $page->show_in_nav ? __('Visible') : __('Hidden') }}
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Footer') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ $page->show_in_footer ? __('Visible') : __('Hidden') }}
                        </div>
                    </div>
                </div>

                {{-- SEO Information --}}
                @if($page->meta_title || $page->meta_description)
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('SEO Information') }}</h4>
                        <div class="space-y-3">
                            @if($page->meta_title)
                                <div>
                                    <span class="font-medium text-sm text-gray-600 dark:text-gray-400">{{ __('Meta Title:') }}</span>
                                    <p class="text-sm text-gray-800 dark:text-white/90 mt-1">{{ $page->meta_title }}</p>
                                </div>
                            @endif
                            @if($page->meta_description)
                                <div>
                                    <span class="font-medium text-sm text-gray-600 dark:text-gray-400">{{ __('Meta Description:') }}</span>
                                    <p class="text-sm text-gray-800 dark:text-white/90 mt-1">{{ $page->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Content --}}
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('Content') }}</h4>
                    <div class="prose max-w-none dark:prose-invert text-sm">
                        {!! $page->content ?? '<p>—</p>' !!}
                    </div>
                </div>

                {{-- Page URL Preview --}}
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('Page') }} URL:</h4>
                    <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                        <iconify-icon icon="lucide:external-link" class="text-gray-500"></iconify-icon>
                        <a href="{{ url('/pages/' . $page->slug) }}" target="_blank" 
                           class="text-primary hover:underline text-sm break-all">
                            {{ url('/pages/' . $page->slug) }}
                        </a>
                    </div>
                </div>

                {{-- Created / Updated --}}
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                    <div>{{ __('Created At') }}: {{ $page->created_at->format('M d, Y h:i A') }}</div>
                    @if ($page->created_at != $page->updated_at)
                        <div>{{ __('Updated At') }} : {{ $page->updated_at->format('M d, Y h:i A') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(\App\Enums\Hooks\PageFilterHook::PAGE_SHOW_AFTER_CONTENT, '', $page) !!}

    @push('styles')
        <style>
            /* Make images inside the content smaller */
            .prose img {
                max-width: 300px;
                height: auto;
                border-radius: 0.375rem;
            }
        </style>
    @endpush
</x-layouts.backend-layout>